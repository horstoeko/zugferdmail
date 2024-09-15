<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Attachment;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use InvalidArgumentException;

/**
 * Class representing a handler which saves the attachment (the invoice document)
 * to a file
 *
 * @category ZugferdMailReader
 * @package  ZugferdMailReader
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerSaveToFile implements ZugferdMailHandlerInterface
{
    /**
     * The path to store the attachment (the invoice document)
     *
     * @var string
     */
    protected $filePath = "";

    /**
     * The different filename to which the file is stored
     *
     * @var string|null
     */
    protected $fileName = null;

    /**
     * Constructor
     *
     * @param string      $filePath
     * @param string|null $fileName
     */
    public function __construct(string $filePath, ?string $fileName)
    {
        $this->setFilePath($filePath);
        $this->setFileName($fileName);
    }

    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        $attachment->save($this->getFilePath(), $this->getFileName());
    }

    /**
     * Returns the file path to which the attachment (the invoice document) is stored
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Sets the file path to which the attachment (the invoice document) is stored
     *
     * @param  string $filePath
     * @return ZugferdMailHandlerSaveToFile
     */
    public function setFilePath($filePath): ZugferdMailHandlerSaveToFile
    {
        if (empty($filePath)) {
            throw new InvalidArgumentException("The file path must not be empty");
        }

        $filePath = rtrim($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($filePath)) {
            throw new InvalidArgumentException(sprintf("The file path %s does not exist", $filePath));
        }

        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Returns the different file name to which the attachment (the invoice document) is stored
     *
     * @return string|null
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets the different file name to which the attachment (the invoice document) is stored
     *
     * @param  string|null $filename
     * @return ZugferdMailHandlerSaveToFile
     */
    public function setFileName(?string $filename): ZugferdMailHandlerSaveToFile
    {
        if (!is_null($filename)) {
            if (empty($filename)) {
                throw new InvalidArgumentException("The file name must not be empty");
            }
        }

        $this->fileName = $filename;

        return $this;
    }
}
