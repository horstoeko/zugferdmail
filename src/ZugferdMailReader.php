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
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferd\ZugferdDocumentPdfReader;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\config\ZugferdMailAccount;

/**
 * Class representing the mail reader
 *
 * @category ZugferdMailReader
 * @package  ZugferdMailReader
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailReader
{
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
        $this->clientManager = $this->config->getClientManager();
    }

    /**
     * Check all defined accounts
     *
     * @return ZugferdMailReader
     */
    public function checkAllAccounts(): ZugferdMailReader
    {
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
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @return void
     */
    protected function checkSingleAccountFolder(ZugferdMailAccount $account, Folder $folder): void
    {
        if (in_array($folder->full_name, $account->getFoldersTowatch())) {
            $folder->messages()->all()->get()->filter(
                function (Message $message) {
                    return $message->hasAttachments();
                }
            )->each(
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
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @param  Message            $message
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
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @param  Message            $message
     * @param  Attachment         $attachment
     * @return void
     */
    protected function checkSingleMessageAttachment(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment): void
    {
        if (!in_array($attachment->getMimeType(), $account->getMmimeTypesToWatch())) {
            return;
        }

        try {
            $document = ZugferdDocumentPdfReader::readAndGuessFromContent($attachment->getContent());
            $this->triggerHandlers($account, $folder, $message, $attachment, $document);
        } catch (Throwable $e) {
            try {
                $document = ZugferdDocumentReader::readAndGuessFromContent($attachment->getContent());
                $this->triggerHandlers($account, $folder, $message, $attachment, $document);
            } catch (Throwable $e) {
                // Do nothing
            }
        }
    }

    /**
     * Internal trigger when attachment was found
     *
     * @param  ZugferdMailAccount $account
     * @param  Folder             $folder
     * @param  Message            $message
     * @param  ZugferdDocument    $document
     * @return void
     */
    protected function triggerHandlers(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocument $document): void
    {
        foreach ($account->getHandlers() as $handler) {
            $handler->handleDocument($account, $folder, $message, $attachment, $document);
        }
    }
}
