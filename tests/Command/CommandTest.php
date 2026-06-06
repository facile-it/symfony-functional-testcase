<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\Command;

use Facile\SymfonyFunctionalTestCase\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Kernel;

class CommandTest extends WebTestCase
{
    /**
     * This method tests both the default setting of `runCommandTester()` and the kernel reusing, as, to reuse kernel,
     * it is needed a kernel is yet instantiated. So we test these two conditions here, to not repeat the code.
     */
    public function testRunCommandWithoutOptionsAndReuseKernel(): void
    {
        $this->skipIfSymfonyAfter81();

        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test');

        $this->assertInstanceOf(CommandTester::class, $commandTester);
        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());

        /** @phpstan-ignore-next-line argument.type */
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test', [], true);

        $this->assertInstanceOf(CommandTester::class, $commandTester);
        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());
    }

    public function testRunCommandTesterGenericMethod(): void
    {
        $commandTester = $this->runCommandTester('facileitsymfonyfunctionaltestcase:test');

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());
    }

    public function testRunCommandTesterSpecificMethod(): void
    {
        $commandTester = $this->runCommandTester('facileitsymfonyfunctionaltestcase:test');

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());

        $commandTester = $this->runCommandTester('facileitsymfonyfunctionaltestcase:test', [], true);

        /** @phpstan-ignore-next-line method.impossibleType */
        $this->assertInstanceOf(CommandTester::class, $commandTester);
        $this->assertSame(0, $commandTester->getStatusCode());

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());
    }

    public function testRunCommandParentStaticMethod(): void
    {
        $this->skipIfSymfonyBefore81();

        $commandTester = self::runCommand('facileitsymfonyfunctionaltestcase:test');

        $this->assertSame(0, $commandTester->statusCode);
        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());
    }

    public function testRunCommandWithoutOptionsAndNotReuseKernel(): void
    {
        $this->skipIfSymfonyAfter81();

        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test');

        $this->assertInstanceOf(CommandTester::class, $commandTester);
        $this->assertSame(0, $commandTester->getStatusCode());

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());

        $this->environment = 'prod';
        /** @phpstan-ignore-next-line argument.type */
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test', [], false);

        $this->assertStringContainsString('Environment: prod', $commandTester->getDisplay());
    }

    public function testRunCommandStatusCode(): void
    {
        $this->skipIfSymfonyAfter81();

        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test-status-code');

        $this->assertInstanceOf(CommandTester::class, $commandTester);
        $this->assertSame(10, $commandTester->getStatusCode());
    }

    public function testPrepareCommandTester(): void
    {
        $commandTester = $this->prepareCommandTester('facileitsymfonyfunctionaltestcase:test-status-code');

        $commandTester->execute([]);

        $this->assertSame(10, $commandTester->getStatusCode());
    }

    private function skipIfSymfonyAfter81(): void
    {
        if (Kernel::VERSION_ID >= 8_01_00) {
            $this->markTestSkipped('Test not executable under Symfony 8.1+');
        }
    }

    private function skipIfSymfonyBefore81(): void
    {
        if (Kernel::VERSION_ID < 8_01_00) {
            $this->markTestSkipped('Test not executable under Symfony below 8.1');
        }
    }
}
