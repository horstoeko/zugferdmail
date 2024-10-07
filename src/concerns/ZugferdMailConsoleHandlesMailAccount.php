<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use ReflectionClass;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait representing the output of mail account informations to the console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleHandlesMailAccount
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
            ->addOption('recursive', null, InputOption::VALUE_NONE, 'Check folders recursive')
            ->addOption('folder', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A folder to look into')
            ->addOption('mimetype', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid mimetype for an message attachment')
            ->addOption('handler', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid handler class');

        return $this;
    }

    /**
     * Create a mail account from console options
     *
     * @param  InputInterface $input
     * @return ZugferdMailAccount
     */
    protected function createMailAccountFromOptions(InputInterface $input): ZugferdMailAccount
    {
        $account = new ZugferdMailAccount();
        $account->setIdentifier(sprintf("%s@%s:%s", $input->getOption('username'), $input->getOption('host'), $input->getOption('port')));
        $account->setHost($input->getOption('host'));
        $account->setPort($input->getOption('port'));
        $account->setProtocol($input->getOption('protocol'));
        $account->setEncryption(strcasecmp($input->getOption('encryption'), "none") === 0 ? false : $input->getOption('encryption'));
        $account->setValidateCert($input->getOption('validateCert'));
        $account->setUsername($input->getOption('username'));
        $account->setPassword($input->getOption('password'));
        $account->setAuthentication(strcasecmp($input->getOption('authentication'), "none") === 0 ? null : $input->getOption('authentication'));
        $account->setTimeout($input->getOption('timeout'));
        $account->setRecursive($input->getOption('recursive'));

        foreach ($input->getOption('folder') as $folderToWatch) {
            $account->addFolderToWatch($folderToWatch);
        }

        foreach ($input->getOption('mimetype') as $mimeTypeToWatch) {
            $account->addMimeTypeToWatch($mimeTypeToWatch);
        }

        foreach ($input->getOption('handler') as $handlerClassName) {
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
     */
    protected function writeAccountInformation(OutputInterface $output, ZugferdMailAccount $account): void
    {
        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['ID', 'Host', 'Port', 'Protocol', 'Encryption', 'ValidateCert', 'Authentication', 'Username', 'Recursive']);
        $table->setRows(
            [
            [
                $account->getIdentifier(),
                $account->getHost(),
                $account->getPort(),
                $account->getProtocol(),
                $account->getEncryption(),
                $account->getValidateCert() === true ? "Yes" : "No",
                $account->getAuthentication() === null ? "None" : $account->getAuthentication(),
                $account->getUsername(),
                $account->getRecursive() === true ? "Yes" : "No",
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
