<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesConfigOptions;
use horstoeko\zugferdmail\tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class ZugferdMailConcernConsoleHandlesConfigOptionsTest extends TestCase
{
    use ZugferdMailConsoleHandlesConfigOptions;

    /**
     * Input definition
     *
     * @var InputDefinition
     */
    protected $definition = null;

    /**
     * Adds an option.
     *
     * @param  string       $name
     * @param  string|null  $shortcut
     * @param  integer|null $mode
     * @param  string       $description
     * @param  mixed        $default
     * @return static
     */
    protected function addOption(string $name, $shortcut = null, ?int $mode = null, string $description = '', $default = null): self
    {
        $this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->definition = new InputDefinition();
    }

    public function testInputInterface(): void
    {
        $this->configureConfigOptions();

        $arrayInput = new ArrayInput(
            [
                '--enableublsupport' => null,
                '--enablesymfonyvalidation' => null,
                '--enablexsdvalidation' => null,
                '--enablekositvalidation' => null,
            ],
            $this->definition
        );

        $this->assertTrue($arrayInput->hasOption('enableublsupport'));
        $this->assertTrue($arrayInput->hasOption('enablesymfonyvalidation'));
        $this->assertTrue($arrayInput->hasOption('enablexsdvalidation'));
        $this->assertTrue($arrayInput->hasOption('enablekositvalidation'));
    }

    public function testCreateConfigFromOptions(): void
    {
        $this->configureConfigOptions();

        $arrayInput = new ArrayInput(
            [
                '--enableublsupport' => null,
                '--enablesymfonyvalidation' => null,
                '--enablexsdvalidation' => null,
                '--enablekositvalidation' => null,
            ],
            $this->definition
        );

        $config = $this->createConfigFromOptions($arrayInput);

        $this->assertTrue($config->getUblSupportEnabled());
        $this->assertTrue($config->getSymfonyValidationEnabled());
        $this->assertTrue($config->getXsdValidationEnabled());
        $this->assertTrue($config->getKositValidationEnabled());
    }
}
