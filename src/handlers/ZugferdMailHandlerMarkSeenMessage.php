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
class ZugferdMailHandlerMarkSeenMessage extends ZugferdMailHandlerAbstract
{
    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType): void
    {
        try {
            $this->addLogMessageToMessageBag(sprintf('Marking mail %s as seen', $message->getUid()));
            $message->setFlag('Seen');
            $this->addLogMessageToMessageBag(sprintf('Successfully marked mail %s as seen', $message->getUid()));
        } catch (Throwable $throwable) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to mark mail as seen: %s', $throwable->getMessage()));
            throw $throwable;
        }
    }
}
