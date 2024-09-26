<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use InvalidArgumentException;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

/**
 * Class representing a handler that copies a message to another folder
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerCopyMessage extends ZugferdMailHandlerAbstract
{
    /**
     * The folder to copy the message to
     *
     * @var string
     */
    protected $copyToFolder = "";

    /**
     * Constructor
     *
     * @param string $copyToFolder
     */
    public function __construct(string $copyToFolder)
    {
        $this->setCopyToFolder($copyToFolder);
    }

    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        $message->copy($this->getCopyToFolder());
    }

    /**
     * Returns the folder name to which the message should be copied
     *
     * @return string
     */
    public function getCopyToFolder(): string
    {
        return $this->copyToFolder;
    }

    /**
     * Sets the folder name to which the message should be copied
     *
     * @param  string $copyToFolder
     * @return ZugferdMailHandlerCopyMessage
     */
    public function setCopyToFolder(string $copyToFolder): ZugferdMailHandlerCopyMessage
    {
        if (empty($copyToFolder)) {
            throw new InvalidArgumentException("The destination folder must not be empty");
        }

        $this->copyToFolder = $copyToFolder;

        return $this;
    }
}
