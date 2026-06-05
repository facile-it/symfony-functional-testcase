<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class TestCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct('facileitsymfonyfunctionaltestcase:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Symfony version: ' . Kernel::VERSION_ID);
        $output->writeln('Environment: ' . $this->kernel->getEnvironment());
        $output->writeln('Verbosity level set: ' . $output->getVerbosity());

        return 0;
    }
}
