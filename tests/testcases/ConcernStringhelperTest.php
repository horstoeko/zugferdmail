<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailStringHelper;
use horstoeko\zugferdmail\tests\TestCase;

class ConcernStringhelperTest extends TestCase
{
    use ZugferdMailStringHelper;

    public function testTruncateString(): void
    {
        $testString = "012345678901234567890123456789";

        $this->assertEquals("012345678901234567890123456...", $this->zfMailTruncateString($testString, 30));
        $this->assertEquals("012345678901234567890123456789", $this->zfMailTruncateString($testString, 40));
    }
}
