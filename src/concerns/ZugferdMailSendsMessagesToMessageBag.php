<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use Throwable;
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
    protected function addMessageToMessageBag(string $type, string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addMessage($type, $this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a log entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addLogMessageToMessageBag(string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addLogMessage($this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a secondary log entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addLogSecondaryMessageToMessageBag(string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addLogSecondaryMessage($this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a warning entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addWarningMessageToMessageBag(string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addWarningMessage($this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a error entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addErrorMessageToMessageBag(string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addErrorMessage($this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding a success entry to message bag
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addSuccessMessageToMessageBag(string $message, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addSuccessMessage($this->getMessageBagSourceFromClassname(), $message, $additionalData);

        return $this;
    }

    /**
     * Helper function for adding an throwable (Exception) to message bag
     *
     * @param  Throwable $throwable
     * @param  array     $additionalData
     * @return static
     */
    protected function addThrowableToMessageBag(Throwable $throwable, array $additionalData = [])
    {
        ZugferdMailMessageBag::factory()->addThrowable($throwable, $this->getMessageBagSourceFromClassname(), $additionalData);

        return $this;
    }

    /**
     * Helper function for adding messages to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $type
     * @param  string  $message
     * @param  array   $additionalData
     * @return static
     */
    protected function addMessageToMessageBagIf(bool $condition, string $type, string $message, array $additionalData = [])
    {
        return $condition ? $this->addMessageToMessageBag($type, $message, $additionalData) : $this;
    }

    /**
     * Helper function for adding a log entry to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $message
     * @param  array   $additionalData
     * @return static
     */
    protected function addLogMessageToMessageBagIf(bool $condition, string $message, array $additionalData = [])
    {
        return $condition ? $this->addLogMessageToMessageBag($message, $additionalData) : $this;
    }

    /**
     * Helper function for adding a secondary log entry to message bag if $condition evaluates to true
     *
     * @param  string $message
     * @param  array  $additionalData
     * @return static
     */
    protected function addLogSecondaryMessageToMessageBagIf(bool $condition, string $message, array $additionalData = [])
    {
        return $condition ? $this->addLogSecondaryMessageToMessageBag($message, $additionalData) : $this;
    }

    /**
     * Helper function for adding a warning entry to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $message
     * @param  array   $additionalData
     * @return static
     */
    protected function addWarningMessageToMessageBagIf(bool $condition, string $message, array $additionalData = [])
    {
        return $condition ? $this->addWarningMessageToMessageBag($message, $additionalData) : $this;
    }

    /**
     * Helper function for adding a error entry to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $message
     * @param  array   $additionalData
     * @return static
     */
    protected function addErrorMessageToMessageBagIf(bool $condition, string $message, array $additionalData = [])
    {
        return $condition ? $this->addErrorMessageToMessageBag($message, $additionalData) : $this;
    }

    /**
     * Helper function for adding a success entry to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $message
     * @param  array   $additionalData
     * @return static
     */
    protected function addSuccessMessageToMessageBagIf(bool $condition, string $message, array $additionalData = [])
    {
        return $condition ? $this->addSuccessMessageToMessageBag($message, $additionalData) : $this;
    }

    /**
     * Helper function for adding an throwable (Exception) to message bag if $condition evaluates to true
     *
     * @param  boolean   $condition
     * @param  Throwable $throwable
     * @param  array     $additionalData
     * @return static
     */
    protected function addThrowableToMessageBagIf(bool $condition, Throwable $throwable, array $additionalData = [])
    {
        return $condition ? $this->addThrowableToMessageBag($throwable, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple messages to message bag
     *
     * @param  string $type
     * @param  array  $messages
     * @param  array  $additionalData
     * @return static
     */
    protected function addMultipleMessagesToMessageBag(string $type, array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($type, $additionalData): void {
                $this->addMessageToMessageBag($type, $message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple log entries to message bag
     *
     * @param  array $messages
     * @param  array $additionalData
     * @return static
     */
    protected function addMultipleLogMessagesToMessageBag(array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($additionalData): void {
                $this->addLogMessageToMessageBag($message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple secondary log entries to message bag
     *
     * @param  array $messages
     * @param  array $additionalData
     * @return static
     */
    protected function addMultipleLogSecondaryMessagesToMessageBag(array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($additionalData): void {
                $this->addLogSecondaryMessageToMessageBag($message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple warning entries to message bag
     *
     * @param  array $messages
     * @param  array $additionalData
     * @return static
     */
    protected function addMultipleWarningMessagesToMessageBag(array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($additionalData): void {
                $this->addWarningMessageToMessageBag($message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple error entries to message bag
     *
     * @param  array $messages
     * @param  array $additionalData
     * @return static
     */
    protected function addMultipleErrorMessagesToMessageBag(array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($additionalData): void {
                $this->addErrorMessageToMessageBag($message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple success entries to message bag
     *
     * @param  array $messages
     * @param  array $additionalData
     * @return static
     */
    protected function addMultipleSuccessMessagesToMessageBag(array $messages, array $additionalData = [])
    {
        array_walk(
            $messages,
            function ($message) use ($additionalData): void {
                $this->addSuccessMessageToMessageBag($message, $additionalData);
            }
        );

        return $this;
    }

    /**
     * Helper function for adding multiple messages to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  string  $type
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleMessagesToMessageBagIf(bool $condition, string $type, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleMessagesToMessageBag($type, $messages, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple log entries to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleLogMessagesToMessageBagIf(bool $condition, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleLogMessagesToMessageBag($messages, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple secondary log entries to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleLogSecondaryMessagesToMessageBagIf(bool $condition, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleLogSecondaryMessagesToMessageBag($messages, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple warning entries to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleWarningMessagesToMessageBagIf(bool $condition, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleWarningMessagesToMessageBag($messages, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple error entries to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleErrorMessagesToMessageBagIf(bool $condition, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleErrorMessagesToMessageBag($messages, $additionalData) : $this;
    }

    /**
     * Helper function for adding multiple success entries to message bag if $condition evaluates to true
     *
     * @param  boolean $condition
     * @param  array   $messages
     * @param  array   $additionalData
     * @return static
     */
    protected function addMultipleSuccessMessagesToMessageBagIf(bool $condition, array $messages, array $additionalData = [])
    {
        return $condition ? $this->addMultipleSuccessMessagesToMessageBag($messages, $additionalData) : $this;
    }

    /**
     * Helper function for getting the source from a current classname
     *
     * @return string
     */
    private function getMessageBagSourceFromClassname(): string
    {
        $classname = explode('\\', static::class);

        return array_pop($classname);
    }
}
