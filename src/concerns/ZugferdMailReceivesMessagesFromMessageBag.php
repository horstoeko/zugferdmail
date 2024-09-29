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
 * Trait representing general facillities to send
 * messages to the message bag
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailReceivesMessagesFromMessageBag
{
    /**
     * Returns an array of all log messages
     *
     * @return array
     */
    public function getLogMessagesFromMessageBag(): array
    {
        return ZugferdMailMessageBag::factory()->getLogMessages();
    }

    /**
     * Returns true if __no__ log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoLogMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasNoLogMessages();
    }

    /**
     * Returns true if log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasLogMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasLogMessages();
    }

    /**
     * Returns an array of all warning messages
     *
     * @return array
     */
    public function getWarningMessagesFromMessageBag(): array
    {
        return ZugferdMailMessageBag::factory()->getWarningMessages();
    }

    /**
     * Returns true if __no__ warning messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoWarningMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasNoWarningMessages();
    }

    /**
     * Returns true if warning messages are present otherwise false
     *
     * @return boolean
     */
    public function hasWarningMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasWarningMessages();
    }

    /**
     * Returns an array of all error messages
     *
     * @return array
     */
    public function getErrorMessagesFromMessageBag(): array
    {
        return ZugferdMailMessageBag::factory()->getErrorMessages();
    }

    /**
     * Returns true if __no__ error messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoErrorMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasNoErrorMessages();
    }

    /**
     * Returns true if error messages are present otherwise false
     *
     * @return boolean
     */
    public function hasErrorMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasErrorMessages();
    }

    /**
     * Returns an array of all success messages
     *
     * @return array
     */
    public function getSuccessMessagesFromMessageBag(): array
    {
        return ZugferdMailMessageBag::factory()->getSuccessMessages();
    }

    /**
     * Returns true if __no__ success messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoSuccessMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasNoSuccessMessages();
    }

    /**
     * Returns true if success messages are present otherwise false
     *
     * @return boolean
     */
    public function hasSuccessMessagesInMessageBag(): bool
    {
        return ZugferdMailMessageBag::factory()->hasSuccessMessages();
    }

    /**
     * Returns all messages from message container
     *
     * @return array
     */
    public function getAllMessagesFromMessageBag(): array
    {
        return ZugferdMailMessageBag::factory()->getAllMessages();
    }
}
