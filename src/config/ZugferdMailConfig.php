<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\config;

use InvalidArgumentException;
use Webklex\PHPIMAP\ClientManager;

/**
 * Class representing the config for the Zugferd MailReader
 *
 * @category ZugferdMailReader
 * @package  ZugferdMailReader
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
}
