<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailClearsMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailSendsMessagesToMessageBag;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;
use horstoeko\zugferdmail\tests\TestCase;
use horstoeko\zugferdmail\ZugferdMailMessageBag;

class ZugferdMailConcernMessageBagTest extends TestCase
{
    use ZugferdMailClearsMessageBag;
    use ZugferdMailReceivesMessagesFromMessageBag;
    use ZugferdMailSendsMessagesToMessageBag;

    public function testInitialMessageBag(): void
    {
        $this->assertTrue($this->hasNoLogMessagesInMessageBag());
        $this->assertFalse($this->hasLogMessagesInMessageBag());

        $this->assertTrue($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->assertFalse($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertTrue($this->hasNoWarningMessagesInMessageBag());
        $this->assertFalse($this->hasWarningMessagesInMessageBag());

        $this->assertTrue($this->hasNoErrorMessagesInMessageBag());
        $this->assertFalse($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertEmpty($this->getLogMessagesFromMessageBag());
        $this->assertEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertEmpty($this->getAllMessagesFromMessageBag());

        $this->assertFalse($this->getHasAnyMessageInMessageBag());
    }

    public function testAddLogMessageToMessageBag(): void
    {
        $this->addLogMessageToMessageBag('Message 1');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertTrue($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->assertFalse($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertTrue($this->hasNoWarningMessagesInMessageBag());
        $this->assertFalse($this->hasWarningMessagesInMessageBag());

        $this->assertTrue($this->hasNoErrorMessagesInMessageBag());
        $this->assertFalse($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[0];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 1", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_LOG, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddSecondaryLogMessageToMessageBag(): void
    {
        $this->addLogSecondaryMessageToMessageBag('Message 2');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertTrue($this->hasNoWarningMessagesInMessageBag());
        $this->assertFalse($this->hasWarningMessagesInMessageBag());

        $this->assertTrue($this->hasNoErrorMessagesInMessageBag());
        $this->assertFalse($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(2, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[1];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 2", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_LOG_SECONDARY, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddWarningMessageToMessageBag(): void
    {
        $this->addWarningMessageToMessageBag('Message 3');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertFalse($this->hasNoWarningMessagesInMessageBag());
        $this->assertTrue($this->hasWarningMessagesInMessageBag());

        $this->assertTrue($this->hasNoErrorMessagesInMessageBag());
        $this->assertFalse($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertNotEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(3, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[2];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 3", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_WARN, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddErrorMessageToMessageBag(): void
    {
        $this->addErrorMessageToMessageBag('Message 4');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertFalse($this->hasNoWarningMessagesInMessageBag());
        $this->assertTrue($this->hasWarningMessagesInMessageBag());

        $this->assertFalse($this->hasNoErrorMessagesInMessageBag());
        $this->assertTrue($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertNotEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertNotEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(1, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(4, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[3];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 4", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_ERROR, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddSuccessMessageToMessageBag(): void
    {
        $this->addSuccessMessageToMessageBag('Message 5');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertFalse($this->hasNoWarningMessagesInMessageBag());
        $this->assertTrue($this->hasWarningMessagesInMessageBag());

        $this->assertFalse($this->hasNoErrorMessagesInMessageBag());
        $this->assertTrue($this->hasErrorMessagesInMessageBag());

        $this->assertFalse($this->hasNoSuccessMessagesInMessageBag());
        $this->assertTrue($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertNotEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertNotEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertNotEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(1, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(1, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(5, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[4];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 5", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddThrowableToMessageBag(): void
    {
        $this->addThrowableToMessageBag(new \Exception('ExceptionMessage 1'));

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertFalse($this->hasNoWarningMessagesInMessageBag());
        $this->assertTrue($this->hasWarningMessagesInMessageBag());

        $this->assertFalse($this->hasNoErrorMessagesInMessageBag());
        $this->assertTrue($this->hasErrorMessagesInMessageBag());

        $this->assertFalse($this->hasNoSuccessMessagesInMessageBag());
        $this->assertTrue($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertNotEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertNotEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertNotEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(2, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(1, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(6, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[5];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("ExceptionMessage 1", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_ERROR, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertNotEmpty($message['additionalData']);
        $this->assertArrayHasKey("errno", $message['additionalData']);
        $this->assertArrayHasKey("errfile", $message['additionalData']);
        $this->assertArrayHasKey("errline", $message['additionalData']);
        $this->assertArrayHasKey("errtrace", $message['additionalData']);
        $this->assertEquals(0, $message['additionalData']["errno"]);
        $this->assertStringContainsString("tests/testcases/ZugferdMailConcernMessageBagTest.php", $message['additionalData']["errfile"]);
        $this->assertEquals(313, $message['additionalData']["errline"]);
        $this->assertNotEquals('', $message['additionalData']["errtrace"]);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testAddMessageToMessageBag(): void
    {
        $this->addMessageToMessageBag(ZugferdMailMessageBagType::MESSAGETYPE_LOG, 'Message 6');

        $this->assertFalse($this->hasNoLogMessagesInMessageBag());
        $this->assertTrue($this->hasLogMessagesInMessageBag());

        $this->assertFalse($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->asserttrue($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertFalse($this->hasNoWarningMessagesInMessageBag());
        $this->assertTrue($this->hasWarningMessagesInMessageBag());

        $this->assertFalse($this->hasNoErrorMessagesInMessageBag());
        $this->assertTrue($this->hasErrorMessagesInMessageBag());

        $this->assertFalse($this->hasNoSuccessMessagesInMessageBag());
        $this->assertTrue($this->hasSuccessMessagesInMessageBag());

        $this->assertNotEmpty($this->getLogMessagesFromMessageBag());
        $this->assertNotEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertNotEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertNotEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertNotEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertCount(2, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(2, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(1, $this->getSuccessMessagesFromMessageBag());

        $this->assertCount(7, $this->getAllMessagesFromMessageBag());

        $this->assertTrue($this->getHasAnyMessageInMessageBag());

        $message = $this->getAllMessagesFromMessageBag()[6];

        $this->assertIsArray($message);
        $this->assertArrayHasKey("message", $message);
        $this->assertArrayHasKey("type", $message);
        $this->assertArrayHasKey("source", $message);
        $this->assertArrayHasKey("additionalData", $message);
        $this->assertArrayHasKey("datetime", $message);

        $this->assertEquals("Message 6", $message['message']);
        $this->assertEquals(ZugferdMailMessageBagType::MESSAGETYPE_LOG, $message['type']);
        $this->assertEquals("ZugferdMailConcernMessageBagTest", $message['source']);
        $this->assertIsArray($message['additionalData']);
        $this->assertEmpty($message['additionalData']);
        $this->assertNotNull($message['datetime']);
        $this->assertInstanceOf(\DateTime::class, $message['datetime']);
    }

    public function testClearMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertTrue($this->hasNoLogMessagesInMessageBag());
        $this->assertFalse($this->hasLogMessagesInMessageBag());

        $this->assertTrue($this->hasNoLogSecondaryMessagesInMessageBag());
        $this->assertFalse($this->hasLogSecondaryMessagesInMessageBag());

        $this->assertTrue($this->hasNoWarningMessagesInMessageBag());
        $this->assertFalse($this->hasWarningMessagesInMessageBag());

        $this->assertTrue($this->hasNoErrorMessagesInMessageBag());
        $this->assertFalse($this->hasErrorMessagesInMessageBag());

        $this->assertTrue($this->hasNoSuccessMessagesInMessageBag());
        $this->assertFalse($this->hasSuccessMessagesInMessageBag());

        $this->assertEmpty($this->getLogMessagesFromMessageBag());
        $this->assertEmpty($this->getLogSecondaryMessagesFromMessageBag());
        $this->assertEmpty($this->getWarningMessagesFromMessageBag());
        $this->assertEmpty($this->getErrorMessagesFromMessageBag());
        $this->assertEmpty($this->getSuccessMessagesFromMessageBag());

        $this->assertEmpty($this->getAllMessagesFromMessageBag());

        $this->assertFalse($this->getHasAnyMessageInMessageBag());
    }

    public function testMessageBagToString(): void
    {
        $this->clearMessageBag();
        $this->addLogMessageToMessageBag('Message 1');

        $messageBag = ZugferdMailMessageBag::factory();
        $messageBagAsString = (string)$messageBag;
        $messageBagAsJson = $messageBag->toJson();

        $this->assertIsString($messageBagAsString);
        $this->assertIsString($messageBagAsJson);
        $this->assertSame($messageBagAsJson, $messageBagAsString);

        $this->assertJson($messageBagAsJson);
        $this->assertJson($messageBagAsString);

        $messageBagJson = json_decode($messageBagAsString);

        $this->assertIsArray($messageBagJson);
        $this->assertArrayHasKey(0, $messageBagJson);
        $this->assertArrayNotHasKey(1, $messageBagJson);
        $this->assertIsObject($messageBagJson[0]);
        $this->assertObjectHasProperty('type', $messageBagJson[0]);
        $this->assertObjectHasProperty('source', $messageBagJson[0]);
        $this->assertObjectHasProperty('message', $messageBagJson[0]);
        $this->assertObjectHasProperty('additionalData', $messageBagJson[0]);
        $this->assertObjectHasProperty('datetime', $messageBagJson[0]);
    }

    public function testAddMultipleMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleMessagesToMessageBag(ZugferdMailMessageBagType::MESSAGETYPE_LOG, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(2, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleLogMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogMessagesToMessageBag(["Test 1", "Test 2", "Test 3"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(3, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(3, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleLogSecondaryMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogSecondaryMessagesToMessageBag(["Test 1", "Test 2", "Test 3"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(3, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(3, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleWarningMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleWarningMessagesToMessageBag(["Test 1", "Test 2", "Test 3"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(3, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(3, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleErrorMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleErrorMessagesToMessageBag(["Test 1", "Test 2", "Test 3"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(3, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(3, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleSuccessMessagesToMessageBag(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleSuccessMessagesToMessageBag(["Test 1", "Test 2", "Test 3"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(3, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(3, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMessageToMessageBagIf(false, ZugferdMailMessageBagType::MESSAGETYPE_LOG, 'Message 1');
        $this->addMessageToMessageBagIf(true, ZugferdMailMessageBagType::MESSAGETYPE_LOG, 'Message 2');
        $this->addMessageToMessageBagIf(false, ZugferdMailMessageBagType::MESSAGETYPE_LOG, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getLogMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getLogMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getLogMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddLogMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addLogMessageToMessageBagIf(false, 'Message 1');
        $this->addLogMessageToMessageBagIf(true, 'Message 2');
        $this->addLogMessageToMessageBagIf(false, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(1, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getLogMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getLogMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getLogMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddLogSecondaryMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addLogSecondaryMessageToMessageBagIf(false, 'Message 1');
        $this->addLogSecondaryMessageToMessageBagIf(true, 'Message 2');
        $this->addLogSecondaryMessageToMessageBagIf(false, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getLogSecondaryMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddWarningMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addWarningMessageToMessageBagIf(false, 'Message 1');
        $this->addWarningMessageToMessageBagIf(true, 'Message 2');
        $this->addWarningMessageToMessageBagIf(false, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(1, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getWarningMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getWarningMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getWarningMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddErrorMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addErrorMessageToMessageBagIf(false, 'Message 1');
        $this->addErrorMessageToMessageBagIf(true, 'Message 2');
        $this->addErrorMessageToMessageBagIf(false, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(1, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getErrorMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getErrorMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getErrorMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddSuccessMessageToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addSuccessMessageToMessageBagIf(false, 'Message 1');
        $this->addSuccessMessageToMessageBagIf(true, 'Message 2');
        $this->addSuccessMessageToMessageBagIf(false, 'Message 3');

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(1, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getSuccessMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getSuccessMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddThrowableToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addThrowableToMessageBagIf(false, new \Exception("Message 1"));
        $this->addThrowableToMessageBagIf(true, new \Exception("Message 2"));
        $this->addThrowableToMessageBagIf(false, new \Exception("Message 3"));

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(1, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(1, $this->getAllMessagesFromMessageBag());

        $this->assertArrayHasKey(0, $this->getErrorMessagesFromMessageBag());
        $this->assertArrayNotHasKey(1, $this->getErrorMessagesFromMessageBag());
        $this->assertEquals("Message 2", $this->getErrorMessagesFromMessageBag()[0]["message"]);
    }

    public function testAddMultipleMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleMessagesToMessageBagIf(false, ZugferdMailMessageBagType::MESSAGETYPE_LOG, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleMessagesToMessageBagIf(true, ZugferdMailMessageBagType::MESSAGETYPE_LOG, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(2, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleLogMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogMessagesToMessageBagIf(false, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogMessagesToMessageBagIf(true, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(2, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleLogSecondaryMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogSecondaryMessagesToMessageBagIf(false, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleLogSecondaryMessagesToMessageBagIf(true, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(2, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleWarningMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleWarningMessagesToMessageBagIf(false, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleWarningMessagesToMessageBagIf(true, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(2, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleErrorMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleErrorMessagesToMessageBagIf(false, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleErrorMessagesToMessageBagIf(true, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(2, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(0, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }

    public function testAddMultipleSuccessMessagesToMessageBagIf(): void
    {
        $this->clearMessageBag();

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleSuccessMessagesToMessageBagIf(false, ["Test 1", "Test 2"]);

        $this->assertFalse($this->getHasAnyMessageInMessageBag());

        $this->addMultipleSuccessMessagesToMessageBagIf(true, ["Test 1", "Test 2"]);

        $this->assertTrue($this->getHasAnyMessageInMessageBag());
        $this->assertCount(0, $this->getLogMessagesFromMessageBag());
        $this->assertCount(0, $this->getLogSecondaryMessagesFromMessageBag());
        $this->assertCount(0, $this->getWarningMessagesFromMessageBag());
        $this->assertCount(0, $this->getErrorMessagesFromMessageBag());
        $this->assertCount(2, $this->getSuccessMessagesFromMessageBag());
        $this->assertCount(2, $this->getAllMessagesFromMessageBag());
    }
}
