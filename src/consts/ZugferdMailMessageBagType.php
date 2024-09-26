<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\consts;

class ZugferdMailMessageBagType
{
    /**
     * Identifier for a message of type __info__
     *
     * @var string
     */
    public const MESSAGETYPE_LOG = "info";

    /**
     * Identifier for a message of type __warning__
     *
     * @var string
     */
    public const MESSAGETYPE_WARN = "warn";

    /**
     * Identifier for a message of type __error__
     *
     * @var string
     */
    public const MESSAGETYPE_ERROR = "error";
}
