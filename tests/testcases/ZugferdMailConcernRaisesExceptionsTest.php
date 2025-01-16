<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailRaisesExceptions;
use horstoeko\zugferdmail\tests\TestCase;
use RuntimeException;

class ZugferdMailConcernRaisesExceptionsTest extends TestCase
{
    use ZugferdMailRaisesExceptions;

    protected const EXCEPTIONMESSAGE = 'Message for RuntimeException';

    public function testRaiseExceptionClassIfWithTrue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(static::EXCEPTIONMESSAGE);

        $this->raiseExceptionClassIf(true, RuntimeException::class, static::EXCEPTIONMESSAGE);
    }

    public function testRaiseRuntimeExceptionClassIfWithTrue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(static::EXCEPTIONMESSAGE);

        $this->raiseRuntimeExceptionIf(true, static::EXCEPTIONMESSAGE);
    }
}
