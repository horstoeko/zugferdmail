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
use InvalidArgumentException;
use Webklex\PHPIMAP\Attachment;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;

/**
 * Class representing a handler that moves a message to another folder
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerMoveMessage extends ZugferdMailHandlerAbstract
{
    /**
     * The folder to move the message to
     *
     * @var string
     */
    protected $moveToFolder = "";

    /**
     * Constructor
     *
     * @param string $moveToFolder
     */
    public function __construct(string $moveToFolder)
    {
        $this->setMoveToFolder($moveToFolder);
    }

    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        try {
            $this->addLogMessageToMessageBag(sprintf('Moving mail to %s', $this->getMoveToFolder()));
            $message->move($this->getMoveToFolder());
            $this->addLogMessageToMessageBag(sprintf('Successfully moved mail to %s', $this->getMoveToFolder()));
        } catch (Throwable $e) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to move mail to %s: %s', $this->getMoveToFolder(), $e->getMessage()));
            throw $e;
        }
    }

    /**
     * Returns the folder name to which the message should be moved
     *
     * @return string
     */
    public function getMoveToFolder(): string
    {
        return $this->moveToFolder;
    }

    /**
     * Sets the folder name to which the message should be moved
     *
     * @param  string $moveToFolder
     * @return ZugferdMailHandlerMoveMessage
     */
    public function setMoveToFolder(string $moveToFolder): ZugferdMailHandlerMoveMessage
    {
        if (empty($moveToFolder)) {
            throw new InvalidArgumentException("The destination folder must not be empty");
        }

        $this->moveToFolder = $moveToFolder;

        return $this;
    }
}
