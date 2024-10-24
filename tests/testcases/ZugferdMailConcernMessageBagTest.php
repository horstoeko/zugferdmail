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
    use ZugferdMailClearsMessageBag,
        ZugferdMailReceivesMessagesFromMessageBag,
        ZugferdMailSendsMessagesToMessageBag;

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(1, count($this->getAllMessagesFromMessageBag()));

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(2, count($this->getAllMessagesFromMessageBag()));

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(3, count($this->getAllMessagesFromMessageBag()));

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(0, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(4, count($this->getAllMessagesFromMessageBag()));

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(5, count($this->getAllMessagesFromMessageBag()));

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

        $this->assertEquals(1, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(2, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(6, count($this->getAllMessagesFromMessageBag()));

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
        $this->assertEquals(301, $message['additionalData']["errline"]);
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

        $this->assertEquals(2, count($this->getLogMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getLogSecondaryMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getWarningMessagesFromMessageBag()));
        $this->assertEquals(2, count($this->getErrorMessagesFromMessageBag()));
        $this->assertEquals(1, count($this->getSuccessMessagesFromMessageBag()));

        $this->assertEquals(7, count($this->getAllMessagesFromMessageBag()));

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

    public function testClearMessageBag()
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
    }

    public function testMessageBagToString()
    {
        $this->clearMessageBag();
        $this->addLogMessageToMessageBag('Message 1');

        $messageBag = ZugferdMailMessageBag::factory();
        $messageBagAsString = (string)$messageBag;
        $messageBagAsJson = $messageBag->toJson();

        $this->assertIsString($messageBagAsString);
        $this->assertIsString($messageBagAsJson);
        $this->assertEquals($messageBagAsJson, $messageBagAsString);

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
}
