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
use horstoeko\zugferdmail\concerns\ZugferdMailStringHelper;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\consts\ZugferdMailReaderRecognitionType;
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
    use ZugferdMailSendsMessagesToMessageBag,
        ZugferdMailReceivesMessagesFromMessageBag,
        ZugferdMailClearsMessageBag,
        ZugferdMailRaisesExceptions,
        ZugferdMailStringHelper;

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
     * @param ZugferdMailAccount $account
     * @return void
     * @throws MaskNotFoundException
     * @throws ConnectionFailedException
     * @throws FolderFetchingException
     * @throws RuntimeException
     */
    protected function checkSingleAccount(ZugferdMailAccount $account): void
    {
        $this->clientManager->account($account->getIdentifier())->connect()->getFolders()->each(
            function (Folder $folder) use ($account) {
                $this->checkSingleAccountFolder($account, $folder);
            }
        );
    }

    /**
     * Checks a single mail account folder
     *
     * @param ZugferdMailAccount $account
     * @param Folder $folder
     * @return void
     * @throws ConnectionFailedException
     * @throws RuntimeException
     * @throws GetMessagesFailedException
     */
    protected function checkSingleAccountFolder(ZugferdMailAccount $account, Folder $folder): void
    {
        if (in_array($folder->full_name, $account->getFoldersTowatch())) {
            $folder->messages()->all()->get()->each(
                function (Message $message) use ($account, $folder) {
                    $this->checkSingleMessage($account, $folder, $message);
                }
            );
        }

        collect($folder->children)->each(
            function (Folder $subFolder) use ($account) {
                $this->checkSingleAccountFolder($account, $subFolder);
            }
        );
    }

    /**
     * Checks a single mail
     *
     * @param ZugferdMailAccount $account
     * @param Folder $folder
     * @param Message $message
     * @return void
     */
    protected function checkSingleMessage(ZugferdMailAccount $account, Folder $folder, Message $message): void
    {
        $message->attachments()->each(
            function (Attachment $attachment) use ($account, $folder, $message) {
                $this->checkSingleMessageAttachment($account, $folder, $message, $attachment);
            }
        );
    }

    /**
     * Checks a single mail attachment
     *
     * @param ZugferdMailAccount $account
     * @param Folder $folder
     * @param Message $message
     * @param Attachment $attachment
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

        $this->addLogMessageToMessageBag(sprintf("Checking attachment of mail %s, Subject: %s, Sender: %s", $message->getUid(), $this->zfMailTruncateString($message->getSubject(), 20), $this->zfMailTruncateString($message->getFrom(), 20)));

        if (!in_array($attachment->getMimeType(), $account->getMimeTypesToWatch())) {
            $this->addLogMessageToMessageBag(sprintf("Mimetype %s does not match", $attachment->getMimeType()));
            $this->addLogMessageToMessageBag('');
            return;
        }

        $this->addLogMessageToMessageBag(sprintf("Valid Mimetype %s found", $attachment->getMimeType()));

        $document = null;

        if (is_null($document)) {
            try {
                $this->addLogMessageToMessageBag('Checking for ZUGFeRD compatible PDF', $messageAdditionalData);
                $document = ZugferdDocumentPdfReader::readAndGuessFromContent($attachment->getContent());
                $this->raiseRuntimeExceptionIf(is_null($document), "No document returned");
                $this->addSuccessMessageToMessageBag('Mail contains a ZUGFeRD compatible PDF', $messageAdditionalData);
                $this->validateDocument($document, $messageAdditionalData);
                $this->triggerHandlers($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_PDF_CII);
            } catch (Throwable $e) {
                $this->addWarningMessageToMessageBag(sprintf("No ZUGFeRD compatible PDF found (%s)", $e->getMessage()), $messageAdditionalData);
            }
        }

        if (is_null($document)) {
            try {
                $this->addLogMessageToMessageBag('Checking for ZUGFeRD compatible XML', $messageAdditionalData);
                $document = ZugferdDocumentReader::readAndGuessFromContent($attachment->getContent());
                $this->addSuccessMessageToMessageBag('Mail contains a ZUGFeRD compatible XML', $messageAdditionalData);
                $this->validateDocument($document, $messageAdditionalData);
                $this->triggerHandlers($account, $folder, $message, $attachment, $document, ZugferdMailReaderRecognitionType::ZFMAIL_RECOGNITION_TYPE_XML_CII);
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
                } catch (Throwable $e) {
                    $this->addWarningMessageToMessageBag(sprintf("No UBL compatible XML found (%s)", $e->getMessage()), $messageAdditionalData);
                }
            } else {
                $this->addWarningMessageToMessageBag("UBL support disabled", $messageAdditionalData);
            }
        }

        $this->addLogMessageToMessageBag('');
    }

    /**
     * Validates a document
     *
     * @param ZugferdDocument $document
     * @param array $messageAdditionalData
     * @return void
     */
    private function validateDocument(ZugferdDocument $document, array $messageAdditionalData): void
    {
        if ($this->config->getSymfonyValidationEnabled()) {
            $validator = new ZugferdDocumentValidator($document);
            $this->raiseRuntimeExceptionIf(count($validator->validateDocument()) != 0, "Validation against Symfony-Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against Symfony validator', $messageAdditionalData);
        } else {
            $this->addWarningMessageToMessageBag('The document was not validated against Symfony validator (Disabled)', $messageAdditionalData);
        }
        if ($this->config->getXsdValidationEnabled()) {
            $validator = new ZugferdXsdValidator($document);
            $this->raiseRuntimeExceptionIf($validator->validate()->hasValidationErrors(), "Validation against XSD-Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against XSD scheme', $messageAdditionalData);
        } else {
            $this->addWarningMessageToMessageBag('The document was not validated against XSD scheme (Disabled)', $messageAdditionalData);
        }
        if ($this->config->getKositValidationEnabled()) {
            $validator = new ZugferdKositValidator($document);
            $this->raiseRuntimeExceptionIf($validator->validate()->hasValidationErrors(), "Validation against KosIT Validation failed");
            $this->addSuccessMessageToMessageBag('The document was successfully validated against the KosIT validator', $messageAdditionalData);
        } else {
            $this->addWarningMessageToMessageBag('The document was not validated against the KosIT validator (Disabled)', $messageAdditionalData);
        }
    }

    /**
     * Internal trigger when attachment was found
     *
     * @param ZugferdMailAccount $account
     * @param Folder $folder
     * @param Message $message
     * @param Attachment $attachment
     * @param ZugferdDocumentReader $document
     * @param int $recognitionType
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
}
