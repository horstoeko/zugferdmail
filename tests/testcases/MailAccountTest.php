<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\tests\TestCase;

class MailAccountTest extends TestCase
{
    public function testCreateMailAccount(): void
    {
        $mailAccount = new ZugferdMailAccount();

        $this->assertNotNull($mailAccount);
    }
}
