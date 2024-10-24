<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferdmail\concerns\ZugferdMailParsesPlaceholders;
use horstoeko\zugferdmail\tests\TestCase;

class ConcernParsesPlaceholdersTest extends TestCase
{
    use ZugferdMailParsesPlaceholders;

    public function testParsePlaceholdersByZugferdDocumentReaderEn16931(): void
    {
        $documentReader = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/../assets/fx_en16931.xml");

        $this->assertNotNull($documentReader);
        $this->assertEquals(ZugferdProfiles::PROFILE_EN16931, $documentReader->getProfileId());

        $testString = "Invoice_{documentno}_{documentsellername}";
        $parsedString = $this->parsePlaceholdersByZugferdDocumentReader($documentReader, $testString);

        $this->assertIsString($parsedString);
        $this->assertEquals("Invoice_471102_Lieferant GmbH", $parsedString);
    }

    public function testParsePlaceholdersByZugferdDocumentReaderExtended(): void
    {
        $documentReader = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/../assets/fx_extended.xml");

        $this->assertNotNull($documentReader);
        $this->assertEquals(ZugferdProfiles::PROFILE_EXTENDED, $documentReader->getProfileId());

        $testString = "Invoice_{documentno}_{documentsellername}";
        $parsedString = $this->parsePlaceholdersByZugferdDocumentReader($documentReader, $testString);

        $this->assertIsString($parsedString);
        $this->assertEquals("Invoice_471102_Lieferant GmbH", $parsedString);
    }
}
