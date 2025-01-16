<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\helpers;

use DateTimeInterface;
use horstoeko\zugferd\ZugferdDocumentReader;

/**
 * Class representing placeholder parser
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailPlaceholderHelper
{
    /**
     * Internal mapping table for mapping placeholders to their values
     *
     * @var array
     */
    private $mappingTable = [];

    /**
     * Constructor (hidden)
     */
    final protected function __construct()
    {
    }

    /**
     * Factory, init mapping table from an array
     *
     * @param  array $arr
     * @return ZugferdMailPlaceholderHelper
     */
    public static function fromArray(array $arr): ZugferdMailPlaceholderHelper
    {
        $placeHolderHelper = new static();

        foreach ($arr as $arrKey => $arrValue) {
            $placeHolderHelper->addPlaceholder($arrKey, $arrValue);
        }

        return $placeHolderHelper;
    }

    /**
     * Factory, init mapping table from ZugferdDocumentReader instance
     *
     * @param  ZugferdDocumentReader $document
     * @return ZugferdMailPlaceholderHelper
     */
    public static function fromZugferdDocumentReader(ZugferdDocumentReader $document): ZugferdMailPlaceholderHelper
    {
        $placeHolderHelper = new static();

        $document->getDocumentInformation($documentNo, $documentTypeCode, $documentDate, $invoiceCurrency, $taxCurrency, $documentName, $documentLanguage, $effectiveSpecifiedPeriod);
        $document->getDocumentSeller($documentSellerName, $documentSellerIds, $documentSellerDescription);
        $document->getDocumentSellerGlobalId($documentSellerGlobalIds);
        $document->getDocumentSellerAddress($documentSellerLineOne, $documentSellerLineTwo, $documentSellerLineThree, $documentSellerPostCode, $documentSellerCity, $documentSellerCountry, $documentSellerSubDivision);

        $placeHolderHelper
            ->addPlaceholder("documentno", $documentNo)
            ->addPlaceholder("documenttypecode", $documentTypeCode)
            ->addPlaceholder("documentdate", $documentDate)
            ->addPlaceholder("documentname", $documentName)
            ->addPlaceholder("documentlanguage", $documentLanguage)
            ->addPlaceholder("documentinvoicecurrency", $invoiceCurrency)
            ->addPlaceholder("documenttaxcurrency", $taxCurrency)
            ->addPlaceholder("documentspecifiedperiod", $effectiveSpecifiedPeriod)
            ->addPlaceholder("documentsellerid", $documentSellerIds)
            ->addPlaceholder("documentsellerglobalid", $documentSellerGlobalIds)
            ->addPlaceholder("documentsellername", $documentSellerName)
            ->addPlaceholder("documentsellerdescription", $documentSellerDescription)
            ->addPlaceholder("documentselleraddrline1", $documentSellerLineOne)
            ->addPlaceholder("documentselleraddrline2", $documentSellerLineTwo)
            ->addPlaceholder("documentselleraddrline3", $documentSellerLineThree)
            ->addPlaceholder("documentsellerpostcode", $documentSellerPostCode)
            ->addPlaceholder("documentsellercity", $documentSellerCity)
            ->addPlaceholder("documentsellercountry", $documentSellerCountry)
            ->addPlaceholder("documentsellersubdiv", $documentSellerSubDivision);

        return $placeHolderHelper;
    }

    /**
     * Add a placeholder with it's value to internal mapping table
     *
     * @param  string                              $placeholderName
     * @param  string|array|DateTimeInterface|null $placeHolderValue
     * @return ZugferdMailPlaceholderHelper
     */
    public function addPlaceholder(string $placeholderName, $placeHolderValue): ZugferdMailPlaceholderHelper
    {
        if (empty($placeholderName) || empty($placeHolderValue)) {
            return $this;
        }

        switch (true) {
            case is_string($placeHolderValue):
                $this->mappingTable[$placeholderName] = $placeHolderValue;
                break;
            case $placeHolderValue instanceof DateTimeInterface:
                $this->addPlaceholder(sprintf("%s_ymd", $placeholderName), $placeHolderValue->format("Ymd"));
                $this->addPlaceholder(sprintf("%s_y_m_d", $placeholderName), $placeHolderValue->format("Y-m-d"));
                break;
            case is_array($placeHolderValue):
                foreach ($placeHolderValue as $placeHolderValueKey => $placeHolderValueData) {
                    $this->addPlaceholder(sprintf("%s_%s", $placeholderName, $placeHolderValueKey), $placeHolderValueData);
                }

                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * Parses placeholders in $subject by the internal mapping table
     *
     * @param  string $subject
     * @return string
     */
    public function parseString(string $subject): string
    {
        return preg_replace_callback(
            '/\{(\w+)\}/',
            function ($placeholderMatch) {
                $placeHolderName = $placeholderMatch[1];
                $placeHolderValhe = $this->mappingTable[$placeHolderName] ?? "";
                return $placeHolderValhe;
            },
            $subject
        );
    }
}
