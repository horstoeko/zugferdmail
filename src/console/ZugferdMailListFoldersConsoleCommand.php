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
use Symfony\Component\Console\Helper\Table;
use Webklex\PHPIMAP\Folder;

/**
 * Class representing a console command for listing folders in an email account
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailListFoldersConsoleCommand extends ZugferdMailBaseConsoleCommand
{
    use ZugferdMailConsoleHandlesConfigOptions,
        ZugferdMailConsoleHandlesMailAccountOptions,
        ZugferdMailConsoleOutputsMessageBagMessages;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('zfmail:listmailboxfolders')
            ->setDescription('Lists mailboxes for an account')
            ->setHelp('Lists mailboxes for an account')
            ->configureMailAccountOptions();
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(): int
    {
        $account = $this->createMailAccountFromOptions($this->inputInterface);

        $this->writeAccountInformation($this->outputInterface, $account);

        $config = $this->createConfigFromOptions($this->inputInterface);
        $config->addAccountObject($account);

        $reader = new ZugferdMailReader($config);

        $folders = $reader->getAllAvailableRootFolders();
        $folders = array_map(
            function (Folder $folder) {
                return [
                    $folder->full_name,
                    count($folder->children),
                    $folder->messages()->all()->count(),
                ];
            },
            $folders[0]["folders"]
        );

        $table = new Table($this->outputInterface);
        $table->setStyle('box');
        $table->setHeaders(['Foldername', "Subfolders", "Messages"]);
        $table->setRows($folders);
        $table->render();

        $this->outputMessagesFromMessageBagAsTableToCli($this->outputInterface);

        return Command::SUCCESS;
    }
}
