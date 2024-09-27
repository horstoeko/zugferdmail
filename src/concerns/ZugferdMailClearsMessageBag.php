<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use horstoeko\zugferdmail\ZugferdMailMessageBag;

/**
 * Trait representing general facillities clear the message bag
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailClearsMessageBag
{
    /**
     * Clear the message bag
     *
     * @return static
     */
    public function clearMessageBag()
    {
        ZugferdMailMessageBag::factory()->clear();

        return $this;
    }
}
