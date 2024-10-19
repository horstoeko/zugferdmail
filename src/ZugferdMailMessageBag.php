<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail;

use DateTime;
use Throwable;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;

/**
 * Class representing the message bag / Log
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailMessageBag
{
    /**
     * Singleton
     *
     * @var ZugferdMailMessageBag
     */
    protected static $instance = null;

    /**
     * Message container
     *
     * @var array
     */
    private $messageContainer = [];

    /**
     * Factory for the message bag
     *
     * @return ZugferdMailMessageBag
     */
    public static function factory(): ZugferdMailMessageBag
    {
        if (!static::$instance) {
            static::$instance = new ZugferdMailMessageBag();
        }

        return static::$instance;
    }

    /**
     * Returns the config as a JSON string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Get the messages as JSON string
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->messageContainer, JSON_PRETTY_PRINT);
    }

    /**
     * Clear the message bag
     *
     * @return ZugferdMailMessageBag
     */
    public function clear(): ZugferdMailMessageBag
    {
        $this->messageContainer = [];

        return $this;
    }

    /**
     * Add a messsage with type, message text and additional data
     * to internal message container
     *
     * @param  string $type
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addMessage(string $type, string $source, string $message, array $additionalData = []): ZugferdMailMessageBag
    {
        $this->messageContainer[] = [
            "type" => $type,
            "source" => $source,
            "message" => $message,
            "additionalData" => $additionalData,
            "datetime" => new DateTime()
        ];

        return $this;
    }

    /**
     * Add a message of type "Log"
     *
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addLogMessage(string $source, string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_LOG, $source, $message, $additionalData);

        return $this;
    }

    /**
     * Add a message of type "Log (Secondary)"
     *
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addLogSecondaryMessage(string $source, string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_LOG_SECONDARY, $source, $message, $additionalData);

        return $this;
    }

    /**
     * Add a message of type "Warning"
     *
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addWarningMessage(string $source, string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_WARN, $source, $message, $additionalData);

        return $this;
    }

    /**
     * Add a message of type "Error"
     *
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addErrorMessage(string $source, string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_ERROR, $source, $message, $additionalData);

        return $this;
    }

    /**
     * Add a message of type "Success"
     *
     * @param  string $source
     * @param  string $message
     * @param  array  $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addSuccessMessage(string $source, string $message, array $additionalData = [])
    {
        $this->addMessage(ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS, $source, $message, $additionalData);

        return $this;
    }

    /**
     * Add a message of type "Error" by the given exception/throwable
     *
     * @param  Throwable $throwable
     * @param  string    $source
     * @param  array     $additionalData
     * @return ZugferdMailMessageBag
     */
    public function addThrowable(Throwable $throwable, string $source, array $additionalData = []): ZugferdMailMessageBag
    {
        $additionalData = array_merge(
            $additionalData,
            [
                "errno" => $throwable->getCode(),
                "errfile" => $throwable->getFile(),
                "errline" => $throwable->getLine(),
                "errtrace" => $throwable->getTraceAsString(),
            ]
        );

        $this->addErrorMessage($source, $throwable->getMessage(), $additionalData);

        return $this;
    }

    /**
     * Get messages from message container filtered by message type
     *
     * @param  string $messageType
     * @return array
     */
    private function getMessageBagFilteredByType(string $messageType): array
    {
        return array_map(
            function ($data) {
                return $data["message"];
            },
            array_filter(
                $this->messageContainer,
                function ($data) use ($messageType) {
                    return $data['type'] == $messageType;
                }
            )
        );
    }

    /**
     * Returns an array of all log messages
     *
     * @return array
     */
    public function getLogMessages(): array
    {
        return $this->getMessageBagFilteredByType(ZugferdMailMessageBagType::MESSAGETYPE_LOG);
    }

    /**
     * Returns true if __no__ log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoLogMessages(): bool
    {
        return empty($this->getLogMessages());
    }

    /**
     * Returns true if log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasLogMessages(): bool
    {
        return !$this->hasNoLogMessages();
    }

    /**
     * Returns an array of all secondary log messages
     *
     * @return array
     */
    public function getLogSecondaryMessages(): array
    {
        return $this->getMessageBagFilteredByType(ZugferdMailMessageBagType::MESSAGETYPE_LOG_SECONDARY);
    }

    /**
     * Returns true if __no__ secondary log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoLogSecondaryMessages(): bool
    {
        return empty($this->getLogSecondaryMessages());
    }

    /**
     * Returns true if secondary log messages are present otherwise false
     *
     * @return boolean
     */
    public function hasLogSecondaryMessages(): bool
    {
        return !$this->hasNoLogSecondaryMessages();
    }

    /**
     * Returns an array of all warning messages
     *
     * @return array
     */
    public function getWarningMessages(): array
    {
        return $this->getMessageBagFilteredByType(ZugferdMailMessageBagType::MESSAGETYPE_WARN);
    }

    /**
     * Returns true if __no__ warning messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoWarningMessages(): bool
    {
        return empty($this->getWarningMessages());
    }

    /**
     * Returns true if warning messages are present otherwise false
     *
     * @return boolean
     */
    public function hasWarningMessages(): bool
    {
        return !$this->hasNoWarningMessages();
    }

    /**
     * Returns an array of all error messages
     *
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->getMessageBagFilteredByType(ZugferdMailMessageBagType::MESSAGETYPE_ERROR);
    }

    /**
     * Returns true if __no__ error messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoErrorMessages(): bool
    {
        return empty($this->getErrorMessages());
    }

    /**
     * Returns true if error messages are present otherwise false
     *
     * @return boolean
     */
    public function hasErrorMessages(): bool
    {
        return !$this->hasNoErrorMessages();
    }

    /**
     * Returns an array of all success messages
     *
     * @return array
     */
    public function getSuccessMessages(): array
    {
        return $this->getMessageBagFilteredByType(ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS);
    }

    /**
     * Returns true if __no__ success messages are present otherwise false
     *
     * @return boolean
     */
    public function hasNoSuccessMessages(): bool
    {
        return empty($this->getSuccessMessages());
    }

    /**
     * Returns true if success messages are present otherwise false
     *
     * @return boolean
     */
    public function hasSuccessMessages(): bool
    {
        return !$this->hasNoSuccessMessages();
    }

    /**
     * Returns all messages from message container
     *
     * @return array
     */
    public function getAllMessages(): array
    {
        return $this->messageContainer;
    }
}
