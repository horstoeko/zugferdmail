<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail;

use Throwable;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\ClientManager;
use horstoeko\zugferd\ZugferdDocument;
use horstoeko\zugferd\ZugferdDocumentPdfReader;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferd\ZugferdDocumentValidator;
use horstoeko\zugferd\ZugferdKositValidator;
use horstoeko\zugferd\ZugferdXsdValidator;
use horstoeko\zugferdmail\concerns\ZugferdMailClearsMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailRaisesExceptions;
use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailSendsMessagesToMessageBag;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\consts\ZugferdMailReaderRecognitionType;
use horstoeko\zugferdmail\helpers\ZugferdMailStringHelper;
use horstoeko\zugferdublbridge\XmlConverterUblToCii;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

/**
 * Class representing the mail reader
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailReader
{
    use ZugferdMailSendsMessagesToMessageBag;
    use ZugferdMailReceivesMessagesFromMessageBag;
    use ZugferdMailClearsMessageBag;
    use ZugferdMailRaisesExceptions;

    /**
     * The config
     *
     * @var ZugferdMailConfig
     */
    protected $config;

    /**
     * The client manager
     *
     * @var ClientManager
     */
    protected $clientManager;

    /**
     * Constructor
     *
     * @param ZugferdMailConfig $config
     */
    public function __construct(ZugferdMailConfig $config)
    {
        $this->config = $config;
        $this->clientManager = $config->makeClientManager();
    }

    /**
     * List available folders for each defined account
     *
     * @return array
     * @throws MaskNotFoundException
     * @throws ConnectionFailedException
     * @throws FolderFetchingException
     * @throws RuntimeException
     */
    public function getAllAvailableRootFolders(): array
    {
        $result = [];

        foreach ($this->config->getAccounts() as $account) {
            $result[] = [
                "account" => $account->getIdentifier(),
                "folders" => $this->clientManager->account($account->getIdentifier())->connect()->getFolders()->toArray(),
            ];
        }

        return $result;
    }

    /**
     * Check all defined accounts
     *
     * @return ZugferdMailReader
     * @throws MaskNotFoundException
     * @throws ConnectionFailedException
     * @throws FolderFetchingException
     * @throws RuntimeException
     */
    public function checkAllAccounts(): ZugferdMailReader
    {
        $this->clearMessageBag();

        foreach ($this->config->getAccounts() as $account) {
            $this->checkSingleAccount($account);
        }

        return $this;
    }

    /**
     * Checks a single account
     *
     * @param  ZugferdMailAccount $account
     * @return void
     * @throws MaskNotFoundException
     * @throws ConnectionFailedException
     * @throws FolderFetchingException
     * @throws RuntimeException
     */
    protected function checkSingleAccount(ZugferdMailAccount $account): void
    {
        $this->clientManager->account($account->getIdentifier())->connect()->getFolders()->each(
            function (Folder $folder) use ($account): void {
                $this->checkSingleAccountFolder($account, $folder);
            }
        );
    }

    /**
     * Checks a single mail account folder
     *
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @return void
     * @throws ConnectionFailedException
     * @throws RuntimeException
     * @throws GetMessagesFailedException
     */
    protected function checkSingleAccountFolder(ZugferdMailAccount $account, Folder $folder): void
    {
        if (in_array($folder->full_name, $account->getFoldersTowatch())) {
            $folder->messages()->all()->when(
                $account->getUnseenMessagesOnlyEnabled(),
                function ($query) {
                    return $query->unseen();
                }
            )->get()->each(
                function (Message $message) use ($account, $folder): void {
                    $this->checkSingleMessage($account, $folder, $message);
                }
            );
        }

        collect($folder->children)->each(
            function (Folder $subFolder) use ($account): void {
                $this->checkSingleAccountFolder($account, $subFolder);
            }
        );
    }

    /**
     * Checks a single mail
     *
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @param  Message            $message
     * @return void
     */
    protected function checkSingleMessage(ZugferdMailAccount $account, Folder $folder, Message $message): void
    {
        $message->attachments()->each(
            function (Attachment $attachment) use ($account, $folder, $message): void {
                $this->checkSingleMessageAttachment($account, $folder, $message, $attachment);
            }
        );
    }

    /**
     * Checks a single mail attachment
     *
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @param  Message            $message
     * @param  Attachment         $attachment
     * @return void
     */
    protected function checkSingleMessageAttachment(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment): void
    {
        $messageAdditionalData = [
            "account" => $account,
            "folder" => $folder,
            "message" => $message,
            "attachment" => $attachment,
        ];

        $this->addLogMessageToMessageBag(sprintf("Checking attachment of mail %s, Subject: %s, Sender: %s", $message->getUid(), ZugferdMailStringHelper::truncateString($message->getSubject(), 20), ZugferdMailStringHelper::truncateString($message->getFrom(), 20)));

        if (!in_array($attachment->getMimeType(), $account->getMimeTypesToWatch())) {
            $this->addLogMessageToMessageBag(sprintf("Mimetype %s does not match", $attachment->getMimeType()));
            $this->addLogMessageToMessageBag('');
            return;
        }

        $this->addLogMessageToMessageBag(sprintf("Valid Mimetype %s found", $attachment->getMimeType()));

        $document = null;

        try {
            $this->addLogMessageToMessageBag('Checking for ZUGFeRD compatible PDF', $messageAdditionalData);
            $document = ZugferdDocumentPdfReader::readAndGuessFromContent($attachment->getContent());
            $this->addSuccessMessageToMessageBag('Mail contains a ZUGFeRD compatible PDF', $messageAdditionalData);
            $this->validateDocument($document, $messageAdditionalData);
            $this->triggerHandlers($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_PDF_CII);
            $this->triggerCallbacks($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_PDF_CII);
        } catch (Throwable $e) {
            $this->addWarningMessageToMessageBag(sprintf("No ZUGFeRD compatible PDF found (%s)", $e->getMessage()), $messageAdditionalData);
        }

        if (is_null($document)) {
            try {
                $this->addLogMessageToMessageBag('Checking for ZUGFeRD compatible XML', $messageAdditionalData);
                $document = ZugferdDocumentReader::readAndGuessFromContent($attachment->getContent());
                $this->addSuccessMessageToMessageBag('Mail contains a ZUGFeRD compatible XML', $messageAdditionalData);
                $this->validateDocument($document, $messageAdditionalData);
                $this->triggerHandlers($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_XML_CII);
                $this->triggerCallbacks($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_XML_CII);
            } catch (Throwable $e) {
                $this->addWarningMessageToMessageBag(sprintf("No ZUGFeRD compatible XML found (%s)", $e->getMessage()), $messageAdditionalData);
            }
        }

        if (is_null($document)) {
            if ($this->config->getUblSupportEnabled() === true) {
                try {
                    $this->addLogMessageToMessageBag('Checking for UBL compatible XML', $messageAdditionalData);
                    $document = ZugferdDocumentReader::readAndGuessFromContent(
                        XmlConverterUblToCii::fromString($attachment->getContent())->convert()->saveXmlString()
                    );
                    $this->addSuccessMessageToMessageBag('Mail contains a UBL compatible XML', $messageAdditionalData);
                    $this->validateDocument($document, $messageAdditionalData);
                    $this->triggerHandlers($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_XML_UBL);
                    $this->triggerCallbacks($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_XML_UBL);
                } catch (Throwable $e) {
                    $this->addWarningMessageToMessageBag(sprintf("No UBL compatible XML found (%s)", $e->getMessage()), $messageAdditionalData);
                }
            } else {
                $this->addLogSecondaryMessageToMessageBag("UBL support disabled", $messageAdditionalData);
            }
        }

        $this->addLogMessageToMessageBag('');
    }

    /**
     * Validates a document
     *
     * @param  ZugferdDocument $document
     * @param  array           $messageAdditionalData
     * @return void
     */
    private function validateDocument(ZugferdDocument $document, array $messageAdditionalData): void
    {
        if ($this->config->getSymfonyValidationEnabled()) {
            $validator = (new ZugferdDocumentValidator($document))->validateDocument();
            $this->raiseRuntimeExceptionIf($validator->count() != 0, "Validation against Symfony-Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against Symfony validator', $messageAdditionalData);
        } else {
            $this->addLogSecondaryMessageToMessageBag('The document was not validated against Symfony validator (Disabled)', $messageAdditionalData);
        }

        if ($this->config->getXsdValidationEnabled()) {
            $validator = (new ZugferdXsdValidator($document))->validate();
            $this->addMultipleErrorMessagesToMessageBagIf($validator->hasValidationErrors(), $validator->validationErrors());
            $this->raiseRuntimeExceptionIf($validator->hasValidationErrors(), "Validation against XSD-Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against XSD scheme', $messageAdditionalData);
        } else {
            $this->addLogSecondaryMessageToMessageBag('The document was not validated against XSD scheme (Disabled)', $messageAdditionalData);
        }

        if ($this->config->getKositValidationEnabled()) {
            $validator = (new ZugferdKositValidator($document))->validate();
            $this->addMultipleLogSecondaryMessagesToMessageBagIf($validator->hasNoValidationInformation(), $validator->getValidationInformation());
            $this->addMultipleWarningMessagesToMessageBagIf($validator->hasValidationWarnings(), $validator->getValidationWarnings());
            $this->addMultipleErrorMessagesToMessageBagIf($validator->hasValidationErrors(), $validator->getValidationErrors());
            $this->addMultipleErrorMessagesToMessageBagIf($validator->hasProcessErrors(), $validator->getProcessErrors());
            $this->raiseRuntimeExceptionIf($validator->hasValidationErrors(), "Validation against KosIT Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against the KosIT validator', $messageAdditionalData);
        } else {
            $this->addLogSecondaryMessageToMessageBag('The document was not validated against the KosIT validator (Disabled)', $messageAdditionalData);
        }
    }

    /**
     * Internal trigger a handler when attachment was found
     *
     * @param  ZugferdMailAccount    $account
     * @param  Folder                $folder
     * @param  Message               $message
     * @param  Attachment            $attachment
     * @param  ZugferdDocumentReader $document
     * @param  int                   $recognitionType
     * @return void
     */
    protected function triggerHandlers(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType): void
    {
        foreach ($account->getHandlers() as $handler) {
            try {
                $handler->handleDocument($account, $folder, $message, $attachment, $document, $recognitionType);
            } catch (Throwable $e) {
                $this->addThrowableToMessageBag($e);
            }
        }
    }

    /**
     * Internal trigger a callback when attachment was found
     *
     * @param  ZugferdMailAccount    $account
     * @param  Folder                $folder
     * @param  Message               $message
     * @param  Attachment            $attachment
     * @param  ZugferdDocumentReader $document
     * @param  int                   $recognitionType
     * @return void
     */
    protected function triggerCallbacks(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType): void
    {
        foreach ($account->getCallbacks() as $callback) {
            try {
                $returnValue = call_user_func($callback, $account, $folder, $message, $attachment, $document, $recognitionType);
                if ($returnValue === false) {
                    break;
                }
            } catch (Throwable $e) {
                $this->addThrowableToMessageBag($e);
            }
        }
    }
}
