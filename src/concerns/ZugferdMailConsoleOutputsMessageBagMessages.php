<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait representing the output of the content of the internal messagebag to the console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleOutputsMessageBagMessages
{
    use ZugferdMailReceivesMessagesFromMessageBag;

    /**
     * Output messages to CLI
     *
     * @return void
     */
    protected function outputMessagesToCli(OutputInterface $output): void
    {
        foreach ($this->getAllMessages() as $message) {
            $output->writeln($this->formatMessage($message));
        }
    }

    /**
     * Output messages as a table to CLI
     *
     * @return void
     */
    protected function outputMessagesAsTableToCli(OutputInterface $output): void
    {
        $messages = collect($this->getAllMessages());

        $messages = $messages->map(
            function ($message, int $messageKey) use ($messages) {
                $result = [];

                if ($message["message"] == "") {
                    if ($messageKey != $messages->count() - 1) {
                        $result = [new TableSeparator()];
                    }
                } else {
                    $result = [$this->formatMessage($message)];
                }

                return $result;
            }
        )->toArray();

        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['Message']);
        $table->setRows($messages);
        $table->render();
    }

    /**
     * Format the message string
     *
     * @param  array $message
     * @return string
     */
    private function formatMessage(array $message): string
    {
        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_LOG) {
            return sprintf("%s", $message["message"]);
        }
        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_WARN) {
            return sprintf("<comment>%s</comment>", $message["message"]);
        }
        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_ERROR) {
            return sprintf("<error>%s</error>", $message["message"]);
        }
        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS) {
            return sprintf("<info>%s</info>", $message["message"]);
        }
        return $message["message"];
    }
}