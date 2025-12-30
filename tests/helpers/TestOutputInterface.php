<?php

namespace horstoeko\zugferdmail\tests\helpers;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestOutputInterface implements OutputInterface
{
    /**
     * Internal buffer which contains the grabbed outputs
     *
     * @var array
     */
    private $outputs = [];

    /**
     * Internal verbosity flag
     *
     * @var integer
     */
    private $verbosity = 0;

    /**
     * Output formatter
     *
     * @var OutputFormatterInterface
     */
    private $formatter = null;

    /**
     * @inheritDoc
     */
    public function write($messages, bool $newline = false, int $options = 0): void
    {
        $this->outputs[] = $messages;
    }

    /**
     * @inheritDoc
     */
    public function writeln($messages, int $options = 0): void
    {
        $this->outputs[] = $messages;
    }

    /**
     * @inheritDoc
     */
    public function setVerbosity(int $level): void
    {
        $this->verbosity = $level;
    }

    /**
     * @inheritDoc
     */
    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isSilent(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isQuiet(): bool
    {
        return self::VERBOSITY_QUIET === $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isVerbose(): bool
    {
        return self::VERBOSITY_VERBOSE <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isVeryVerbose(): bool
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isDebug(): bool
    {
        return self::VERBOSITY_DEBUG <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function setDecorated(bool $decorated): void
    {
        $this->formatter->setDecorated($decorated);
    }

    /**
     * @inheritDoc
     */
    public function isDecorated(): bool
    {
        return $this->formatter->isDecorated();
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): OutputFormatterInterface
    {
        return $this->formatter;
    }

    /**
     * Returns the grabbed outputs
     *
     * @return array
     */
    public function getOutputs(): array
    {
        $outputs = $this->outputs;

        $this->outputs = [];

        return $outputs;
    }
}
