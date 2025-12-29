<?php

namespace horstoeko\zugferdmail\tests\testcases;

use Closure;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerCli;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerNull;
use horstoeko\zugferdmail\tests\TestCase;
use InvalidArgumentException;

class ZugferdMailAccountTest extends TestCase
{
    public function testMailAccountInit(): void
    {
        $mailAccount = new ZugferdMailAccount();

        $this->assertNotNull($mailAccount);
        $this->assertNotEmpty($mailAccount->getIdentifier());
        $this->assertTrue(preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $mailAccount->getIdentifier()) === 1);
        $this->assertEquals("", $mailAccount->getHost());
        $this->assertEquals(0, $mailAccount->getPort());
        $this->assertEquals("imap", $mailAccount->getProtocol());
        $this->assertEquals("ssl", $mailAccount->getEncryption());
        $this->assertEquals(true, $mailAccount->getValidateCert());
        $this->assertEquals("", $mailAccount->getUsername());
        $this->assertEquals("", $mailAccount->getPassword());
        $this->assertNull($mailAccount->getAuthentication());
        $this->assertEquals(30, $mailAccount->getTimeout());
        $this->assertEmpty($mailAccount->getFoldersTowatch());
        $this->assertEmpty($mailAccount->getMimeTypesToWatch());
        $this->assertEmpty($mailAccount->getHandlers());
        $this->assertEmpty($mailAccount->getHandlersNoDocumentFound());
        $this->assertEmpty($mailAccount->getCallbacks());
        $this->assertEmpty($mailAccount->getCallbacksNoDocumentFound());
        $this->assertFalse($mailAccount->getUnseenMessagesOnlyEnabled());
    }

    public function testMailAccountSetIdInvalid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setIdentifier("");

        $this->assertEquals(1, preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $mailAccount->getIdentifier()));
    }

    public function testMailAccountSetIdValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setIdentifier("id");

        $this->assertEquals("id", $mailAccount->getIdentifier());
    }

    public function testMailAccountSetHostInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("");
    }

    public function testMailAccountSetHostValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");

        $this->assertEquals("127.0.0.1", $mailAccount->getHost());
    }

    public function testMailAccountSetPortInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setPort(-1);
    }

    public function testMailAccountSetPortValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setPort(25);

        $this->assertEquals(25, $mailAccount->getPort());
    }

    public function testMailAccountSetProtocolInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf("The protocol must be one of imap, legacy-imap, pop3 or nntp, unknown given"));

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setProtocol("unknown");
    }

    public function testMailAccountSetProtocolValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setProtocol("pop3");

        $this->assertEquals("pop3", $mailAccount->getProtocol());
    }

    public function testMailAccountSetEncryptionInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setEncryption("unknown");
    }

    public function testMailAccountSetEncryptionValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setEncryption("tls");

        $this->assertEquals("tls", $mailAccount->getEncryption());
    }

    public function testMailAccountSetValidateCertValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setValidateCert(false);

        $this->assertEquals(false, $mailAccount->getValidateCert());
    }

    public function testMailAccountSetUsernameInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setUsername("");
    }

    public function testMailAccountSetUsernameValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setUsername("demouser");

        $this->assertEquals("demouser", $mailAccount->getUsername());
    }

    public function testMailAccountSetPasswordInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setPassword("");
    }

    public function testMailAccountSetPasswordValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setPassword("demopwd");

        $this->assertEquals("demopwd", $mailAccount->getPassword());
    }

    public function testMailAccountSetAuthenticationInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setAuthentication("");
    }

    public function testMailAccountSetAuthenticationValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setAuthentication("oauth");

        $this->assertEquals("oauth", $mailAccount->getAuthentication());
    }

    public function testMailAccountSetTimeoutInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setTimeout(-99);
    }

    public function testMailAccountSetTimeoutValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setTimeout(60);

        $this->assertEquals(60, $mailAccount->getTimeout());
    }

    public function testMailAccountSetFoldersToWatchEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setFoldersToWatch(['', '']);

        $this->assertIsArray($mailAccount->getFoldersTowatch());
        $this->assertEmpty($mailAccount->getFoldersTowatch());
    }

    public function testMailAccountSetFoldersToWatchOneEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setFoldersToWatch(['', 'INBOX']);

        $this->assertNotEmpty($mailAccount->getFoldersTowatch());
        $this->assertArrayNotHasKey(0, $mailAccount->getFoldersTowatch());
        $this->assertArrayHasKey(1, $mailAccount->getFoldersTowatch());
        $this->assertEquals('INBOX', $mailAccount->getFoldersTowatch()[1]);
    }

    public function testMailAccountSetFoldersToWatchAllNotEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setFoldersToWatch(['SOMEFOLDER', 'INBOX']);

        $this->assertNotEmpty($mailAccount->getFoldersTowatch());
        $this->assertArrayHasKey(0, $mailAccount->getFoldersTowatch());
        $this->assertArrayHasKey(1, $mailAccount->getFoldersTowatch());
        $this->assertArrayNotHasKey(2, $mailAccount->getFoldersTowatch());
        $this->assertEquals('SOMEFOLDER', $mailAccount->getFoldersTowatch()[0]);
        $this->assertEquals('INBOX', $mailAccount->getFoldersTowatch()[1]);
    }

    public function testMailAccountAddFoldersToWatchEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addFolderToWatch('');
    }

    public function testMailAccountAddFoldersToWatchNotEmpty(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addFolderToWatch('INBOX');

        $this->assertArrayHasKey(0, $mailAccount->getFoldersTowatch());
        $this->assertArrayNotHasKey(1, $mailAccount->getFoldersTowatch());
        $this->assertEquals('INBOX', $mailAccount->getFoldersTowatch()[0]);

        $mailAccount->addFolderToWatch('SOMEFOLDER');

        $this->assertArrayHasKey(0, $mailAccount->getFoldersTowatch());
        $this->assertArrayHasKey(1, $mailAccount->getFoldersTowatch());
        $this->assertArrayNotHasKey(2, $mailAccount->getFoldersTowatch());
        $this->assertEquals('INBOX', $mailAccount->getFoldersTowatch()[0]);
        $this->assertEquals('SOMEFOLDER', $mailAccount->getFoldersTowatch()[1]);
    }

    public function testMailAccountSetMimeTypesToWatchEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setMimeTypesToWatch(['', '']);

        $this->assertIsArray($mailAccount->getMimeTypesToWatch());
        $this->assertEmpty($mailAccount->getMimeTypesToWatch());
    }

    public function testMailAccountSetMimeTypesToWatchOneEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setMimeTypesToWatch(['', 'text/xml']);

        $this->assertNotEmpty($mailAccount->getMimeTypesToWatch());
        $this->assertArrayNotHasKey(0, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayHasKey(1, $mailAccount->getMimeTypesToWatch());
        $this->assertEquals('text/xml', $mailAccount->getMimeTypesToWatch()[1]);
    }

    public function testMailAccountSetMimeTypesToWatchAllNotEmptyArray(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setMimeTypesToWatch(['application/pdf', 'text/xml']);

        $this->assertNotEmpty($mailAccount->getMimeTypesToWatch());
        $this->assertArrayHasKey(0, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayHasKey(1, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayNotHasKey(2, $mailAccount->getMimeTypesToWatch());
        $this->assertEquals('application/pdf', $mailAccount->getMimeTypesToWatch()[0]);
        $this->assertEquals('text/xml', $mailAccount->getMimeTypesToWatch()[1]);
    }

    public function testMailAccountAddMimeTypesToWatchEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addMimeTypeToWatch('');
    }

    public function testMailAccountAddMimeTypesToWatchNotEmpty(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addMimeTypeToWatch('application/pdf');

        $this->assertArrayHasKey(0, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayNotHasKey(1, $mailAccount->getMimeTypesToWatch());
        $this->assertEquals('application/pdf', $mailAccount->getMimeTypesToWatch()[0]);

        $mailAccount->addMimeTypeToWatch('text/xml');

        $this->assertArrayHasKey(0, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayHasKey(1, $mailAccount->getMimeTypesToWatch());
        $this->assertArrayNotHasKey(2, $mailAccount->getMimeTypesToWatch());
        $this->assertEquals('application/pdf', $mailAccount->getMimeTypesToWatch()[0]);
        $this->assertEquals('text/xml', $mailAccount->getMimeTypesToWatch()[1]);
    }

    public function testMailAccountSetHandlersOneInvalid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHandlers([new ZugferdMailMessageBagType(), new ZugferdMailHandlerNull()]);

        $this->assertArrayNotHasKey(0, $mailAccount->getHandlers());
        $this->assertArrayHasKey(1, $mailAccount->getHandlers());
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlers()[1]);
    }

    public function testMailAccountSetHandlersAllValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHandlers([new ZugferdMailHandlerCli(), new ZugferdMailHandlerNull()]);

        $this->assertArrayHasKey(0, $mailAccount->getHandlers());
        $this->assertArrayHasKey(1, $mailAccount->getHandlers());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlers()[0]);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlers()[1]);
    }

    public function testMailAccountAddHandlers(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addHandler(new ZugferdMailHandlerCli());

        $this->assertArrayHasKey(0, $mailAccount->getHandlers());
        $this->assertArrayNotHasKey(1, $mailAccount->getHandlers());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlers()[0]);

        $mailAccount->addHandler(new ZugferdMailHandlerNull());

        $this->assertArrayHasKey(0, $mailAccount->getHandlers());
        $this->assertArrayHasKey(1, $mailAccount->getHandlers());
        $this->assertArrayNotHasKey(2, $mailAccount->getHandlers());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlers()[0]);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlers()[1]);
    }

    public function testMailAccountSetCallbacksOneInvalid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setCallbacks(
            [null, function () {
            }]
        );

        $this->assertArrayNotHasKey(0, $mailAccount->getCallbacks());
        $this->assertArrayHasKey(1, $mailAccount->getCallbacks());
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[1]);
    }

    public function testMailAccountSetCallbacksAllValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setCallbacks(
            [function () {
            }, function () {
            }]
        );

        $this->assertArrayHasKey(0, $mailAccount->getCallbacks());
        $this->assertArrayHasKey(1, $mailAccount->getCallbacks());
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[0]);
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[1]);
    }

    public function testMailAccountAddCallbacks(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addCallback(
            function () {
            }
        );

        $this->assertArrayHasKey(0, $mailAccount->getCallbacks());
        $this->assertArrayNotHasKey(1, $mailAccount->getCallbacks());
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[0]);

        $mailAccount->addCallback(
            function () {
            }
        );

        $this->assertArrayHasKey(0, $mailAccount->getCallbacks());
        $this->assertArrayHasKey(1, $mailAccount->getCallbacks());
        $this->assertArrayNotHasKey(2, $mailAccount->getCallbacks());
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[0]);
        $this->assertInstanceOf(Closure::class, $mailAccount->getCallbacks()[1]);
    }

    public function testMailAccountSetHandlersNoDocumentFoundOneInvalid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHandlersNoDocumentFound([new ZugferdMailMessageBagType(), new ZugferdMailHandlerNull()]);

        $this->assertArrayNotHasKey(0, $mailAccount->getHandlersNoDocumentFound());
        $this->assertArrayHasKey(1, $mailAccount->getHandlersNoDocumentFound());
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlersNoDocumentFound()[1]);
    }

    public function testMailAccountSetHandlersNoDocumentFoundAllValid(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHandlersNoDocumentFound([new ZugferdMailHandlerCli(), new ZugferdMailHandlerNull()]);

        $this->assertArrayHasKey(0, $mailAccount->getHandlersNoDocumentFound());
        $this->assertArrayHasKey(1, $mailAccount->getHandlersNoDocumentFound());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlersNoDocumentFound()[0]);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlersNoDocumentFound()[1]);
    }

    public function testMailAccountAddHandlersNoDocumentFound(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->addHandlerNoDocumentFound(new ZugferdMailHandlerCli());

        $this->assertArrayHasKey(0, $mailAccount->getHandlersNoDocumentFound());
        $this->assertArrayNotHasKey(1, $mailAccount->getHandlersNoDocumentFound());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlersNoDocumentFound()[0]);

        $mailAccount->addHandlerNoDocumentFound(new ZugferdMailHandlerNull());

        $this->assertArrayHasKey(0, $mailAccount->getHandlersNoDocumentFound());
        $this->assertArrayHasKey(1, $mailAccount->getHandlersNoDocumentFound());
        $this->assertArrayNotHasKey(2, $mailAccount->getHandlersNoDocumentFound());
        $this->assertInstanceOf(ZugferdMailHandlerCli::class, $mailAccount->getHandlersNoDocumentFound()[0]);
        $this->assertInstanceOf(ZugferdMailHandlerNull::class, $mailAccount->getHandlersNoDocumentFound()[1]);
    }

    public function testSetUnseenMessagesOnlyEnabled(): void
    {
        $mailAccount = new ZugferdMailAccount();

        $this->assertFalse($mailAccount->getUnseenMessagesOnlyEnabled());

        $mailAccount->setUnseenMessagesOnlyEnabled(true);

        $this->assertTrue($mailAccount->getUnseenMessagesOnlyEnabled());
    }

    public function testActivateDeactivateUnseenMessagesOnly(): void
    {
        $mailAccount = new ZugferdMailAccount();

        $this->assertFalse($mailAccount->getUnseenMessagesOnlyEnabled());

        $mailAccount->activateUnseenMessagesOnly();

        $this->assertTrue($mailAccount->getUnseenMessagesOnlyEnabled());

        $mailAccount->deactivateUnseenMessagesOnly();

        $this->assertFalse($mailAccount->getUnseenMessagesOnlyEnabled());
    }

    public function testGetMailAccountDefinition(): void
    {
        $mailAccount = new ZugferdMailAccount();
        $mailAccount->setHost("127.0.0.1");
        $mailAccount->setport(993);

        $mailAccountDefinition = $mailAccount->getAccountDefinition();

        $this->assertIsArray($mailAccountDefinition);
        $this->assertArrayHasKey("host", $mailAccountDefinition);
        $this->assertArrayHasKey("port", $mailAccountDefinition);
        $this->assertArrayHasKey("protocol", $mailAccountDefinition);
        $this->assertArrayHasKey("encryption", $mailAccountDefinition);
        $this->assertArrayHasKey("validate_cert", $mailAccountDefinition);
        $this->assertArrayHasKey("username", $mailAccountDefinition);
        $this->assertArrayHasKey("password", $mailAccountDefinition);
        $this->assertArrayHasKey("authentication", $mailAccountDefinition);
        $this->assertArrayHasKey("timeout", $mailAccountDefinition);

        $this->assertEquals("127.0.0.1", $mailAccountDefinition["host"]);
        $this->assertEquals(993, $mailAccountDefinition["port"]);
        $this->assertEquals("imap", $mailAccountDefinition["protocol"]);
        $this->assertEquals("ssl", $mailAccountDefinition["encryption"]);
        $this->assertEquals(true, $mailAccountDefinition["validate_cert"]);
        $this->assertEquals("", $mailAccountDefinition["username"]);
        $this->assertEquals("", $mailAccountDefinition["password"]);
        $this->assertNull($mailAccountDefinition["authentication"]);
        $this->assertEquals(30, $mailAccountDefinition["timeout"]);

        $mailAccount->setProtocol("pop3");

        $mailAccountDefinition = $mailAccount->getAccountDefinition();

        $this->assertEquals("pop3", $mailAccountDefinition["protocol"]);
    }
}
