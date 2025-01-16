<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use InvalidArgumentException;
use Throwable;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\concerns\ZugferdMailParsesPlaceholders;
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
    use ZugferdMailParsesPlaceholders;

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
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType): void
    {
        try {
            $copyToFolder = $this->parsePlaceholdersByZugferdDocumentReader($document, $this->getCopyToFolder());
            $this->addLogMessageToMessageBag(sprintf('Copying mail to %s', $copyToFolder));
            $message->copy($copyToFolder);
            $this->addLogMessageToMessageBag(sprintf('Successfully copied mail to %s', $copyToFolder));
        } catch (Throwable $throwable) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to copy mail: %s', $throwable->getMessage()));
            throw $throwable;
        }
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
        if ($copyToFolder === '' || $copyToFolder === '0') {
            throw new InvalidArgumentException("The destination folder must not be empty");
        }

        $this->copyToFolder = $copyToFolder;

        return $this;
    }
}
