<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\config;

use stdClass;
use RuntimeException;
use InvalidArgumentException;
use Webklex\PHPIMAP\ClientManager;

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
        $this->setUblSupportEnabled(true);

        return $this;
    }

    /**
     * Deactivate the UBL-Syntax support
     *
     * @return ZugferdMailConfig
     */
    public function deactivateUblSupport(): ZugferdMailConfig
    {
        $this->setUblSupportEnabled(false);

        return $this;
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
        $account->setMmimeTypesToWatch($mimeTypesToWatch);

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
     * Loads a configuration from a file.
     * The file must exist.
     *
     * @param  string $filename
     * @return ZugferdMailConfig
     */
    public static function loadFromFile(string $filename): ZugferdMailConfig
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException(sprintf("The file %s does not exist.", $filename));
        }

        $jsonString = file_get_contents($filename);

        if ($jsonString === false) {
            throw new RuntimeException(sprintf("Cannot read the file %s.", $filename));
        }

        $jsonObject = json_decode($jsonString);

        $config = new ZugferdMailConfig;
        $config->setDateFormat($jsonObject->dateFormat);
        $config->setUblSupportEnabled($jsonObject->ublSupportEnabled);

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
            $account->setMmimeTypesToWatch($accountDefinition->mimeTypesToWatch);

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
     */
    public function saveToFile(string $filename): ZugferdMailConfig
    {
        $jsonObject = new stdClass;
        $jsonObject->dateFormat = $this->getDateFormat();
        $jsonObject->ublSupportEnabled = $this->getUblSupportEnabled();
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
            $jsonAccountObject->mimeTypesToWatch = $account->getMmimeTypesToWatch();
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

        if (file_put_contents($filename, json_encode($jsonObject, JSON_PRETTY_PRINT)) === false) {
            throw new RuntimeException(sprintf("Cannot save to file %s.", $filename));
        }

        return $this;
    }
}
