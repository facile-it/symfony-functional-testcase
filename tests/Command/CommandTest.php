<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\Command;

use Facile\SymfonyFunctionalTestCase\WebTestCase;

class CommandTest extends WebTestCase
{
    /**
     * This method tests both the default setting of `runCommand()` and the kernel reusing, as, to reuse kernel,
     * it is needed a kernel is yet instantiated. So we test these two conditions here, to not repeat the code.
     */
    public function testRunCommandWithoutOptionsAndReuseKernel(): void
    {
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test');

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());

        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test', [], true);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());
    }

    public function testRunCommandWithoutOptionsAndNotReuseKernel(): void
    {
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test');

        $this->assertSame(0, $commandTester->getStatusCode());

        $this->assertStringContainsString('Environment: test', $commandTester->getDisplay());

        $this->environment = 'prod';
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test', [], false);

        $this->assertStringContainsString('Environment: prod', $commandTester->getDisplay());
    }

    public function testRunCommandStatusCode(): void
    {
        $commandTester = $this->runCommand('facileitsymfonyfunctionaltestcase:test-status-code');

        $this->assertSame(10, $commandTester->getStatusCode());
    }

    public function testPrepareCommandTester(): void
    {
        $commandTester = $this->prepareCommandTester('facileitsymfonyfunctionaltestcase:test-status-code');

        $commandTester->execute([]);

        $this->assertSame(10, $commandTester->getStatusCode());
    }
}
