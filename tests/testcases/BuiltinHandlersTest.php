<?php

namespace horstoeko\zugferdmail\tests\testcases;

use InvalidArgumentException;
use horstoeko\zugferdmail\tests\TestCase;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerCopyMessage;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerMoveMessage;

class BuiltinHandlersTest extends TestCase
{
    public function testCreateBuiltInHandlerCopyMessageWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The destination folder must not be empty');

        $handler = new ZugferdMailHandlerCopyMessage('');
    }

    public function testCreateBuiltInHandlerMoveMessageWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The destination folder must not be empty');

        $handler = new ZugferdMailHandlerMoveMessage('');
    }
}
