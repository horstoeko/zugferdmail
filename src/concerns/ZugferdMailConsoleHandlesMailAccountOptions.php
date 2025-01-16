<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use ArithmeticError;
use ReflectionClass;
use DivisionByZeroError;
use InvalidArgumentException as GlobalInvalidArgumentException;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait representing configuring the console options for defining
 * a mail account and output the information to the console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleHandlesMailAccountOptions
{
    /**
     * Add console options with all needed options for creating a mail account
     *
     * @return static
     */
    protected function configureMailAccountOptions()
    {
        $this->addOption('host', null, InputOption::VALUE_REQUIRED, 'The host to contact', '')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'The port on the host to contact', 993)
            ->addOption('protocol', null, InputOption::VALUE_REQUIRED, 'The protocol to use. Must be one of imap, legacy-imap, pop3 or nntp, imap', 'imap')
            ->addOption('encryption', null, InputOption::VALUE_REQUIRED, 'The encryption to use. Must be one of none, ssl, tls, starttls, notls', 'ssl')
            ->addOption('validateCert', null, InputOption::VALUE_NONE, 'SSL certificates must be valid')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The username to use for authentication', '')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password to use for authentication', '')
            ->addOption('authentication', null, InputOption::VALUE_REQUIRED, 'The authentication method to use. Must be one of none, oauth', 'none')
            ->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'Connection timeout in seconds', 30)
            ->addOption('enableunseenonly', null, InputOption::VALUE_NONE, 'Process unseen messages only');

        return $this;
    }

    /**
     * Add console options which all needed to define watches for an mail account
     *
     * @return static
     */
    protected function configureMailAccountWatchOptions()
    {
        $this->addOption('folder', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A folder to look into')
            ->addOption('mimetype', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid mimetype for an message attachment')
            ->addOption('handler', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid handler class');

        return $this;
    }

    /**
     * Create a mail account from console options
     *
     * @param  InputInterface $input
     * @return ZugferdMailAccount
     * @throws InvalidArgumentException
     * @throws GlobalInvalidArgumentException
     */
    protected function createMailAccountFromOptions(InputInterface $input): ZugferdMailAccount
    {
        $account = new ZugferdMailAccount();

        $account->setHost($input->getOption('host'));
        $account->setPort($input->getOption('port'));
        $account->setProtocol($input->getOption('protocol'));
        $account->setEncryption(strcasecmp($input->getOption('encryption'), "none") === 0 ? false : $input->getOption('encryption'));
        $account->setValidateCert($input->getOption('validateCert'));
        $account->setUsername($input->getOption('username'));
        $account->setPassword($input->getOption('password'));
        $account->setAuthentication(strcasecmp($input->getOption('authentication'), "none") === 0 ? null : $input->getOption('authentication'));
        $account->setTimeout($input->getOption('timeout'));
        $account->setUnseenMessagesOnlyEnabled($input->getOption('enableunseenonly'));

        $foldersToWatch = $input->hasOption('folder') ? $input->getOption('folder') : [];
        $mimeTypesToWatch = $input->hasOption('mimetype') ? $input->getOption('mimetype') : [];
        $handlers = $input->hasOption('handler') ? $input->getOption('handler') : [];

        foreach ($foldersToWatch as $folderToWatch) {
            $account->addFolderToWatch($folderToWatch);
        }

        foreach ($mimeTypesToWatch as $mimeTypeToWatch) {
            $account->addMimeTypeToWatch($mimeTypeToWatch);
        }

        foreach ($handlers as $handlerClassName) {
            $args = explode(",", $handlerClassName);
            $handlerClassName = $args[0];
            unset($args[0]);

            $reflection = new ReflectionClass($handlerClassName);
            $handler = $reflection->newInstanceArgs($args);

            $account->addHandler($handler);
        }

        return $account;
    }

    /**
     * Writes account information
     *
     * @param  OutputInterface    $output
     * @param  ZugferdMailAccount $account
     * @return void
     * @throws InvalidArgumentException
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    protected function writeAccountInformation(OutputInterface $output, ZugferdMailAccount $account): void
    {
        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['ID', 'Host', 'Port', 'Protocol', 'Encryption', 'ValidateCert', 'Authentication', 'Username']);
        $table->setRows(
            [
                [
                    $account->getIdentifier(),
                    $account->getHost(),
                    $account->getPort(),
                    $account->getProtocol(),
                    $account->getEncryption(),
                    $account->getValidateCert() === true ? "Yes" : "No",
                    $account->getAuthentication() ?? "None",
                    $account->getUsername(),
                ],
            ]
        );
        $table->render();
    }

    /**
     * Write account's folders to watch
     *
     * @param  OutputInterface    $output
     * @param  ZugferdMailAccount $account
     * @return void
     * @throws InvalidArgumentException
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    protected function writeAccountFoldersToWatch(OutputInterface $output, ZugferdMailAccount $account): void
    {
        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['Folder']);
        foreach ($account->getFoldersTowatch() as $folderToWatch) {
            $table->addRow([$folderToWatch]);
        }

        $table->render();
    }

    /**
     * Write account's mimetypes to watch
     *
     * @param  OutputInterface    $output
     * @param  ZugferdMailAccount $account
     * @return void
     * @throws InvalidArgumentException
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    protected function writeAccountMimeTypesToWatch(OutputInterface $output, ZugferdMailAccount $account): void
    {
        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['MimeTypes']);
        foreach ($account->getMimeTypesToWatch() as $mimeTypeToWatch) {
            $table->addRow([$mimeTypeToWatch]);
        }

        $table->render();
    }
}
