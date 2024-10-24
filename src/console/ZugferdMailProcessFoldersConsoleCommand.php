<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\console;

use ReflectionClass;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesMailAccountOptions;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleOutputsMessageBagMessages;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\ZugferdMailReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class representing a console command for processing messages in folders of an email account
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailProcessFoldersConsoleCommand extends ZugferdMailBaseConsoleCommand
{
    use ZugferdMailConsoleHandlesMailAccountOptions,
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
            ->configureMailAccountWatchOptions()
            ->addOption('enableublsupport', null, InputOption::VALUE_NONE, 'Enable UBL support')
            ->addOption('enablesymfonyvalidation', null, InputOption::VALUE_NONE, 'Enable Symfony validation')
            ->addOption('enablexsdvalidation', null, InputOption::VALUE_NONE, 'Enable XSD validation')
            ->addOption('enablekositvalidation', null, InputOption::VALUE_NONE, 'Enable Kosit validation');
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(): int
    {
        $account = $this->createMailAccountFromOptions($this->inputInterface);

        $this->writeAccountInformation($this->outputInterface, $account);
        $this->writeAccountFoldersToWatch($this->outputInterface, $account);
        $this->writeAccountMimeTypesToWatch($this->outputInterface, $account);

        $config = new ZugferdMailConfig();
        $config->addAccountObject($account);
        $config->setUblSupportEnabled($this->inputInterface->getOption('enableublsupport'));
        $config->setSymfonyValidationEnabled($this->inputInterface->getOption('enablesymfonyvalidation'));
        $config->setXsdValidationEnabled($this->inputInterface->getOption('enablexsdvalidation'));
        $config->setKositValidationEnabled($this->inputInterface->getOption('enablekositvalidation'));

        $reader = new ZugferdMailReader($config);
        $reader->checkAllAccounts();

        $this->outputMessagesFromMessageBagAsTableToCli($this->outputInterface);

        return Command::SUCCESS;
    }
}
