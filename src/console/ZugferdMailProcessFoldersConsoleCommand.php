<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\console;

use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesConfigOptions;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesMailAccountOptions;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleOutputsMessageBagMessages;
use horstoeko\zugferdmail\ZugferdMailReader;
use Symfony\Component\Console\Command\Command;

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
    use ZugferdMailConsoleHandlesConfigOptions;
    use ZugferdMailConsoleHandlesMailAccountOptions;
    use ZugferdMailConsoleOutputsMessageBagMessages;
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
            ->configureConfigOptions();
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

        $config = $this->createConfigFromOptions($this->inputInterface);
        $config->addAccountObject($account);

        $reader = new ZugferdMailReader($config);
        $reader->checkAllAccounts();

        $this->outputMessagesFromMessageBagAsTableToCli($this->outputInterface);

        return Command::SUCCESS;
    }
}
