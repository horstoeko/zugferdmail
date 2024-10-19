<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Trait representing the facility to use custom colors
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleCustomColors
{
    /**
     * Introduce custom colors
     *
     * @param  OutputInterface $output
     * @return void
     */
    protected function setCustomColors(OutputInterface $output)
    {
        $outputStyle = new OutputFormatterStyle('gray', '#000', []);
        $output->getFormatter()->setStyle('gray', $outputStyle);
    }
}
