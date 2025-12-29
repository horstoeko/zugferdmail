<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\config;

use InvalidArgumentException;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerAbstract;

/**
 * Class representing the mail account definition for the Zugferd MailReader
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailAccount
{
    /**
     * The account identifier
     *
     * @var string
     */
    protected $identifier = "";

    /**
     * The host to call
     *
     * @var string
     */
    protected $host = "";

    /**
     * The port on the host to use
     *
     * @var integer
     */
    protected $port = 0;

    /**
     * The Protocol you want to utilize
     *
     * @var string
     */
    protected $protocol = "imap";

    /**
     * Used encryption method. Use false to disable encryption.
     *
     * @var string|boolean
     */
    protected $encryption = "ssl";

    /**
     * The certificate gets validated by default. You can turn this off, by setting it to false.
     *
     * @var boolean
     */
    protected $validateCert = true;

    /**
     * Account username
     *
     * @var string
     */
    protected $username = "";

    /**
     * Account password
     *
     * @var string
     */
    protected $password = "";

    /**
     * Set it to oauth if you want to authenticate using oAuth. You may provide your access token as password
     *
     * @var string|null
     */
    protected $authentication = null;

    /**
     * The timeout
     *
     * @var integer
     */
    protected $timeout = 30;

    /**
     * Folders to watch
     *
     * @var string[]
     */
    protected $foldersToWatch = [];

    /**
     * Mime types to watch
     *
     * @var array
     */
    protected $mimeTypesToWatch = [];

    /**
     * Handler for found documents
     *
     * @var array<ZugferdMailHandlerAbstract>
     */
    protected $handlers = [];

    /**
     * Callbacks for found documents
     *
     * @var array
     */
    protected $callBacks = [];

    /**
     * Handler for the case when no documents were found
     *
     * @var array<ZugferdMailHandlerAbstract>
     */
    protected $handlersNoDocumentFound = [];

    /**
     * Callbacks for the case when no documents were found
     *
     * @var array
     */
    protected $callBacksNoDocumentFound = [];

    /**
     * Look for unseen messages only
     *
     * @var boolean
     */
    protected $unseenMessagesOnlyEnabled = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setIdentifier();
    }

    /**
     * Returns the account identifier
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Sets the account identifier
     *
     * @param  string $identifier
     * @return ZugferdMailAccount
     * @throws InvalidArgumentException
     */
    public function setIdentifier(string $identifier = ''): ZugferdMailAccount
    {
        if (empty(trim($identifier))) {
            $identifier = $this->createGuidForIdentifier();
        }

        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Returns the account's host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Sets the account's host
     *
     * @param  string $host
     * @return ZugferdMailAccount
     */
    public function setHost(string $host): ZugferdMailAccount
    {
        if (empty($host)) {
            throw new InvalidArgumentException("The host must not be empty");
        }

        $this->host = $host;

        return $this;
    }

    /**
     * Returns the account's port
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Sets the account's port
     *
     * @param  integer $port
     * @return ZugferdMailAccount
     */
    public function setPort(int $port): ZugferdMailAccount
    {
        if ($port < 0 || $port > 65535) {
            throw new InvalidArgumentException("The port must be between 0 and 65535");
        }

        $this->port = $port;

        return $this;
    }

    /**
     * Returns the protocol to use
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * Sets the protocol to use
     *
     * @param  string $protocol
     * @return ZugferdMailAccount
     */
    public function setProtocol(string $protocol): ZugferdMailAccount
    {
        if (!in_array($protocol, ['imap', 'legacy-imap', 'pop3', 'nntp'])) {
            throw new InvalidArgumentException(sprintf("The protocol must be one of imap, legacy-imap, pop3 or nntp, %s given", $protocol));
        }

        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Returns the encryption to use
     *
     * @return string|boolean
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * Set the encryption to use
     *
     * @param  string|boolean $encryption
     * @return ZugferdMailAccount
     */
    public function setEncryption($encryption): ZugferdMailAccount
    {
        if (!in_array($encryption, [false, "ssl", "tls", "starttls", "notls"])) {
            throw new InvalidArgumentException("The encryption must be false or one of ssl, tls, starttls, notls");
        }

        $this->encryption = $encryption;

        return $this;
    }

    /**
     * Returns true if the SSL certificates should be validated, otherwise fals
     *
     * @return boolean
     */
    public function getValidateCert(): bool
    {
        return $this->validateCert;
    }

    /**
     * Set to true if SSL ceritificates should be validates, otherwise to fals
     *
     * @param  boolean $validateCert
     * @return ZugferdMailAccount
     */
    public function setValidateCert(bool $validateCert): ZugferdMailAccount
    {
        $this->validateCert = $validateCert;

        return $this;
    }

    /**
     * Returns the account's username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Sets the accounts user name
     *
     * @param  string $username
     * @return ZugferdMailAccount
     */
    public function setUsername(string $username): ZugferdMailAccount
    {
        if (empty($username)) {
            throw new InvalidArgumentException("The username must be not empty");
        }

        $this->username = $username;

        return $this;
    }

    /**
     * Returns the account's password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Sets the account's password
     *
     * @param  string $password
     * @return ZugferdMailAccount
     */
    public function setPassword(string $password): ZugferdMailAccount
    {
        if (empty($password)) {
            throw new InvalidArgumentException("The password must be not empty");
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Returns the authentication method
     *
     * @return string|null
     */
    public function getAuthentication(): ?string
    {
        return $this->authentication;
    }

    /**
     * Sets the account's authentication methid
     *
     * @param  string|null $authentication
     * @return ZugferdMailAccount
     */
    public function setAuthentication(?string $authentication): ZugferdMailAccount
    {
        if (!is_null($authentication) && $authentication != "oauth") {
            throw new InvalidArgumentException("The authentication method must be one of null (NUL), oauth");
        }

        $this->authentication = $authentication;

        return $this;
    }

    /**
     * Returns the account's timeout
     *
     * @return integer
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Sets the account's timeout
     *
     * @param  integer $timeout
     * @return ZugferdMailAccount
     */
    public function setTimeout(int $timeout): ZugferdMailAccount
    {
        if ($timeout < 0) {
            throw new InvalidArgumentException("The authentication method must be zero or greater");
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Returns folders to watch for
     *
     * @return string[]
     */
    public function getFoldersTowatch(): array
    {
        return $this->foldersToWatch;
    }

    /**
     * Sets folders to watch for
     *
     * @param  array<string> $folders
     * @return ZugferdMailAccount
     */
    public function setFoldersToWatch(array $folders): ZugferdMailAccount
    {
        $this->foldersToWatch = array_filter($folders);

        return $this;
    }

    /**
     * Add a folder to watch
     *
     * @param  string $folderPath
     * @return ZugferdMailAccount
     */
    public function addFolderToWatch(string $folderPath): ZugferdMailAccount
    {
        if (empty($folderPath)) {
            throw new InvalidArgumentException("Path must not be empty");
        }

        $this->foldersToWatch[] = $folderPath;

        return $this;
    }

    /**
     * Returns a list of attachment mimetypes to watch
     *
     * @return array<string>
     */
    public function getMimeTypesToWatch(): array
    {
        return $this->mimeTypesToWatch;
    }

    /**
     * Sets a list of attachment mimetypes to watch
     *
     * @param  array<string> $mimeTypesToWatch
     * @return ZugferdMailAccount
     */
    public function setMimeTypesToWatch(array $mimeTypesToWatch): ZugferdMailAccount
    {
        $this->mimeTypesToWatch = array_filter($mimeTypesToWatch);

        return $this;
    }

    /**
     * Add a mimetype to watch
     *
     * @param  string $mimeTypesToWatch
     * @return ZugferdMailAccount
     */
    public function addMimeTypeToWatch(string $mimeTypesToWatch): ZugferdMailAccount
    {
        if (empty($mimeTypesToWatch)) {
            throw new InvalidArgumentException("Mimetype must not be empty");
        }

        $this->mimeTypesToWatch[] = $mimeTypesToWatch;

        return $this;
    }

    /**
     * Returns a list of handlers when a document was found
     *
     * @return array<ZugferdMailHandlerAbstract>
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Returns a list of callbacks when a document was found
     *
     * @return array<callable>
     */
    public function getCallbacks(): array
    {
        return $this->callBacks;
    }

    /**
     * Returns a list of handlers which are called in case when no documents were found
     *
     * @return array<ZugferdMailHandlerAbstract>
     */
    public function getHandlersNoDocumentFound(): array
    {
        return $this->handlersNoDocumentFound;
    }

    /**
     * Returns a list of callbacks which are called in case when no documents were found
     *
     * @return array<callable>
     */
    public function getCallbacksNoDocumentFound(): array
    {
        return $this->callBacksNoDocumentFound;
    }

    /**
     * Sets multiuple handlers to run when a document was found
     *
     * @param  array<ZugferdMailHandlerAbstract> $handlers
     * @return ZugferdMailAccount
     */
    public function setHandlers(array $handlers): ZugferdMailAccount
    {
        $this->handlers = array_filter(
            $handlers,
            function ($handler) {
                return $handler instanceof ZugferdMailHandlerAbstract;
            }
        );

        return $this;
    }

    /**
     * Sets multiple callbacks to run when a document was found
     *
     * @param  array<callable> $callbacks
     * @return ZugferdMailAccount
     */
    public function setCallbacks(array $callbacks): ZugferdMailAccount
    {
        $this->callBacks = array_filter(
            $callbacks,
            function ($callback) {
                return is_callable($callback);
            }
        );

        return $this;
    }

    /**
     * Addd a handler to call when a document was found
     *
     * @param  ZugferdMailHandlerAbstract $handler
     * @return ZugferdMailAccount
     */
    public function addHandler(?ZugferdMailHandlerAbstract $handler): ZugferdMailAccount
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Add a callback to call when a docuemnt was found
     *
     * @param  callable $callback
     * @return ZugferdMailAccount
     */
    public function addCallback(callable $callback): ZugferdMailAccount
    {
        $this->callBacks[] = $callback;

        return $this;
    }

    /**
     * Sets multiuple handlers which are called in the case when no documents were found
     *
     * @param  array<ZugferdMailHandlerAbstract> $handlers
     * @return ZugferdMailAccount
     */
    public function setHandlersNoDocumentFound(array $handlers): ZugferdMailAccount
    {
        $this->handlersNoDocumentFound = array_filter(
            $handlers,
            function ($handler) {
                return $handler instanceof ZugferdMailHandlerAbstract;
            }
        );

        return $this;
    }

    /**
     * Sets multiple callbacks which are called in the case when no documents were found
     *
     * @param  array<callable> $callbacks
     * @return ZugferdMailAccount
     */
    public function setCallbacksNoDocumentFound(array $callbacks): ZugferdMailAccount
    {
        $this->callBacksNoDocumentFound = array_filter(
            $callbacks,
            function ($callback) {
                return is_callable($callback);
            }
        );

        return $this;
    }

    /**
     * Addd a handler to call in the case when no documents were found
     *
     * @param  ZugferdMailHandlerAbstract $handler
     * @return ZugferdMailAccount
     */
    public function addHandlerNoDocumentFound(?ZugferdMailHandlerAbstract $handler): ZugferdMailAccount
    {
        $this->handlersNoDocumentFound[] = $handler;

        return $this;
    }

    /**
     * Add a callback to call in the case when no documents were found
     *
     * @param  callable $callback
     * @return ZugferdMailAccount
     */
    public function addCallbackNoDocumentFound(callable $callback): ZugferdMailAccount
    {
        $this->callBacksNoDocumentFound[] = $callback;

        return $this;
    }

    /**
     * Returns true if only unssen messages are visible, otherwise false
     *
     * @return boolean
     */
    public function getUnseenMessagesOnlyEnabled(): bool
    {
        return $this->unseenMessagesOnlyEnabled;
    }

    /**
     * Activate or deactivate the filter for only unseen messages
     *
     * @param  boolean $unseenMessagesOnly
     * @return ZugferdMailAccount
     */
    public function setUnseenMessagesOnlyEnabled(bool $unseenMessagesOnly): ZugferdMailAccount
    {
        $this->unseenMessagesOnlyEnabled = $unseenMessagesOnly;

        return $this;
    }

    /**
     * Activate the filtering only of unseen messages
     *
     * @return ZugferdMailAccount
     */
    public function activateUnseenMessagesOnly(): ZugferdMailAccount
    {
        return $this->setUnseenMessagesOnlyEnabled(true);
    }

    /**
     * Deactivate the filtering only of unseen messages
     *
     * @return ZugferdMailAccount
     */
    public function deactivateUnseenMessagesOnly(): ZugferdMailAccount
    {
        return $this->setUnseenMessagesOnlyEnabled(false);
    }

    /**
     * Returns the account definitoon
     *
     * @return array
     */
    public function getAccountDefinition(): array
    {
        return [
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'protocol'  => $this->getProtocol(),
            'encryption'    => $this->getEncryption(),
            'validate_cert' => $this->getValidateCert(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'authentication' => $this->getAuthentication(),
            "timeout" => $this->getTimeout(),
        ];
    }

    /**
     * Create a guid as an dummy identifier
     *
     * @return string
     */
    private function createGuidForIdentifier(): string
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }
}
