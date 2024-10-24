<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\helpers\ZugferdMailStringHelper;
use horstoeko\zugferdmail\tests\TestCase;

class ZugferdMailStringHelperTest extends TestCase
{
    public function testTruncateString(): void
    {
        $testString = "012345678901234567890123456789";

        $this->assertEquals("012345678901234567890123456...", ZugferdMailStringHelper::truncateString($testString, 30));
        $this->assertEquals("012345678901234567890123456789", ZugferdMailStringHelper::truncateString($testString, 40));
    }
}
