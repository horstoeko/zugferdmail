<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use horstoeko\zugferdmail\ZugferdMailMessageBag;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;

/**
 * Trait representing general facillities to send
 * messages to the message bag
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailSendsMessagesToMessageBag
{
    /**
     * Helper function for adding messages to message bag
     *
     * @param  string $type
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addMessage(string $type, string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addMessage($type, static::class, $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a log entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addLogMessage(string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_LOG, $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a warning entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addWarningMessage(string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_WARN, $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a error entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addErrorMessage(string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_ERROR, $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a success entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addSuccessMessage(string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS, $message, $additionalData);

        return $this;
    }
}
