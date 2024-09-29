<?php

namespace horstoeko\zugferdmail\tests\testcases;

use InvalidArgumentException;
use Webklex\PHPIMAP\ClientManager;
use horstoeko\zugferdmail\tests\TestCase;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\config\ZugferdMailAccount;

class MailConfigTest extends TestCase
{
    public function testMailCondigInit(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertNotNull($config);
        $this->assertEquals("d-M-Y", $config->getDateFormat());
        $this->assertEquals(false, $config->getUblSupportEnabled());
        $this->assertEmpty($config->getAccounts());
    }

    public function testMailConfigSetDateFormarInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $config = new ZugferdMailConfig();
        $config->setDateFormat("dummy");
    }

    public function testMailConfigSetDateFormarValid(): void
    {
        $config = new ZugferdMailConfig();
        $config->setDateFormat("d M y");

        $this->assertEquals("d M y", $config->getDateFormat());
    }

    public function testMailConfigActivateUblSupport(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getUblSupportEnabled());

        $config->activateUblSupport();

        $this->assertEquals(true, $config->getUblSupportEnabled());
    }

    public function testMailConfigDeactivateUblSupport(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getUblSupportEnabled());

        $config->activateUblSupport();

        $this->assertEquals(true, $config->getUblSupportEnabled());

        $config->deactivateUblSupport();

        $this->assertEquals(false, $config->getUblSupportEnabled());
    }

    public function testMailConfigMakeClientManager(): void
    {
        $config = new ZugferdMailConfig();
        $clientManagher = $config->makeClientManager();

        $this->assertNotNull($clientManagher);
        $this->assertInstanceOf(ClientManager::class, $clientManagher);
    }

    public function testMailConfigAddAccount(): void
    {
        $config = new ZugferdMailConfig();
        $config->addAccount("test", "127.0.0.1", 993, "imap", "tls", true, "demouser", "demopwd");

        $this->assertNotEmpty($config->getAccounts());
        $this->assertArrayHasKey(0, $config->getAccounts());
        $this->assertInstanceOf(ZugferdMailAccount::class, $config->getAccounts()[0]);

        $mailAccount = $config->getAccounts()[0];

        $this->assertEquals("127.0.0.1", $mailAccount->getHost());
        $this->assertEquals(993, $mailAccount->getPort());
        $this->assertEquals("imap", $mailAccount->getProtocol());
    }

    public function testMailConfigAddAccountObject(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setPort(993);
        $mailAccount->setProtocol("imap");
        $mailAccount->setEncryption("tls");

        $config = new ZugferdMailConfig();
        $config->addAccountObject($mailAccount);

        $this->assertNotEmpty($config->getAccounts());
        $this->assertArrayHasKey(0, $config->getAccounts());
        $this->assertInstanceOf(ZugferdMailAccount::class, $config->getAccounts()[0]);

        $mailAccount = $config->getAccounts()[0];

        $this->assertEquals("127.0.0.1", $mailAccount->getHost());
        $this->assertEquals(993, $mailAccount->getPort());
        $this->assertEquals("imap", $mailAccount->getProtocol());
        $this->assertEquals("tls", $mailAccount->getEncryption());
    }

    public function testMailConfigRemoveAccount(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEmpty($config->getAccounts());

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setIdentifier("demo");
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setPort(993);
        $mailAccount->setProtocol("imap");
        $mailAccount->setEncryption("tls");

        $config->addAccountObject($mailAccount);

        $this->assertNotEmpty($config->getAccounts());
        $this->assertArrayHasKey(0, $config->getAccounts());

        $config->removeAccount("demo");

        $this->assertEmpty($config->getAccounts());
    }
}
