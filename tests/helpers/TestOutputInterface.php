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
    public function write($messages, bool $newline = false, int $options = 0)
    {
        $this->outputs[] = $messages;
    }

    /**
     * @inheritDoc
     */
    public function writeln($messages, int $options = 0)
    {
        $this->outputs[] = $messages;
    }

    /**
     * @inheritDoc
     */
    public function setVerbosity(int $level)
    {
        $this->verbosity = $level;
    }

    /**
     * @inheritDoc
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isQuiet()
    {
        return self::VERBOSITY_QUIET === $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isVerbose()
    {
        return self::VERBOSITY_VERBOSE <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isVeryVerbose()
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function isDebug()
    {
        return self::VERBOSITY_DEBUG <= $this->verbosity;
    }

    /**
     * @inheritDoc
     */
    public function setDecorated(bool $decorated)
    {
        $this->formatter->setDecorated($decorated);
    }

    /**
     * @inheritDoc
     */
    public function isDecorated()
    {
        return $this->formatter->isDecorated();
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @inheritDoc
     */
    public function getFormatter()
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
