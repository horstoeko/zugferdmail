<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

/**
 * Trait representing a collection of output helpers
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailStringHelper
{
    /**
     * Truncate a string. If string is longer than $maxLength, ellipsis will be added
     *
     * @param  string $string
     * @param  int    $maxLength
     * @return string
     */
    protected function zfMailTruncateString(string $string, int $maxLength): string
    {
        return strlen($string) > $maxLength - 3 ? substr($string, 0, $maxLength - 3) . '...' : $string;
    }
}
