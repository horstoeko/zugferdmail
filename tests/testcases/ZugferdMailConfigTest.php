<?php

namespace horstoeko\zugferdmail\tests\testcases;

use stdClass;
use InvalidArgumentException;
use RuntimeException;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerCopyMessage;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerNull;
use horstoeko\zugferdmail\tests\TestCase;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

class ZugferdMailConfigTest extends TestCase
{
    public function testMailCondigInit(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertNotNull($config);
        $this->assertEquals("d-M-Y", $config->getDateFormat());
        $this->assertEquals(false, $config->getUblSupportEnabled());
        $this->assertEquals(false, $config->getSymfonyValidationEnabled());
        $this->assertEquals(false, $config->getSymfonyValidationEnabled());
        $this->assertEquals(false, $config->getXsdValidationEnabled());
        $this->assertEquals(false, $config->getKositValidationEnabled());
        $this->assertEquals(false, $config->getProcessUnseenMessagesOnlyEnabled());
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

    public function testMailConfigActivateSymfonyValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getSymfonyValidationEnabled());

        $config->activateSymfonyValidation();

        $this->assertEquals(true, $config->getSymfonyValidationEnabled());
    }

    public function testMailConfigDeactivateSymfonyValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getSymfonyValidationEnabled());

        $config->activateSymfonyValidation();

        $this->assertEquals(true, $config->getSymfonyValidationEnabled());

        $config->deactivateSymfonyValidation();

        $this->assertEquals(false, $config->getSymfonyValidationEnabled());
    }

    public function testMailConfigActivateXsdValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getXsdValidationEnabled());

        $config->activateXsdValidation();

        $this->assertEquals(true, $config->getXsdValidationEnabled());
    }

    public function testMailConfigDeactivateXsdValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getXsdValidationEnabled());

        $config->activateXsdValidation();

        $this->assertEquals(true, $config->getXsdValidationEnabled());

        $config->deactivateXsdValidation();

        $this->assertEquals(false, $config->getXsdValidationEnabled());
    }

    public function testMailConfigActivateKositValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getKositValidationEnabled());

        $config->activateKositValidation();

        $this->assertEquals(true, $config->getKositValidationEnabled());
    }

    public function testMailConfigDeactivateKositValidation(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getKositValidationEnabled());

        $config->activateKositValidation();

        $this->assertEquals(true, $config->getKositValidationEnabled());

        $config->deactivateKositValidation();

        $this->assertEquals(false, $config->getKositValidationEnabled());
    }

    public function testMailConfigActivateProcessUnseenMessagesOnly(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getProcessUnseenMessagesOnlyEnabled());

        $config->activateProcessUnseenMessagesOnly();

        $this->assertEquals(true, $config->getProcessUnseenMessagesOnlyEnabled());
    }

    public function testMailConfigDeactivateProcessUnseenMessagesOnly(): void
    {
        $config = new ZugferdMailConfig();

        $this->assertEquals(false, $config->getProcessUnseenMessagesOnlyEnabled());

        $config->activateProcessUnseenMessagesOnly();

        $this->assertEquals(true, $config->getProcessUnseenMessagesOnlyEnabled());

        $config->deactivateProcessUnseenMessagesOnly();

        $this->assertEquals(false, $config->getProcessUnseenMessagesOnlyEnabled());
    }

    public function testMailConfigMakeClientManager(): void
    {
        $config = new ZugferdMailConfig();
        $config->addAccount("test", "127.0.0.1", 993, "imap", "tls", true, "demouser", "demopwd");

        $clientManagher = $config->makeClientManager();

        $this->assertNotNull($clientManagher);
        $this->assertInstanceOf(ClientManager::class, $clientManagher);
        $this->assertNotNull($clientManagher->account("test"));
        $this->assertInstanceOf(Client::class, $clientManagher->account("test"));
        $this->assertEquals("127.0.0.1", $clientManagher->account("test")->host);
        $this->assertEquals(993, $clientManagher->account("test")->port);
        $this->assertEquals("imap", $clientManagher->account("test")->protocol);
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
        $mailAccount->setUsername('demouser');
        $mailAccount->setPassword('demopassword');
        $mailAccount->addFolderToWatch('INBOX');
        $mailAccount->addFolderToWatch('INBOX/somefolder');
        $mailAccount->addMimeTypeToWatch('text/xml');
        $mailAccount->addMimeTypeToWatch('application/pdf');
        $mailAccount->addHandler(new ZugferdMailHandlerNull());
        $mailAccount->addHandler(new ZugferdMailHandlerCopyMessage('INBOX/someotherfolder'));

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
        $this->assertEquals("demouser", $mailAccount->getUsername());
        $this->assertEquals("demopassword", $mailAccount->getPassword());
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

    public function testLoadConfigFromNotExistingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ZugferdMailConfig::loadFromFile(dirname(__FILE__) . '/../assets/unknown.json');
    }

    public function testLoadConfigWithContentWhichIsNoJson(): void
    {
        $configFilename = dirname(__FILE__) . '/../assets/config.nojson.json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('The file %s does not seem to be a valid json.', $configFilename));

        ZugferdMailConfig::loadFromFile($configFilename);
    }

    public function testLoadConfigWithContentWhichIsInvalidJson(): void
    {
        $configFilename = dirname(__FILE__) . '/../assets/config.invalid.json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('The file %s could not be identified as a valid JSON file', $configFilename));

        ZugferdMailConfig::loadFromFile($configFilename);
    }

    public function testLoadConfigWithValidJson(): void
    {
        $configFilename = dirname(__FILE__) . '/../assets/config.valid.json';

        $config = ZugferdMailConfig::loadFromFile($configFilename);

        $this->assertNotNull($config);
        $this->assertInstanceOf(ZugferdMailConfig::class, $config);
        $this->assertEquals("d-M-Y", $config->getDateFormat());
        $this->assertTrue($config->getUblSupportEnabled());
        $this->assertTrue($config->getSymfonyValidationEnabled());
        $this->assertTrue($config->getXsdValidationEnabled());
        $this->assertTrue($config->getKositValidationEnabled());
        $this->assertTrue($config->getProcessUnseenMessagesOnlyEnabled());

        $this->assertNotEmpty($config->getAccounts());
        $this->assertArrayHasKey(0, $config->getAccounts());
        $this->assertArrayNotHasKey(1, $config->getAccounts());

        $mailAccount = $config->getAccounts()[0];

        $this->assertEquals("127.0.0.1", $mailAccount->getHost());
        $this->assertEquals(993, $mailAccount->getPort());
        $this->assertEquals("imap", $mailAccount->getProtocol());
        $this->assertEquals("tls", $mailAccount->getEncryption());
        $this->assertTrue($mailAccount->getValidateCert());
        $this->assertEquals("demouser", $mailAccount->getUsername());
        $this->assertEquals("demopassword", $mailAccount->getPassword());
        $this->assertEmpty($mailAccount->getAuthentication());
        $this->assertEquals(45, $mailAccount->getTimeout());

        $mailAccountFoldersToWatch = $mailAccount->getFoldersTowatch();

        $this->assertArrayHasKey(0, $mailAccountFoldersToWatch);
        $this->assertArrayHasKey(1, $mailAccountFoldersToWatch);
        $this->assertArrayNotHasKey(2, $mailAccountFoldersToWatch);
        $this->assertEquals("INBOX", $mailAccountFoldersToWatch[0]);
        $this->assertEquals("INBOX/somefolder", $mailAccountFoldersToWatch[1]);

        $mailAccountMimeTypesToWatch = $mailAccount->getMimeTypesToWatch();

        $this->assertArrayHasKey(0, $mailAccountMimeTypesToWatch);
        $this->assertArrayHasKey(1, $mailAccountMimeTypesToWatch);
        $this->assertArrayNotHasKey(2, $mailAccountMimeTypesToWatch);
        $this->assertEquals("text/xml", $mailAccountMimeTypesToWatch[0]);
        $this->assertEquals("application/pdf", $mailAccountMimeTypesToWatch[1]);

        $mailAccountHandlers = $mailAccount->getHandlers();

        $this->assertArrayHasKey(0, $mailAccountHandlers);
        $this->assertArrayHasKey(1, $mailAccountHandlers);
        $this->assertArrayNotHasKey(2, $mailAccountHandlers);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccountHandlers[0]);
        $this->assertInstanceOf(ZugferdMailHandlerCopyMessage::class, $mailAccountHandlers[1]);

        /**
         * @var ZugferdMailHandlerCopyMessage
         */
        $mailAccountHandler1 = $mailAccountHandlers[1];

        $this->assertEquals("INBOX/someotherfolder", $mailAccountHandler1->getCopyToFolder());
    }

    public function testSaveConfigToInvalidFilename(): void
    {
        $configFilename = dirname(__FILE__) . '/../somefolder/config.save.json';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Directory of file %s does not exist.', $configFilename));

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setPort(993);
        $mailAccount->setProtocol("imap");
        $mailAccount->setEncryption("tls");
        $mailAccount->setUsername('demouser');
        $mailAccount->setPassword('demopassword');
        $mailAccount->addFolderToWatch('INBOX');
        $mailAccount->addFolderToWatch('INBOX/somefolder');
        $mailAccount->addMimeTypeToWatch('text/xml');
        $mailAccount->addMimeTypeToWatch('application/pdf');
        $mailAccount->addHandler(new ZugferdMailHandlerNull());
        $mailAccount->addHandler(new ZugferdMailHandlerCopyMessage('INBOX/someotherfolder'));

        $config = new ZugferdMailConfig();
        $config->addAccountObject($mailAccount);
        $config->saveToFile($configFilename);
    }

    public function testSaveConfigToValidFilename(): void
    {
        $configFilename = dirname(__FILE__) . '/../assets/config.save.json';

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setPort(993);
        $mailAccount->setProtocol("imap");
        $mailAccount->setEncryption("tls");
        $mailAccount->setUsername('demouser');
        $mailAccount->setPassword('demopassword');
        $mailAccount->addFolderToWatch('INBOX');
        $mailAccount->addFolderToWatch('INBOX/somefolder');
        $mailAccount->addMimeTypeToWatch('text/xml');
        $mailAccount->addMimeTypeToWatch('application/pdf');
        $mailAccount->addHandler(new ZugferdMailHandlerNull());
        $mailAccount->addHandler(new ZugferdMailHandlerCopyMessage('INBOX/someotherfolder'));

        $config = new ZugferdMailConfig();
        $config->addAccountObject($mailAccount);
        $config->saveToFile($configFilename);

        $this->assertFileExists($configFilename);
        $this->assertInstanceOf(stdClass::class, json_decode(file_get_contents($configFilename)));
    }

    public function testSaveAndLoadConfigSameFile(): void
    {
        $configFilename = dirname(__FILE__) . '/../assets/config.save.json';

        // Create config and save to config file

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setPort(993);
        $mailAccount->setProtocol("imap");
        $mailAccount->setEncryption("tls");
        $mailAccount->setUsername('demouser');
        $mailAccount->setPassword('demopassword');
        $mailAccount->addFolderToWatch('INBOX');
        $mailAccount->addFolderToWatch('INBOX/somefolder');
        $mailAccount->addMimeTypeToWatch('text/xml');
        $mailAccount->addMimeTypeToWatch('application/pdf');
        $mailAccount->addHandler(new ZugferdMailHandlerNull());
        $mailAccount->addHandler(new ZugferdMailHandlerCopyMessage('INBOX/someotherfolder'));

        $config = new ZugferdMailConfig();
        $config->addAccountObject($mailAccount);
        $config->activateSymfonyValidation();
        $config->activateXsdValidation();
        $config->activateKositValidation();
        $config->activateProcessUnseenMessagesOnly();
        $config->saveToFile($configFilename);

        // Load formerly saved config file

        $config = ZugferdMailConfig::loadFromFile($configFilename);

        $this->assertNotNull($config);
        $this->assertInstanceOf(ZugferdMailConfig::class, $config);
        $this->assertEquals("d-M-Y", $config->getDateFormat());
        $this->assertFalse($config->getUblSupportEnabled());
        $this->assertTrue($config->getSymfonyValidationEnabled());
        $this->assertTrue($config->getXsdValidationEnabled());
        $this->assertTrue($config->getKositValidationEnabled());
        $this->assertTrue($config->getProcessUnseenMessagesOnlyEnabled());

        $this->assertNotEmpty($config->getAccounts());
        $this->assertArrayHasKey(0, $config->getAccounts());
        $this->assertArrayNotHasKey(1, $config->getAccounts());

        $mailAccount = $config->getAccounts()[0];

        $this->assertEquals("127.0.0.1", $mailAccount->getHost());
        $this->assertEquals(993, $mailAccount->getPort());
        $this->assertEquals("imap", $mailAccount->getProtocol());
        $this->assertEquals("tls", $mailAccount->getEncryption());
        $this->assertTrue($mailAccount->getValidateCert());
        $this->assertEquals("demouser", $mailAccount->getUsername());
        $this->assertEquals("demopassword", $mailAccount->getPassword());
        $this->assertEmpty($mailAccount->getAuthentication());
        $this->assertEquals(30, $mailAccount->getTimeout());

        $mailAccountFoldersToWatch = $mailAccount->getFoldersTowatch();

        $this->assertArrayHasKey(0, $mailAccountFoldersToWatch);
        $this->assertArrayHasKey(1, $mailAccountFoldersToWatch);
        $this->assertArrayNotHasKey(2, $mailAccountFoldersToWatch);
        $this->assertEquals("INBOX", $mailAccountFoldersToWatch[0]);
        $this->assertEquals("INBOX/somefolder", $mailAccountFoldersToWatch[1]);

        $mailAccountMimeTypesToWatch = $mailAccount->getMimeTypesToWatch();

        $this->assertArrayHasKey(0, $mailAccountMimeTypesToWatch);
        $this->assertArrayHasKey(1, $mailAccountMimeTypesToWatch);
        $this->assertArrayNotHasKey(2, $mailAccountMimeTypesToWatch);
        $this->assertEquals("text/xml", $mailAccountMimeTypesToWatch[0]);
        $this->assertEquals("application/pdf", $mailAccountMimeTypesToWatch[1]);

        $mailAccountHandlers = $mailAccount->getHandlers();

        $this->assertArrayHasKey(0, $mailAccountHandlers);
        $this->assertArrayHasKey(1, $mailAccountHandlers);
        $this->assertArrayNotHasKey(2, $mailAccountHandlers);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccountHandlers[0]);
        $this->assertInstanceOf(ZugferdMailHandlerCopyMessage::class, $mailAccountHandlers[1]);

        /**
         * @var ZugferdMailHandlerCopyMessage
         */
        $mailAccountHandler1 = $mailAccountHandlers[1];

        $this->assertEquals("INBOX/someotherfolder", $mailAccountHandler1->getCopyToFolder());
    }
}
