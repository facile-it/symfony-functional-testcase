<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var string */
    protected $environment = 'test';

    /** @var ContainerInterface[] */
    protected $containers;

    /**
     * Builds up the environment to run the given command.
     *
     * @param string $name
     * @param array  $params
     * @param bool   $reuseKernel
     *
     * @return CommandTester
     */
    protected function runCommand(string $name, array $params = [], bool $reuseKernel = false): CommandTester
    {
        if (!$reuseKernel) {
            if (null !== static::$kernel) {
                static::$kernel->shutdown();
            }

            $kernel = static::$kernel = static::createKernel(['environment' => $this->environment]);
            $kernel->boot();
        } else {
            $kernel = $this->getContainer()->get('kernel');
        }

        $application = new Application($kernel);

        $command = $application->find($name);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(['command' => $command->getName()], $params),
            [
                'interactive' => false,
                'decorated' => $this->getDecorated(),
                'verbosity' => $this->getVerbosityLevel(),
            ]
        );

        return $commandTester;
    }

    /**
     * Get an instance of the dependency injection container.
     * (this creates a kernel *without* parameters).
     *
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        $cacheKey = $this->environment;
        if (empty($this->containers[$cacheKey])) {
            $options = [
                'environment' => $this->environment,
            ];
            $kernel = $this->createKernel($options);
            $kernel->boot();

            $container = $kernel->getContainer();
            if ($container->has('test.service_container')) {
                $this->containers[$cacheKey] = $container->get('test.service_container');
            } else {
                $this->containers[$cacheKey] = $container;
            }
        }

        return $this->containers[$cacheKey];
    }

    /**
     * Asserts that the HTTP response code of the last request performed by
     * $client matches the expected code. If not, raises an error with more
     * information.
     *
     * @param int    $expectedStatusCode
     * @param Client $client
     */
    public static function assertStatusCode(int $expectedStatusCode, Client $client): void
    {
        HttpAssertions::assertStatusCode($expectedStatusCode, $client);
    }

    protected function tearDown(): void
    {
        foreach ($this->containers as $container) {
            if ($container instanceof ResettableContainerInterface) {
                $container->reset();
            }
        }

        $this->containers = [];

        parent::tearDown();
    }
}
