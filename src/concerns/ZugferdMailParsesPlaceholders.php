<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\helpers\ZugferdMailPlaceholderHelper;

/**
 * Trait representing general facillities to parse a string
 * which contains placeholders
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailParsesPlaceholders
{
    /**
     * Parses a string ($subject) defined by a ZugferdDocumentReader
     *
     * @param  ZugferdDocumentReader $document
     * @param  string                $subject
     * @return string
     */
    protected function parsePlaceholdersByZugferdDocumentReader(ZugferdDocumentReader $document, string $subject): string
    {
        return ZugferdMailPlaceholderHelper::fromZugferdDocumentReader($document)->parseString($subject);
    }
}
