<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailRaisesExceptions;
use horstoeko\zugferdmail\tests\TestCase;
use RuntimeException;

class ConcernRaisesExceptionTest extends TestCase
{
    use ZugferdMailRaisesExceptions;

    protected const EXCEPTIONMESSAGE = 'Message for RuntimeException';

    public function testRaiseExceptionClassIfWithTrue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(static::EXCEPTIONMESSAGE);

        $this->raiseExceptionClassIf(true, RuntimeException::class, static::EXCEPTIONMESSAGE);
    }

    public function testRaiseExceptionClassIfWithFalse(): void
    {
        $this->assertTrue(true); // We need one assertion in this test

        $this->raiseExceptionClassIf(false, RuntimeException::class, static::EXCEPTIONMESSAGE);
    }

    public function testRaiseRuntimeExceptionClassIfWithTrue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(static::EXCEPTIONMESSAGE);

        $this->raiseRuntimeExceptionIf(true, static::EXCEPTIONMESSAGE);
    }

    public function testRaiseRuntimeExceptionClassIfWithFalse(): void
    {
        $this->assertTrue(true); // We need one assertion in this test

        $this->raiseRuntimeExceptionIf(false, static::EXCEPTIONMESSAGE);
    }
}
