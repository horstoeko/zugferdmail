<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\helpers;

/**
 * Class representing a collection of string tools
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailStringHelper
{
    /**
     * Truncate a string if longer than $maxLength and add ellipsis
     *
     * @param  string  $string
     * @param  integer $maxLength
     * @return string
     */
    public static function truncateString(string $string, int $maxLength): string
    {
        return strlen($string) > $maxLength - 3 ? substr($string, 0, $maxLength - 3) . '...' : $string;
    }
}
