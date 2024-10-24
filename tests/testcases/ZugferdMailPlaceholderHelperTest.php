<?php

namespace horstoeko\zugferdmail\tests\testcases;

use DateTime;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferdmail\helpers\ZugferdMailPlaceholderHelper;
use horstoeko\zugferdmail\tests\TestCase;

class ZugferdMailPlaceholderHelperTest extends TestCase
{
    public function testCreateFromEmptyArray(): void
    {
        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray([]);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertEmpty($mappingTable);
    }

    public function testCreateFromFilledArray(): void
    {
        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray(["test" => "A"]);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertNotEmpty($mappingTable);
        $this->assertArrayHasKey("test", $mappingTable);
        $this->assertEquals("A", $mappingTable["test"]);
    }

    public function testCreateFromFilledArrayWithNullValue(): void
    {
        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray(["test" => null]);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertEmpty($mappingTable);
        $this->assertArrayNotHasKey("test", $mappingTable);
    }

    public function testCreateFromFilledArrayWithNullAndNotNullValue(): void
    {
        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray(["test" => null, "test2" => "A"]);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertNotEmpty($mappingTable);
        $this->assertArrayNotHasKey("test", $mappingTable);
        $this->assertArrayHasKey("test2", $mappingTable);
        $this->assertEquals("A", $mappingTable["test2"]);
    }

    public function testCreateFromFilledArrayWithMixedValues(): void
    {
        $dateTime = new DateTime();
        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray(["test" => null, "test2" => "A", "test3" => $dateTime, "test4" => ["a" => "aa", "b" => "bb"]]);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertNotEmpty($mappingTable);
        $this->assertArrayNotHasKey("test", $mappingTable);
        $this->assertArrayHasKey("test2", $mappingTable);
        $this->assertArrayHasKey("test3_ymd", $mappingTable);
        $this->assertArrayHasKey("test3_y_m_d", $mappingTable);
        $this->assertArrayHasKey("test4_a", $mappingTable);
        $this->assertArrayHasKey("test4_b", $mappingTable);
        $this->assertEquals("A", $mappingTable["test2"]);
        $this->assertEquals($dateTime->format("Ymd"), $mappingTable["test3_ymd"]);
        $this->assertEquals($dateTime->format("Y-m-d"), $mappingTable["test3_y_m_d"]);
        $this->assertEquals("aa", $mappingTable["test4_a"]);
        $this->assertEquals("bb", $mappingTable["test4_b"]);
    }

    public function testCreateFromFilledArrayAndParse(): void
    {
        $dateTime = new DateTime();

        $placeholderHelper = ZugferdMailPlaceholderHelper::fromArray(["test" => null, "test2" => "A", "test3" => $dateTime, "test4" => ["a" => "aa", "b" => "bb"]]);

        $testString = "{test}_{test2}_{test3_ymd}_{test4_a}_{test4_b}";
        $parsedstring = $placeholderHelper->parseString($testString);

        $this->assertEquals(sprintf("_A_%s_aa_bb", $dateTime->format("Ymd")), $parsedstring);

        $testString = "{test2}_{test3_ymd}_{test4_a}_{test4_b}";
        $parsedstring = $placeholderHelper->parseString($testString);

        $this->assertEquals(sprintf("A_%s_aa_bb", $dateTime->format("Ymd")), $parsedstring);

        $testString = "{test4_a}_{test4_b}";
        $parsedstring = $placeholderHelper->parseString($testString);

        $this->assertEquals("aa_bb", $parsedstring);
    }

    public function testCreateFromZugferdDocumentReaderEn16931(): void
    {
        $documentReader = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/../assets/fx_en16931.xml");

        $this->assertNotNull($documentReader);
        $this->assertEquals(ZugferdProfiles::PROFILE_EN16931, $documentReader->getProfileId());

        $placeholderHelper = ZugferdMailPlaceholderHelper::fromZugferdDocumentReader($documentReader);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertNotEmpty($mappingTable);

        $this->assertArrayHasKey("documentno", $mappingTable);
        $this->assertArrayHasKey("documenttypecode", $mappingTable);
        $this->assertArrayHasKey("documentdate_ymd", $mappingTable);
        $this->assertArrayHasKey("documentdate_y_m_d", $mappingTable);
        $this->assertArrayNotHasKey("documentname", $mappingTable);
        $this->assertArrayNotHasKey("documentlanguage", $mappingTable);
        $this->assertArrayHasKey("documentinvoicecurrency", $mappingTable);
        $this->assertArrayNotHasKey("documenttaxcurrency", $mappingTable);
        $this->assertArrayNotHasKey("documentspecifiedperiod", $mappingTable);

        $this->assertArrayHasKey("documentsellerid_0", $mappingTable);
        $this->assertArrayHasKey("documentsellerglobalid_0088", $mappingTable);
        $this->assertArrayHasKey("documentsellername", $mappingTable);
        $this->assertArrayNotHasKey("documentsellerdescription", $mappingTable);
        $this->assertArrayHasKey("documentselleraddrline1", $mappingTable);
        $this->assertArrayNotHasKey("documentselleraddrline2", $mappingTable);
        $this->assertArrayNotHasKey("documentselleraddrline3", $mappingTable);
        $this->assertArrayHasKey("documentsellerpostcode", $mappingTable);
        $this->assertArrayHasKey("documentsellercity", $mappingTable);
        $this->assertArrayHasKey("documentsellercountry", $mappingTable);
        $this->assertArrayNotHasKey("documentsellersubdiv_0", $mappingTable);
    }

    public function testCreateFromZugferdDocumentReaderExtended(): void
    {
        $documentReader = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/../assets/fx_extended.xml");

        $this->assertNotNull($documentReader);
        $this->assertEquals(ZugferdProfiles::PROFILE_EXTENDED, $documentReader->getProfileId());

        $placeholderHelper = ZugferdMailPlaceholderHelper::fromZugferdDocumentReader($documentReader);

        $mappingTable = $this->getPrivatePropertyFromObject($placeholderHelper, 'mappingTable');
        $mappingTable = $mappingTable->getValue($placeholderHelper);

        $this->assertIsArray($mappingTable);
        $this->assertNotEmpty($mappingTable);

        $this->assertArrayHasKey("documentno", $mappingTable);
        $this->assertArrayHasKey("documenttypecode", $mappingTable);
        $this->assertArrayHasKey("documentdate_ymd", $mappingTable);
        $this->assertArrayHasKey("documentdate_y_m_d", $mappingTable);
        $this->assertArrayHasKey("documentname", $mappingTable);
        $this->assertArrayHasKey("documentlanguage", $mappingTable);
        $this->assertArrayHasKey("documentinvoicecurrency", $mappingTable);
        $this->assertArrayHasKey("documenttaxcurrency", $mappingTable);
        $this->assertArrayNotHasKey("documentspecifiedperiod", $mappingTable);

        $this->assertArrayHasKey("documentsellerid_0", $mappingTable);
        $this->assertArrayHasKey("documentsellerglobalid_0088", $mappingTable);
        $this->assertArrayHasKey("documentsellername", $mappingTable);
        $this->assertArrayHasKey("documentsellerdescription", $mappingTable);
        $this->assertArrayHasKey("documentselleraddrline1", $mappingTable);
        $this->assertArrayHasKey("documentselleraddrline2", $mappingTable);
        $this->assertArrayHasKey("documentselleraddrline3", $mappingTable);
        $this->assertArrayHasKey("documentsellerpostcode", $mappingTable);
        $this->assertArrayHasKey("documentsellercity", $mappingTable);
        $this->assertArrayHasKey("documentsellercountry", $mappingTable);
        $this->assertArrayHasKey("documentsellersubdiv_0", $mappingTable);
    }
}
