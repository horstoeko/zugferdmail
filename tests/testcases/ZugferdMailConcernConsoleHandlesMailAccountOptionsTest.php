<?php

namespace horstoeko\zugferdmail\tests\testcases;

use horstoeko\zugferdmail\concerns\ZugferdMailConsoleHandlesMailAccountOptions;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerDeleteMessage;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerNull;
use horstoeko\zugferdmail\tests\helpers\TestOutputInterface;
use horstoeko\zugferdmail\tests\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class ZugferdMailConcernConsoleHandlesMailAccountOptionsTest extends TestCase
{
    use ZugferdMailConsoleHandlesMailAccountOptions;

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
    protected function addOption(string $name, $shortcut = null, ?int $mode = null, string $description = '', $default = null)
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

    public function testInputInterfaceNoWatches(): void
    {
        $this->configureMailAccountOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
            ],
            $this->definition
        );

        $this->assertEquals('127.0.0.1', $arrayInput->getOption('host'));
        $this->assertEquals('993', $arrayInput->getOption('port'));
        $this->assertEquals('pop3', $arrayInput->getOption('protocol'));
        $this->assertEquals('tls', $arrayInput->getOption('encryption'));
        $this->assertTrue($arrayInput->getOption('validateCert'));
        $this->assertEquals('demouser', $arrayInput->getOption('username'));
        $this->assertEquals('demopassword', $arrayInput->getOption('password'));
        $this->assertEquals('oauth', $arrayInput->getOption('authentication'));
        $this->assertEquals(60, $arrayInput->getOption('timeout'));
        $this->assertFalse($arrayInput->hasOption('folder'));
        $this->assertFalse($arrayInput->hasOption('mimetype'));
        $this->assertFalse($arrayInput->hasOption('handler'));
    }

    public function testInputInterfaceWithWatches(): void
    {
        $this->configureMailAccountOptions();
        $this->configureMailAccountWatchOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
                '--folder' => ['INBOX', 'Accounts/invoice@nowhere.all'],
                '--mimetype' => ['text/xml', 'application/pdf'],
                '--handler' => [ZugferdMailHandlerNull::class, ZugferdMailHandlerDeleteMessage::class],
            ],
            $this->definition
        );

        $this->assertEquals('127.0.0.1', $arrayInput->getOption('host'));
        $this->assertEquals('993', $arrayInput->getOption('port'));
        $this->assertEquals('pop3', $arrayInput->getOption('protocol'));
        $this->assertEquals('tls', $arrayInput->getOption('encryption'));
        $this->assertTrue($arrayInput->getOption('validateCert'));
        $this->assertEquals('demouser', $arrayInput->getOption('username'));
        $this->assertEquals('demopassword', $arrayInput->getOption('password'));
        $this->assertEquals('oauth', $arrayInput->getOption('authentication'));
        $this->assertEquals(60, $arrayInput->getOption('timeout'));

        $this->assertIsArray($arrayInput->getOption('folder'));
        $this->assertNotEmpty($arrayInput->getOption('folder'));
        $this->assertCount(2, $arrayInput->getOption('folder'));

        $this->assertIsArray($arrayInput->getOption('mimetype'));
        $this->assertNotEmpty($arrayInput->getOption('mimetype'));
        $this->assertCount(2, $arrayInput->getOption('mimetype'));

        $this->assertIsArray($arrayInput->getOption('handler'));
        $this->assertNotEmpty($arrayInput->getOption('handler'));
        $this->assertCount(2, $arrayInput->getOption('handler'));
    }

    public function testCreateMailAccountFromOptionsNoWatches(): void
    {
        $this->configureMailAccountOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
            ],
            $this->definition
        );

        $account = $this->createMailAccountFromOptions($arrayInput);

        $this->assertEquals('127.0.0.1', $account->getHost());
        $this->assertEquals('993', $account->getPort());
        $this->assertEquals('pop3', $account->getProtocol());
        $this->assertEquals('tls', $account->getEncryption());
        $this->assertTrue($account->getValidateCert());
        $this->assertEquals('demouser', $account->getUsername());
        $this->assertEquals('demopassword', $account->getPassword());
        $this->assertEquals('oauth', $account->getAuthentication());
        $this->assertEquals(60, $account->getTimeout());

        $this->assertIsArray($account->getFoldersTowatch());
        $this->assertEmpty($account->getFoldersTowatch());
        $this->assertCount(0, $account->getFoldersTowatch());

        $this->assertIsArray($account->getMimeTypesToWatch());
        $this->assertEmpty($account->getMimeTypesToWatch());
        $this->assertCount(0, $account->getMimeTypesToWatch());

        $this->assertIsArray($account->getHandlers());
        $this->assertEmpty($account->getHandlers());
        $this->assertCount(0, $account->getHandlers());
    }

    public function testCreateMailAccountFromOptionsWithWatches(): void
    {
        $this->configureMailAccountOptions();
        $this->configureMailAccountWatchOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
                '--folder' => ['INBOX', 'Accounts/invoice@nowhere.all'],
                '--mimetype' => ['text/xml', 'application/pdf'],
                '--handler' => [ZugferdMailHandlerNull::class, ZugferdMailHandlerDeleteMessage::class],
            ],
            $this->definition
        );

        $account = $this->createMailAccountFromOptions($arrayInput);

        $this->assertEquals('127.0.0.1', $account->getHost());
        $this->assertEquals('993', $account->getPort());
        $this->assertEquals('pop3', $account->getProtocol());
        $this->assertEquals('tls', $account->getEncryption());
        $this->assertTrue($account->getValidateCert());
        $this->assertEquals('demouser', $account->getUsername());
        $this->assertEquals('demopassword', $account->getPassword());
        $this->assertEquals('oauth', $account->getAuthentication());
        $this->assertEquals(60, $account->getTimeout());

        $this->assertIsArray($account->getFoldersTowatch());
        $this->assertNotEmpty($account->getFoldersTowatch());
        $this->assertCount(2, $account->getFoldersTowatch());

        $this->assertIsArray($account->getMimeTypesToWatch());
        $this->assertNotEmpty($account->getMimeTypesToWatch());
        $this->assertCount(2, $account->getMimeTypesToWatch());

        $this->assertIsArray($account->getHandlers());
        $this->assertNotEmpty($account->getHandlers());
        $this->assertCount(2, $account->getHandlers());
    }

    public function testWriteAccountInformation(): void
    {
        $this->configureMailAccountOptions();
        $this->configureMailAccountWatchOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
                '--folder' => ['INBOX', 'Accounts/invoice@nowhere.all'],
                '--mimetype' => ['text/xml', 'application/pdf'],
                '--handler' => [ZugferdMailHandlerNull::class, ZugferdMailHandlerDeleteMessage::class],
            ],
            $this->definition
        );

        $account = $this->createMailAccountFromOptions($arrayInput);
        $account->setIdentifier('test');

        $outputFormatter = new OutputFormatter();
        $testOutputInterface = new TestOutputInterface;
        $testOutputInterface->setFormatter($outputFormatter);

        $this->writeAccountInformation($testOutputInterface, $account);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(5, $outputs);

        $this->assertEquals("┌──────┬───────────┬──────┬──────────┬────────────┬──────────────┬────────────────┬──────────┐", $outputs[0]);
        $this->assertEquals("│<info> ID   </info>│<info> Host      </info>│<info> Port </info>│<info> Protocol </info>│<info> Encryption </info>│<info> ValidateCert </info>│<info> Authentication </info>│<info> Username </info>│", $outputs[1]);
        $this->assertEquals("├──────┼───────────┼──────┼──────────┼────────────┼──────────────┼────────────────┼──────────┤", $outputs[2]);
        $this->assertEquals("│ test │ 127.0.0.1 │ 993  │ pop3     │ tls        │ Yes          │ oauth          │ demouser │", $outputs[3]);
        $this->assertEquals("└──────┴───────────┴──────┴──────────┴────────────┴──────────────┴────────────────┴──────────┘", $outputs[4]);
    }

    public function testWriteAccountFoldersToWatch(): void
    {
        $this->configureMailAccountOptions();
        $this->configureMailAccountWatchOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
                '--folder' => ['INBOX', 'Accounts/invoice@nowhere.all'],
                '--mimetype' => ['text/xml', 'application/pdf'],
                '--handler' => [ZugferdMailHandlerNull::class, ZugferdMailHandlerDeleteMessage::class],
            ],
            $this->definition
        );

        $account = $this->createMailAccountFromOptions($arrayInput);
        $account->setIdentifier('test');

        $outputFormatter = new OutputFormatter();
        $testOutputInterface = new TestOutputInterface;
        $testOutputInterface->setFormatter($outputFormatter);

        $this->writeAccountFoldersToWatch($testOutputInterface, $account);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(6, $outputs);

        $this->assertEquals("┌──────────────────────────────┐", $outputs[0]);
        $this->assertEquals("│<info> Folder                       </info>│", $outputs[1]);
        $this->assertEquals("├──────────────────────────────┤", $outputs[2]);
        $this->assertEquals("│ INBOX                        │", $outputs[3]);
        $this->assertEquals("│ Accounts/invoice@nowhere.all │", $outputs[4]);
        $this->assertEquals("└──────────────────────────────┘", $outputs[5]);
    }

    public function testWriteAccountMimeTypesToWatch(): void
    {
        $this->configureMailAccountOptions();
        $this->configureMailAccountWatchOptions();

        $arrayInput = new ArrayInput(
            [
                '--host' => '127.0.0.1',
                '--port' => '993',
                '--protocol' => 'pop3',
                '--encryption' => 'tls',
                '--validateCert' => null,
                '--username' => 'demouser',
                '--password' => 'demopassword',
                '--authentication' => 'oauth',
                '--timeout' => 60,
                '--folder' => ['INBOX', 'Accounts/invoice@nowhere.all'],
                '--mimetype' => ['text/xml', 'application/pdf'],
                '--handler' => [ZugferdMailHandlerNull::class, ZugferdMailHandlerDeleteMessage::class],
            ],
            $this->definition
        );

        $account = $this->createMailAccountFromOptions($arrayInput);
        $account->setIdentifier('test');

        $outputFormatter = new OutputFormatter();
        $testOutputInterface = new TestOutputInterface;
        $testOutputInterface->setFormatter($outputFormatter);

        $this->writeAccountMimeTypesToWatch($testOutputInterface, $account);

        $outputs = $testOutputInterface->getOutputs();

        $this->assertIsArray($outputs);
        $this->assertNotEmpty($outputs);
        $this->assertCount(6, $outputs);

        $this->assertEquals("┌─────────────────┐", $outputs[0]);
        $this->assertEquals("│<info> MimeTypes       </info>│", $outputs[1]);
        $this->assertEquals("├─────────────────┤", $outputs[2]);
        $this->assertEquals("│ text/xml        │", $outputs[3]);
        $this->assertEquals("│ application/pdf │", $outputs[4]);
        $this->assertEquals("└─────────────────┘", $outputs[5]);
    }
}
