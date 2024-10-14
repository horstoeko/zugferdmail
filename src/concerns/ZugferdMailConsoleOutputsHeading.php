<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use Composer\InstalledVersions as ComposerInstalledVersions;
use OutOfBoundsException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait representing the output of general informations to the console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleOutputsHeading
{
    /**
     * Writes a heading
     *
     * @param OutputInterface $output
     * @return void
     * @throws OutOfBoundsException
     */
    protected function writeHeading(OutputInterface $output): void
    {
        $output->writeln('┌────────────────────────────────────────────────┐');
        $output->writeLn('│ <info>' . str_pad(sprintf("horstoeko/zugferdmail %s", ComposerInstalledVersions::getVersion('horstoeko/zugferdmail')), 46, ' ', STR_PAD_RIGHT) . '</info> │');
        $output->writeln('└────────────────────────────────────────────────┘');
    }
}
