<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\console;

use ReflectionClass;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesMailAccount;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleOutputsHeading;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleOutputsMessageBagMessages;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\ZugferdMailReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class representing a console command for processing messages in folders of an email account
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailProcessFoldersConsoleCommand extends Command
{
    use ZugferdMailConsoleHandlesMailAccount,
        ZugferdMailConsoleOutputsHeading,
        ZugferdMailConsoleOutputsMessageBagMessages;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('zfmail:processmailboxfolders')
            ->setDescription('Process mails and their attachments')
            ->setHelp('Process mails and their attachments')
            ->configureMailAccountOptions()
            ->addOption('folder', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A folder to look into')
            ->addOption('mimetype', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid mimetype for an message attachment')
            ->addOption('handler', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A valid handler class')
            ->addOption('enableublsupport', null, InputOption::VALUE_NONE, 'Enable UBL support')
            ->addOption('enablesymfonyvalidation', null, InputOption::VALUE_NONE, 'Enable Symfony validation')
            ->addOption('enablexsdvalidation', null, InputOption::VALUE_NONE, 'Enable XSD validation')
            ->addOption('enablekositvalidation', null, InputOption::VALUE_NONE, 'Enable Kosit validation');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->writeHeading($output);

        $account = $this->createMailAccountFromOptions($input);

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

        $this->writeAccountInformation($output, $account);
        $this->writeAccountFoldersToWatch($output, $account);
        $this->writeAccountMimeTypesToWatch($output, $account);

        $config = new ZugferdMailConfig();
        $config->addAccountObject($account);
        $config->setUblSupportEnabled($input->getOption('enableublsupport'));
        $config->setSymfonyValidationEnabled($input->getOption('enablesymfonyvalidation'));
        $config->setXsdValidationEnabled($input->getOption('enablexsdvalidation'));
        $config->setKositValidationEnabled($input->getOption('enablekositvalidation'));

        $reader = new ZugferdMailReader($config);
        $reader->checkAllAccounts();

        $this->outputMessagesFromMessageBagAsTableToCli($output);

        return Command::SUCCESS;
    }
}
