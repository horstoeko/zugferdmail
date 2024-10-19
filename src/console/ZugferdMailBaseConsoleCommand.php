<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\console;

use LogicException;
use OutOfBoundsException;
use Composer\InstalledVersions as ComposerInstalledVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class representing the base for a console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
abstract class ZugferdMailBaseConsoleCommand extends Command
{
    /**
     * Input interface
     *
     * @var InputInterface
     */
    protected $inputInterface;

    /**
     * Output interface
     *
     * @var OutputInterface
     */
    protected $outputInterface;

    /**
     * Introduce custom colors
     *
     * @return void
     */
    private function setCustomColors(): void
    {
        $outputStyle = new OutputFormatterStyle('gray', '#000', []);
        $this->outputInterface->getFormatter()->setStyle('gray', $outputStyle);
    }

    /**
     * Writes a heading
     *
     * @return void
     * @throws OutOfBoundsException
     */
    private function writeHeading(): void
    {
        $this->outputInterface->writeln('┌────────────────────────────────────────────────┐');
        $this->outputInterface->writeLn('│ <info>' . str_pad(sprintf("horstoeko/zugferdmail %s", ComposerInstalledVersions::getVersion('horstoeko/zugferdmail')), 46, ' ', STR_PAD_RIGHT) . '</info> │');
        $this->outputInterface->writeln('└────────────────────────────────────────────────┘');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->inputInterface = $input;
        $this->outputInterface = $output;

        $this->setCustomColors();
        $this->writeHeading();

        return $this->doExecute();
    }

    /**
     * Executes the current command.
     *
     * @return int 0 if everything went fine, or an exit code
     * @throws LogicException When this abstract method is not implemented
     */
    abstract protected function doExecute(): int;
}
