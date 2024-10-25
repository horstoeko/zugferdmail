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
 * Class representing a handler that marks a message as "seen"
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerMarkUnseenMessage extends ZugferdMailHandlerAbstract
{
    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        try {
            $this->addLogMessageToMessageBag(sprintf('Marking mail %s as unseen', $message->getUid()));
            $message->removeFlag('Seen');
            $this->addLogMessageToMessageBag(sprintf('Successfully marked mail %s as unseen', $message->getUid()));
        } catch (Throwable $e) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to mark mail as unseen: %s', $e->getMessage()));
            throw $e;
        }
    }
}
