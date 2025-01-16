<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use DivisionByZeroError;
use ArithmeticError;
use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\consts\ZugferdMailMessageBagType;
use Symfony\Component\Console\Exception\InvalidArgumentException;
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
     * @param  OutputInterface $output
     * @return void
     */
    protected function outputMessagesFromMessageBagToCli(OutputInterface $output): void
    {
        foreach ($this->getAllMessagesFromMessageBag() as $message) {
            $output->writeln($this->formatMessageBagMessage($message));
        }
    }

    /**
     * Output messages as a table to CLI
     *
     * @param  OutputInterface $output
     * @return void
     * @throws InvalidArgumentException
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    protected function outputMessagesFromMessageBagAsTableToCli(OutputInterface $output): void
    {
        if (!$this->getHasAnyMessageInMessageBag()) {
            return;
        }

        $messages = collect($this->getAllMessagesFromMessageBag());

        $messages = $messages->map(
            function (array $message, int $messageKey) use ($messages): array {
                $result = [];

                if ($message["message"] == "" || $message["message"] == "<T-SEP>") {
                    if ($messageKey != $messages->count() - 1) {
                        $result = [new TableSeparator(), new TableSeparator(), new TableSeparator()];
                    }
                } else {
                    $result = [
                        $message["datetime"]->format("Y-m-d H:i:s"),
                        $this->formatMessageBagMessage($message),
                        $message["source"]
                    ];
                }

                return $result;
            }
        )->toArray();

        $table = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['Date', 'Message', 'Source']);
        $table->setRows($messages);
        $table->render();
    }

    /**
     * Format the message string
     *
     * @param  array $message
     * @return string
     */
    private function formatMessageBagMessage(array $message): string
    {
        $messageText = trim(wordwrap($message["message"], 100));

        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_LOG_SECONDARY) {
            return sprintf("<gray>%s</gray>", $messageText);
        }

        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_WARN) {
            return sprintf("<comment>%s</comment>", $messageText);
        }

        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_ERROR) {
            if (isset($message["additionalData"]["errno"])) {
                return sprintf("<red-text>%s in %s:%s</red-text>", $messageText, $message["additionalData"]["errfile"], $message["additionalData"]["errline"]);
            }

            return sprintf("<red-text>%s</red-text>", $messageText);
        }

        if ($message["type"] === ZugferdMailMessageBagType::MESSAGETYPE_SUCCESS) {
            return sprintf("<info>%s</info>", $messageText);
        }

        return $messageText;
    }
}
