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
    /**
     * Default filename pattern
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
    protected $fileNamePattern = "";

    /**
     * Constructor
     *
     * @param string $filePath
     * @param string $filenamePattern
     */
    public function __construct(string $filePath, string $filenamePattern)
    {
        $this->setFilePath($filePath);
        $this->setFileNamePattern($filenamePattern);
    }

    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        $finalFilename = $this->buildFileNameFromPattern($document, $attachment);

        try {
            $this->addLogMessageToMessageBag(sprintf('Saving attachment to %s%s', $this->getFilePath(), $finalFilename));
            $attachment->save($this->getFilePath(), $finalFilename);
            $this->addLogMessageToMessageBag(sprintf('Successfully saved attachment to %s%s', $this->getFilePath(), $finalFilename));
        } catch (Throwable $e) {
            $this->addLogMessageToMessageBag(sprintf('Failed to save attachment to %s%s: %s', $this->getFilePath(), $finalFilename, $e->getMessage()));
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
    public function getFileNamePattern()
    {
        return $this->fileNamePattern;
    }

    /**
     * Sets the different file name to which the attachment (the invoice document) is stored
     *
     * @param  string $filenamePattern
     * @return ZugferdMailHandlerSaveToFile
     */
    public function setFileNamePattern(string $filenamePattern): ZugferdMailHandlerSaveToFile
    {
        if ($filenamePattern === "") {
            $filenamePattern = static::DEFAULTFILENAMEPATTEN;
        }

        $this->fileNamePattern = $filenamePattern;

        return $this;
    }

    /**
     * Build a filename from the given filename pattern
     *
     * @param  ZugferdDocumentReader $document
     * @param  Attachment            $attachment
     * @return string
     */
    private function buildFileNameFromPattern(ZugferdDocumentReader $document, Attachment $attachment): string
    {
        $document->getDocumentInformation($documentNo, $documentTypeCode, $documentDate, $invoiceCurrency, $taxCurrency, $documentName, $documentLanguage, $effectiveSpecifiedPeriod);
        $document->getDocumentSeller($documentSellerName, $documentSellerIds, $documentSellerDescription);
        $document->getDocumentSellerAddress($documentSellerLineOne, $documentSellerLineTwo, $documentSellerLineThree, $documentSellerPostCode, $documentSellerCity, $documentSellerCountry, $documentSellerSubDivision);

        $mappingTable = [];

        $funcAddToMappingTable = function (array &$mappingTable, ?string $name, ?string $value) {
            if (empty($name) || empty($value)) {
                return;
            }
            $mappingTable[$name] = $value;
        };

        $funcAddToMappingTable($mappingTable, "documentno", $documentNo);
        $funcAddToMappingTable($mappingTable, "documenttypecode", $documentTypeCode);
        $funcAddToMappingTable($mappingTable, "documentdateymd", $documentDate ? $documentDate->format("Ymd") : null);
        $funcAddToMappingTable($mappingTable, "documentdateymd2", $documentDate ? $documentDate->format("Y-m-d") : null);
        $funcAddToMappingTable($mappingTable, "documentname", $documentName);
        $funcAddToMappingTable($mappingTable, "documentlanguage", $documentLanguage);
        $funcAddToMappingTable($mappingTable, "documentinvoicecurrency", $invoiceCurrency);
        $funcAddToMappingTable($mappingTable, "documenttaxecurrency", $taxCurrency);
        $funcAddToMappingTable($mappingTable, "documentspecifiedperiod", $effectiveSpecifiedPeriod ? $effectiveSpecifiedPeriod->format("Ymd") : null);

        $funcAddToMappingTable($mappingTable, "documentsellerid0", $documentSellerIds[0] ?? "");
        $funcAddToMappingTable($mappingTable, "documentsellerid1", $documentSellerIds[1] ?? "");
        $funcAddToMappingTable($mappingTable, "documentsellername", $documentSellerName);
        $funcAddToMappingTable($mappingTable, "documentsellerdescription", $documentSellerDescription);
        $funcAddToMappingTable($mappingTable, "documentselleraddrline1", $documentSellerLineOne);
        $funcAddToMappingTable($mappingTable, "documentselleraddrline2", $documentSellerLineTwo);
        $funcAddToMappingTable($mappingTable, "documentselleraddrline3", $documentSellerLineThree);
        $funcAddToMappingTable($mappingTable, "documentsellerpostcode", $documentSellerPostCode);
        $funcAddToMappingTable($mappingTable, "documentsellercity", $documentSellerCity);
        $funcAddToMappingTable($mappingTable, "documentsellercountry", $documentSellerCountry);
        $funcAddToMappingTable($mappingTable, "documentsellersubvi0", $documentSellerSubDivision[0] ?? "");
        $funcAddToMappingTable($mappingTable, "documentsellersubvi1", $documentSellerSubDivision[1] ?? "");

        $funcAddToMappingTable($mappingTable, "guid", sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
        $funcAddToMappingTable($mappingTable, "hashsha256", hash('sha256', $document->serializeAsXml()));
        $funcAddToMappingTable($mappingTable, "hashsha512", hash('sha512', $document->serializeAsXml()));

        $fileExtension = MimeDb::singleton()->findFirstFileExtensionByMimeType($attachment->getMimeType());

        $parsedFilename = preg_replace_callback(
            '/\{(\w+)\}/',
            function ($placeholderMatch) use ($mappingTable) {
                $placeHolder = $placeholderMatch[1];
                $placeHolderValhe = isset($mappingTable[$placeHolder]) ? $mappingTable[$placeHolder] : "";
                return $placeHolderValhe;
            },
            $this->fileNamePattern
        );

        $parsedFilename = preg_replace('/_+/', '_', $parsedFilename);

        $parsedFilename = $fileExtension ? FileUtils::combineFilenameWithFileextension($parsedFilename, $fileExtension) : $parsedFilename;

        return $parsedFilename;
    }
}
