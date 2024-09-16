<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\console;

use Composer\InstalledVersions as ComposerInstalledVersions;
use Symfony\Component\Console\Output\OutputInterface;

trait ZugferdMailConsoleCommandGeneralOutputTrait
{
    /**
     * Writes a heading
     *
     * @return void
     */
    protected function writeHeading(OutputInterface $output): void
    {
        $output->writeln('┌────────────────────────────────────────────────┐');
        $output->writeLn('│ <info>' . str_pad(sprintf("horstoeko/zugferdmail %s", ComposerInstalledVersions::getVersion('horstoeko/zugferdmail')), 46, ' ', STR_PAD_RIGHT) . '</info> │');
        $output->writeln('└────────────────────────────────────────────────┘');
    }
}
