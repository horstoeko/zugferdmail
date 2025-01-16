<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailSendsMessagesToMessageBag;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

abstract class ZugferdMailHandlerAbstract
{
    use ZugferdMailSendsMessagesToMessageBag,
        ZugferdMailReceivesMessagesFromMessageBag;

    /**
     * The method will be call when a document was found. This method can perform
     * anything you want
     *
     * @param  ZugferdMailAccount    $account
     * @param  Folder                $folder
     * @param  Message               $message
     * @param  Attachment            $attachment
     * @param  ZugferdDocumentReader $document
     * @param  integer               $recognitionType
     * @return void
     */
    abstract public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType);
}
