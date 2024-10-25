<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\config;

use stdClass;
use Throwable;
use RuntimeException;
use ReflectionException;
use InvalidArgumentException;
use Swaggest\JsonSchema\Schema;
use Webklex\PHPIMAP\ClientManager;
use horstoeko\stringmanagement\FileUtils;

/**
 * Class representing the config for the Zugferd MailReader
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailConfig
{
    /**
     * The date format to use
     *
     * @var string
     */
    protected $dateFormat = "d-M-Y";

    /**
     * UBL support enabled (using horstoeko/zugferdublbridge)
     *
     * @var boolean
     */
    protected $ublSupportEnabled = false;

    /**
     * Symfony validation enabled
     *
     * @var boolean
     */
    protected $symfonyValidationEnabled = false;

    /**
     * XSD validation enabled
     *
     * @var boolean
     */
    protected $xsdValidationEnabled = false;

    /**
     * Kosit validation enabled (JAVA is required)
     *
     * @var boolean
     */
    protected $kositValidationEnabled = false;

    /**
     * Look for unseen messages
     *
     * @var boolean
     */
    protected $processUnseenMessagesOnlyEnabled = false;

    /**
     * List of defined accounts
     *
     * @var array<ZugferdMailAccount>
     */
    protected $accounts = [];

    /**
     * Get the date format to use
     *
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * Set the date format to use
     *
     * @param  string $dateFormat
     * @return ZugferdMailConfig
     * @throws InvalidArgumentException
     */
    public function setDateFormat(string $dateFormat): ZugferdMailConfig
    {
        if (!in_array($dateFormat, ["d-M-Y", "d-M-y", "d M y"])) {
            throw new InvalidArgumentException(sprintf("%s is not a valid date format", $dateFormat));
        }

        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Returns true if the UBL-Syntax support is enabled
     *
     * @return boolean
     */
    public function getUblSupportEnabled(): bool
    {
        return $this->ublSupportEnabled;
    }

    /**
     * Activate or deactivate support for UBL-Syntax
     *
     * @param  boolean $ublSupportEnabled
     * @return ZugferdMailConfig
     */
    public function setUblSupportEnabled(bool $ublSupportEnabled): ZugferdMailConfig
    {
        $this->ublSupportEnabled = $ublSupportEnabled;

        return $this;
    }

    /**
     * Activate the UBL-Syntax support
     *
     * @return ZugferdMailConfig
     */
    public function activateUblSupport(): ZugferdMailConfig
    {
        return $this->setUblSupportEnabled(true);
    }

    /**
     * Deactivate the UBL-Syntax support
     *
     * @return ZugferdMailConfig
     */
    public function deactivateUblSupport(): ZugferdMailConfig
    {
        return $this->setUblSupportEnabled(false);
    }

    /**
     * Returns true if the Symfony validation is enabled
     *
     * @return boolean
     */
    public function getSymfonyValidationEnabled(): bool
    {
        return $this->symfonyValidationEnabled;
    }

    /**
     * Activate or deactivate Symfony validation
     *
     * @param  boolean $symfonyValidationEnabled
     * @return ZugferdMailConfig
     */
    public function setSymfonyValidationEnabled(bool $symfonyValidationEnabled): ZugferdMailConfig
    {
        $this->symfonyValidationEnabled = $symfonyValidationEnabled;

        return $this;
    }

    /**
     * Activate the Symfony validation
     *
     * @return ZugferdMailConfig
     */
    public function activateSymfonyValidation(): ZugferdMailConfig
    {
        return $this->setSymfonyValidationEnabled(true);
    }

    /**
     * Deactivate the Symfony validation
     *
     * @return ZugferdMailConfig
     */
    public function deactivateSymfonyValidation(): ZugferdMailConfig
    {
        return $this->setSymfonyValidationEnabled(false);
    }

    /**
     * Returns true if the XSD validation is enabled
     *
     * @return boolean
     */
    public function getXsdValidationEnabled(): bool
    {
        return $this->xsdValidationEnabled;
    }

    /**
     * Activate or deactivate XSD validation
     *
     * @param  boolean $xsdValidationEnabled
     * @return ZugferdMailConfig
     */
    public function setXsdValidationEnabled(bool $xsdValidationEnabled): ZugferdMailConfig
    {
        $this->xsdValidationEnabled = $xsdValidationEnabled;

        return $this;
    }

    /**
     * Activate the XSD validation
     *
     * @return ZugferdMailConfig
     */
    public function activateXsdValidation(): ZugferdMailConfig
    {
        return $this->setXsdValidationEnabled(true);
    }

    /**
     * Deactivate the XSD validation
     *
     * @return ZugferdMailConfig
     */
    public function deactivateXsdValidation(): ZugferdMailConfig
    {
        return $this->setXsdValidationEnabled(false);
    }

    /**
     * Returns true if the Kosit validation is enabled
     *
     * @return boolean
     */
    public function getKositValidationEnabled(): bool
    {
        return $this->kositValidationEnabled;
    }

    /**
     * Activate or deactivate Kosit validation
     *
     * @param  boolean $kositValidationEnabled
     * @return ZugferdMailConfig
     */
    public function setKositValidationEnabled(bool $kositValidationEnabled): ZugferdMailConfig
    {
        $this->kositValidationEnabled = $kositValidationEnabled;

        return $this;
    }

    /**
     * Activate the Kosit validation
     *
     * @return ZugferdMailConfig
     */
    public function activateKositValidation(): ZugferdMailConfig
    {
        return $this->setKositValidationEnabled(true);
    }

    /**
     * Deactivate the Kosit validation
     *
     * @return ZugferdMailConfig
     */
    public function deactivateKositValidation(): ZugferdMailConfig
    {
        return $this->setKositValidationEnabled(false);
    }

    /**
     * Returns true if only unssen messages are processed, otherwise false
     *
     * @return boolean
     */
    public function getProcessUnseenMessagesOnlyEnabled(): bool
    {
        return $this->processUnseenMessagesOnlyEnabled;
    }

    /**
     * Activate or deactivate the processing only of unseen messages
     *
     * @param  boolean $processUnseenMessagesOnly
     * @return ZugferdMailConfig
     */
    public function setProcessUnseenMessagesOnlyEnabled(bool $processUnseenMessagesOnly): ZugferdMailConfig
    {
        $this->processUnseenMessagesOnlyEnabled = $processUnseenMessagesOnly;

        return $this;
    }

    /**
     * Activate the processing only of unseen messages
     *
     * @return ZugferdMailConfig
     */
    public function activateProcessUnseenMessagesOnly(): ZugferdMailConfig
    {
        return $this->setProcessUnseenMessagesOnlyEnabled(true);
    }

    /**
     * Deactivate the processing only of unseen messages
     *
     * @return ZugferdMailConfig
     */
    public function deactivateProcessUnseenMessagesOnly(): ZugferdMailConfig
    {
        return $this->setProcessUnseenMessagesOnlyEnabled(false);
    }

    /**
     * Add an account definition
     *
     * @param  string        $identifier
     * @param  string        $host
     * @param  integer       $port
     * @param  string        $protocol
     * @param  string|false  $encryption
     * @param  boolean       $validateCert
     * @param  string        $username
     * @param  string        $password
     * @param  string|null   $authentication
     * @param  integer       $timeout
     * @param  array<string> $foldersToWatch
     * @param  array<string> $mimeTypesToWatch
     * @return ZugferdMailAccount
     */
    public function addAccount(string $identifier, string $host, int $port, string $protocol, $encryption, bool $validateCert, string $username, string $password, ?string $authentication = null, int $timeout = 30, array $foldersToWatch = [], array $mimeTypesToWatch = []): ZugferdMailAccount
    {
        $account = new ZugferdMailAccount();

        $account->setIdentifier($identifier);
        $account->setHost($host);
        $account->setPort($port);
        $account->setProtocol($protocol);
        $account->setEncryption($encryption);
        $account->setValidateCert($validateCert);
        $account->setUsername($username);
        $account->setPassword($password);
        $account->setAuthentication($authentication);
        $account->setTimeout($timeout);
        $account->setFoldersToWatch($foldersToWatch);
        $account->setMimeTypesToWatch($mimeTypesToWatch);

        $this->addAccountObject($account);

        return $account;
    }

    /**
     * Add an mail account object
     *
     * @param  ZugferdMailAccount $account
     * @return ZugferdMailConfig
     */
    public function addAccountObject(ZugferdMailAccount $account): ZugferdMailConfig
    {
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove an account definition by it's identifier
     *
     * @param  string $identifier
     * @return ZugferdMailConfig
     */
    public function removeAccount(string $identifier): ZugferdMailConfig
    {
        $this->accounts = array_filter(
            $this->accounts,
            function ($account) use ($identifier) {
                return strcasecmp($account->getIdentifier(), $identifier) != 0;
            }
        );

        return $this;
    }

    /**
     * Get the list of defined accounts
     *
     * @return array<ZugferdMailAccount>
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * Build the client manager
     *
     * @return ClientManager
     */
    public function makeClientManager(): ClientManager
    {
        $config = [];

        $config['date_format'] = $this->getDateFormat();
        $config['default'] = false;

        foreach ($this->accounts as $account) {
            $config['accounts'][$account->getIdentifier()] = $account->getAccountDefinition();
        }

        return new ClientManager($config);
    }

    /**
     * Loads a configuration from a file. The file must exist.
     *
     * @param  string $filename
     * @return ZugferdMailConfig
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ReflectionException
     */
    public static function loadFromFile(string $filename): ZugferdMailConfig
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException(sprintf("The file %s does not exist.", $filename));
        }

        $jsonString = file_get_contents($filename);

        if ($jsonString === false) {
            throw new RuntimeException(sprintf("Cannot read the configuration file %s.", $filename));
        }

        $jsonObject = json_decode($jsonString);

        if (is_null($jsonObject)) {
            throw new RuntimeException(sprintf("The file %s does not seem to be a valid json.", $filename));
        }

        if (!static::validateConfig($jsonObject)) {
            throw new RuntimeException(sprintf("The file %s could not be identified as a valid JSON file", $filename));
        }

        $config = new ZugferdMailConfig;
        $config->setDateFormat($jsonObject->dateFormat);
        $config->setUblSupportEnabled($jsonObject->ublSupportEnabled);
        $config->setSymfonyValidationEnabled($jsonObject->symfonyValidationEnabled);
        $config->setXsdValidationEnabled($jsonObject->xsdValidationEnabled);
        $config->setKositValidationEnabled($jsonObject->kositValidationEnabled);
        $config->setProcessUnseenMessagesOnlyEnabled($jsonObject->processUnseenMessagesOnlyEnabled);

        foreach ($jsonObject->accounts as $accountDefinition) {
            $account = new ZugferdMailAccount();
            $account->setIdentifier($accountDefinition->identifier);
            $account->setHost($accountDefinition->host);
            $account->setPort($accountDefinition->port);
            $account->setProtocol($accountDefinition->protocol);
            $account->setEncryption($accountDefinition->encryption);
            $account->setValidateCert($accountDefinition->validateCert);
            $account->setUsername($accountDefinition->username);
            $account->setPassword($accountDefinition->password);
            $account->setAuthentication($accountDefinition->authentication);
            $account->setTimeout($accountDefinition->timeout);
            $account->setFoldersToWatch($accountDefinition->foldersToWatch);
            $account->setMimeTypesToWatch($accountDefinition->mimeTypesToWatch);

            foreach ($accountDefinition->handlers as $accountHandlerDefinition) {
                $reflection = new \ReflectionClass($accountHandlerDefinition->classname);
                $account->addHandler($reflection->newInstanceArgs(array_values(get_object_vars($accountHandlerDefinition->properties))));
            }

            $config->addAccountObject($account);
        }

        return $config;
    }

    /**
     * Save the configuration to a file
     *
     * @param  string $filename
     * @return ZugferdMailConfig
     * @throws RuntimeException
     */
    public function saveToFile(string $filename): ZugferdMailConfig
    {
        $jsonObject = new stdClass;
        $jsonObject->dateFormat = $this->getDateFormat();
        $jsonObject->ublSupportEnabled = $this->getUblSupportEnabled();
        $jsonObject->symfonyValidationEnabled = $this->getSymfonyValidationEnabled();
        $jsonObject->xsdValidationEnabled = $this->getXsdValidationEnabled();
        $jsonObject->kositValidationEnabled = $this->getKositValidationEnabled();
        $jsonObject->processUnseenMessagesOnlyEnabled = $this->getProcessUnseenMessagesOnlyEnabled();
        $jsonObject->accounts = [];

        foreach ($this->getAccounts() as $account) {
            $jsonAccountObject = new stdClass;
            $jsonAccountObject->identifier = $account->getIdentifier();
            $jsonAccountObject->host = $account->getHost();
            $jsonAccountObject->port = $account->getPort();
            $jsonAccountObject->protocol = $account->getProtocol();
            $jsonAccountObject->encryption = $account->getEncryption();
            $jsonAccountObject->validateCert = $account->getValidateCert();
            $jsonAccountObject->username = $account->getUsername();
            $jsonAccountObject->password = $account->getPassword();
            $jsonAccountObject->authentication = $account->getAuthentication();
            $jsonAccountObject->timeout = $account->getTimeout();
            $jsonAccountObject->foldersToWatch = $account->getFoldersTowatch();
            $jsonAccountObject->mimeTypesToWatch = $account->getMimeTypesToWatch();
            $jsonAccountObject->handlers = [];

            foreach ($account->getHandlers() as $handler) {
                $jsonAccountHandlerObject = new stdClass;
                $jsonAccountHandlerObject->classname = get_class($handler);
                $jsonAccountHandlerObject->properties = new stdClass;

                $reflection = new \ReflectionClass($handler);
                $reflectionConstructor = $reflection->getConstructor();

                if (!is_null($reflectionConstructor)) {
                    foreach ($reflectionConstructor->getParameters() as $reflectionConstructorParameter) {
                        $argumentName = $reflectionConstructorParameter->getName();
                        $argumentGetterMethodName = "get" . ucFirst($argumentName);

                        if (!$reflection->hasMethod($argumentGetterMethodName)) {
                            throw new RuntimeException(sprintf("No method %s for property %s found", $argumentGetterMethodName, $argumentName));
                        }

                        $jsonAccountHandlerObject->properties->$argumentName = $handler->$argumentGetterMethodName();
                    };
                }

                $jsonAccountObject->handlers[] = $jsonAccountHandlerObject;
            }

            $jsonObject->accounts[] = $jsonAccountObject;
        }

        if (!static::validateConfig($jsonObject)) {
            throw new RuntimeException("The content of the generated config is not valid.");
        }

        if (!is_dir(FileUtils::getFileDirectory($filename))) {
            throw new RuntimeException(sprintf("Directory of file %s does not exist.", $filename));
        }

        if (file_put_contents($filename, json_encode($jsonObject, JSON_PRETTY_PRINT)) === false) {
            throw new RuntimeException(sprintf("Cannot save to file %s.", $filename));
        }

        return $this;
    }

    /**
     * Validates a config file
     *
     * @param  object $jsonObject
     * @return bool
     */
    protected static function validateConfig($jsonObject): bool
    {
        $result = true;

        $schemaJson = file_get_contents(dirname(__FILE__) . "/schema.json");

        try {
            Schema::import(
                json_decode($schemaJson),
            )->in(
                $jsonObject,
            );
        } catch (Throwable $e) {
            $result = false;
        }

        return $result;
    }
}
