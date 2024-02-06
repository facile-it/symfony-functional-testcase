<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @method ContainerInterface getContainer()
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /** @var string */
    protected $environment = 'test';

    /** @var ContainerInterface[] */
    protected $containers = [];

    /**
     * Returns a CommandTester for the console command with the provided name.
     * It allows to reuse the same kernel that the test uses, so that you can
     * reach in if needed.
     */
    protected function prepareCommandTester(string $name, bool $reuseKernel = false): CommandTester
    {
        if ($reuseKernel) {
            /** @var KernelInterface $kernel */
            $kernel = $this->getContainer()->get('kernel');
        } else {
            $kernel = self::bootKernel(['environment' => $this->environment]);
        }

        $application = new Application($kernel);
        $command = $application->find($name);

        return new CommandTester($command);
    }

    /**
     * Builds up the environment to run the given command.
     *
     * @param array<string, mixed> $params
     */
    protected function runCommand(string $name, array $params = [], bool $reuseKernel = false): CommandTester
    {
        $commandTester = $this->prepareCommandTester($name, $reuseKernel);
        $commandTester->execute(
            $params,
            [
                'interactive' => false,
            ]
        );

        return $commandTester;
    }

    /**
     * Keep support of Symfony < 5.3.
     *
     * @see https://github.com/liip/LiipFunctionalTestBundle/pull/584
     *
     * @param mixed|null $arguments
     */
    public function __call(string $name, $arguments): ContainerInterface
    {
        if ('getContainer' === $name) {
            if (method_exists(parent::class, $name)) {
                return parent::getContainer();
            }

            return $this->getDependencyInjectionContainer();
        }

        throw new \Exception("Method {$name} is not supported.");
    }

    /**
     * Get an instance of the dependency injection container.
     * (this creates a kernel *without* parameters).
     */
    protected function getDependencyInjectionContainer(): ContainerInterface
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
                $container = $container->get('test.service_container');
                $this->assertInstanceOf(ContainerInterface::class, $container);
            }

            $this->containers[$cacheKey] = $container;
        }

        return $this->containers[$cacheKey];
    }

    /**
     * Asserts that the HTTP response code of the last request performed by
     * $client matches the expected code. If not, raises an error with more
     * information.
     */
    public function assertStatusCode(int $expectedStatusCode, KernelBrowser $client, string $message = ''): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertSame($expectedStatusCode, $response->getStatusCode(), $message);
    }

    protected function assertStatusCodeIsSuccessful(KernelBrowser $client): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertTrue($response->isSuccessful(), 'HTTP status code not successful: ' . $response->getStatusCode());
    }

    protected function assertStatusCodeIsRedirect(KernelBrowser $client): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertTrue($response->isRedirect(), 'HTTP status code not a redirect: ' . $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        foreach ($this->containers as $container) {
            if (method_exists($container, 'reset')) {
                $container->reset();
            }
        }

        $this->containers = [];

        parent::tearDown();
    }
}
