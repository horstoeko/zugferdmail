<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use Throwable;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Attachment;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;

/**
 * Class representing a handler that copies a message to another folder
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerDeleteMessage extends ZugferdMailHandlerAbstract
{
    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ?ZugferdDocumentReader $document, ?int $recognitionType)
    {
        try {
            $this->addLogMessageToMessageBag(sprintf('Deleting message with %s', $message->getUid()));
            $message->delete();
            $this->addLogMessageToMessageBag(sprintf('Successfully deleted message with %s', $message->getUid()));
        } catch (Throwable $e) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to delete message with id %s: %s', $message->getUid(), $e->getMessage()));
            throw $e;
        }
    }
}
