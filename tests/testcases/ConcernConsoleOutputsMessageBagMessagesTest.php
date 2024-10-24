<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\tests\TestCase;
use horstoeko\zugferdmail\concerns\ZugferdMailClearsMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailSendsMessagesToMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailReceivesMessagesFromMessageBag;
use horstoeko\zugferdmail\concerns\ZugferdMailConsoleOutputsMessageBagMessages;
use horstoeko\zugferdmail\tests\helpers\TestOutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;

class ConcernConsoleOutputsMessageBagMessagesTest extends TestCase
{
    use ZugferdMailConsoleOutputsMessageBagMessages,
        ZugferdMailClearsMessageBag,
        ZugferdMailReceivesMessagesFromMessageBag,
        ZugferdMailSendsMessagesToMessageBag;

    public function testOutputMessagesFromMessageBagToCli()
    {
        $outputFormatter = new OutputFormatter();
        $testOutputInterface = new TestOutputInterface;
        $testOutputInterface->setFormatter($outputFormatter);

        $this->clearMessageBag();

        // One message

        $this->addLogMessageToMessageBag("Message 1");

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(1, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);

        // Two messages

        $this->addLogSecondaryMessageToMessageBag("Message 2");

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(2, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);
        $this->assertEquals("<gray>Message 2</gray>", $outputs[1]);

        // Three messages

        $this->addWarningMessageToMessageBag('Message 3');

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(3, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);
        $this->assertEquals("<gray>Message 2</gray>", $outputs[1]);
        $this->assertEquals("<comment>Message 3</comment>", $outputs[2]);

        // Four messages

        $this->addErrorMessageToMessageBag('Message 4');

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(4, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);
        $this->assertEquals("<gray>Message 2</gray>", $outputs[1]);
        $this->assertEquals("<comment>Message 3</comment>", $outputs[2]);
        $this->assertEquals("<error>Message 4</error>", $outputs[3]);

        // Five messages

        $this->addSuccessMessageToMessageBag('Message 5');

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(5, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);
        $this->assertEquals("<gray>Message 2</gray>", $outputs[1]);
        $this->assertEquals("<comment>Message 3</comment>", $outputs[2]);
        $this->assertEquals("<error>Message 4</error>", $outputs[3]);
        $this->assertEquals("<info>Message 5</info>", $outputs[4]);

        // Six messages

        $this->addThrowableToMessageBag(new \Exception('ExceptionMessage 1'));

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(6, $outputs);
        $this->assertEquals("Message 1", $outputs[0]);
        $this->assertEquals("<gray>Message 2</gray>", $outputs[1]);
        $this->assertEquals("<comment>Message 3</comment>", $outputs[2]);
        $this->assertEquals("<error>Message 4</error>", $outputs[3]);
        $this->assertEquals("<info>Message 5</info>", $outputs[4]);
        $this->assertStringContainsString("<error>ExceptionMessage 1 in", $outputs[5]);
        $this->assertStringContainsString("ConcernConsoleOutputsMessageBagMessagesTest.php", $outputs[5]);
        $this->assertStringContainsString("</error>", $outputs[5]);

        // Clear message bag

        $this->clearMessageBag();

        $this->outputMessagesFromMessageBagToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertCount(0, $outputs);
    }

    public function testOutputMessagesFromMessageBagAsTableToCli()
    {
        $outputFormatter = new OutputFormatter();
        $testOutputInterface = new TestOutputInterface;
        $testOutputInterface->setFormatter($outputFormatter);

        $this->clearMessageBag();

        // One message

        $this->addLogMessageToMessageBag("Message 1");
        $this->addLogMessageToMessageBag('');
        $this->addLogMessageToMessageBag("Message 2");

        $this->outputMessagesFromMessageBagAsTableToCli($testOutputInterface);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(7, $outputs);
        $this->assertEquals("┌─────────────────────┬───────────┬─────────────────────────────────────────────┐", $outputs[0]);
        $this->assertEquals("│<info> Date                </info>│<info> Message   </info>│<info> Source                                      </info>│", $outputs[1]);
        $this->assertEquals("├─────────────────────┼───────────┼─────────────────────────────────────────────┤", $outputs[2]);
        $this->assertStringContainsString("│ Message 1 │", $outputs[3]);
        $this->assertStringContainsString("│ ConcernConsoleOutputsMessageBagMessagesTest │", $outputs[3]);
        $this->assertEquals("│─────────────────────│───────────│─────────────────────────────────────────────│", $outputs[4]);
        $this->assertStringContainsString("│ Message 2 │", $outputs[5]);
        $this->assertStringContainsString("│ ConcernConsoleOutputsMessageBagMessagesTest │", $outputs[5]);
        $this->assertEquals("└─────────────────────┴───────────┴─────────────────────────────────────────────┘", $outputs[6]);
    }
}
