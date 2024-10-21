<?php

namespace horstoeko\zugferdmail\tests\testcases;

use InvalidArgumentException;
use horstoeko\zugferdmail\tests\TestCase;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerCli;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerCopyMessage;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerMoveMessage;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerDeleteMessage;

class BuiltinHandlersTest extends TestCase
{
    public function testCreateBuiltInHandlerCli(): void
    {
        $handler = new ZugferdMailHandlerCli();

        $this->assertTrue(true, 'No exception should be raised');
    }

    public function testCreateBuiltInHandlerCopyMessageWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The destination folder must not be empty');

        $handler = new ZugferdMailHandlerCopyMessage('');
    }

    public function testCreateBuiltInHandlerCopyMessageWithFolder(): void
    {
        $handler = new ZugferdMailHandlerCopyMessage('INBOX.read');

        $this->assertTrue(true, 'No exception should be raised');
    }

    public function testCreateBuiltInHandlerMoveMessageWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The destination folder must not be empty');

        $handler = new ZugferdMailHandlerMoveMessage('');
    }

    public function testCreateBuiltInHandlerMoveMessageWithFolder(): void
    {
        $handler = new ZugferdMailHandlerMoveMessage('INBOX.read');

        $this->assertTrue(true, 'No exception should be raised');
    }

    public function testCreateBuiltInHandlerDeleteMessage(): void
    {
        $handler = new ZugferdMailHandlerDeleteMessage();

        $this->assertTrue(true, 'No exception should be raised');
    }
}
