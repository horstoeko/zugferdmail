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
use horstoeko\mimedb\MimeDb;
use horstoeko\stringmanagement\FileUtils;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\concerns\ZugferdMailParsesPlaceholders;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

/**
 * Class representing a handler which saves the attachment (the invoice document)
 * to a file
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerSaveToFile extends ZugferdMailHandlerAbstract
{
    use ZugferdMailParsesPlaceholders;

    /**
     * Default filename
     */
    protected const DEFAULTFILENAMEPATTEN = "{documentno}_{documentsellername}";

    /**
     * The path to store the attachment (the invoice document)
     *
     * @var string
     */
    protected $filePath = "";

    /**
     * The different filename to which the file is stored
     *
     * @var string
     */
    protected $fileName = "";

    /**
     * Constructor
     *
     * @param string $filePath
     * @param string $filename
     */
    public function __construct(string $filePath, string $filename)
    {
        $this->setFilePath($filePath);
        $this->setFileName($filename);
    }

    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        try {
            $finalFilename = $this->buildFileNameFrom($document, $attachment);
            $this->addLogMessageToMessageBag(sprintf('Saving attachment to %s%s', $this->getFilePath(), $finalFilename));
            $attachment->save($this->getFilePath(), $finalFilename);
            $this->addLogMessageToMessageBag(sprintf('Successfully saved attachment to %s%s', $this->getFilePath(), $finalFilename));
        } catch (Throwable $e) {
            $this->addErrorMessageToMessageBag(sprintf('Failed to save attachment: %s', $e->getMessage()));
            throw $e;
        }
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
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets the different file name to which the attachment (the invoice document) is stored
     *
     * @param  string $filename
     * @return ZugferdMailHandlerSaveToFile
     */
    public function setFileName(string $filename): ZugferdMailHandlerSaveToFile
    {
        if ($filename === "") {
            $filename = static::DEFAULTFILENAMEPATTEN;
        }

        $this->fileName = $filename;

        return $this;
    }

    /**
     * Build a filename from the given filename
     *
     * @param  ZugferdDocumentReader $document
     * @param  Attachment            $attachment
     * @return string
     */
    private function buildFileNameFrom(ZugferdDocumentReader $document, Attachment $attachment): string
    {
        $parsedFilename = $this->parsePlaceholdersByZugferdDocumentReader($document, $this->getFileName());

        $fileExtension = $attachment->getExtension();
        $fileExtension = $fileExtension ?? MimeDb::singleton()->findFirstFileExtensionByMimeType($attachment->getMimeType());
        $fileExtension = $fileExtension ?? "";

        $parsedFilename = preg_replace('/_+/', '_', $parsedFilename);

        $parsedFilename = $fileExtension ? FileUtils::combineFilenameWithFileextension($parsedFilename, $fileExtension) : $parsedFilename;

        return $parsedFilename;
    }
}
