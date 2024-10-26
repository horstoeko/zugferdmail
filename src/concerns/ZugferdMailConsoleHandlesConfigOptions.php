<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\concerns;

use horstoeko\zugferdmail\config\ZugferdMailConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait representing configuring the console options for defining
 * a mail account and output the information to the console
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
trait ZugferdMailConsoleHandlesConfigOptions
{
    /**
     * Add console options for general config options
     *
     * @return static
     */
    protected function configureConfigOptions()
    {
        $this->addOption('enableublsupport', null, InputOption::VALUE_NONE, 'Enable UBL support')
            ->addOption('enablesymfonyvalidation', null, InputOption::VALUE_NONE, 'Enable Symfony validation')
            ->addOption('enablexsdvalidation', null, InputOption::VALUE_NONE, 'Enable XSD validation')
            ->addOption('enablekositvalidation', null, InputOption::VALUE_NONE, 'Enable Kosit validation')
            ->addOption('enableunseenonly', null, InputOption::VALUE_NONE, 'Process only unseen messages');

        return $this;
    }

    /**
     * Create a general config from console options
     *
     * @param  InputInterface $input
     * @return ZugferdMailConfig
     */
    protected function createConfigFromOptions(InputInterface $input): ZugferdMailConfig
    {
        $config = new ZugferdMailConfig();

        $config->setUblSupportEnabled($input->hasOption('enableublsupport') ? $input->getOption('enableublsupport') : false);
        $config->setSymfonyValidationEnabled($input->hasOption('enablesymfonyvalidation') ? $input->getOption('enablesymfonyvalidation') : false);
        $config->setXsdValidationEnabled($input->hasOption('enablexsdvalidation') ? $input->getOption('enablexsdvalidation') : false);
        $config->setKositValidationEnabled($input->hasOption('enablekositvalidation') ? $input->getOption('enablekositvalidation') : false);
        $config->setUnseenMessagesOnlyEnabled($input->hasOption('enableunseenonly') ? $input->getOption('enableunseenonly') : false);

        return $config;
    }
}
