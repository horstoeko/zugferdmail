<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use RuntimeException;

/**
 * Trait representing general facillities to raise
 * exceptions of several types
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailRaisesExceptions
{
    /**
     * Raise exception defined by $class if $condition is evaludated to trze
     *
     * @param  bool   $condidition
     * @param  string $exceptionClass
     * @param  string $message
     * @return void
     */
    protected function raiseExceptionClassIf(bool $condidition, string $exceptionClass, string $message = ""): void
    {
        if ($condidition !== true) {
            return;
        }

        throw new $exceptionClass($message);
    }

    /**
     * Raise Runtime Exception if $condition is evaludated to trze
     *
     * @param  boolean $condidition
     * @param  string  $message
     * @return void
     */
    protected function raiseRuntimeExceptionIf(bool $condidition, string $message = ""): void
    {
        $this->raiseExceptionClassIf($condidition, RuntimeException::class, $message);
    }
}
