<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Facile\SymfonyFunctionalTestCase\Tests\App\AppKernel;
use Facile\SymfonyFunctionalTestCase\WebTestCase;
use PHPUnit\Framework\ExpectationFailedException;

class WebTestCaseTest extends WebTestCase
{
    public function setUp(): void
    {
        static::$class = AppKernel::class;
    }

    public static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    public function testGetContainer(): void
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasParameter('kernel.environment'));
    }

    #[IgnoreDeprecations]
    public function testGetDependencyInjectionContainer(): void
    {
        $container = $this->getDependencyInjectionContainer();

        $this->assertTrue($container->hasParameter('kernel.environment'));
    }

    public function testCallNotGettingMoreCalls(): void
    {
        $this->expectExceptionMessage('Method fakeMethod is not supported');

        /** @phpstan-ignore-next-line */
        $this->fakeMethod();
    }

    /**
     * Call methods from Symfony to ensure the Controller works.
     */
    public function testIndex(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCodeIsSuccessful($client);
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('Hello world', $content);
    }

    #[Depends('testIndex')]
    public function testIndexAssertStatusCode(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCode(200, $client);
    }

    #[Depends('testIndex')]
    public function testIndexAssertIsSuccessful(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCodeIsSuccessful($client);
    }

    #[Depends('testIndex')]
    public function testIndexAssertIsRedirect(): void
    {
        $path = '/redirect';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCodeIsRedirect($client);
    }

    #[Depends('testIndex')]
    public function testAssertStatusCodeFail(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('-1');

        $this->assertStatusCode(-1, $client);
    }

    #[Depends('testIndex')]
    public function testAssertStatusCodeFailWithMessage(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Custom message');

        $this->assertStatusCode(-1, $client, 'Custom message');
    }

    public function test404Error(): void
    {
        $path = '/missing_page';
        $client = static::createClient();

        try {
            $client->request('GET', $path);
        } catch (NotFoundHttpException) {
            $this->markTestSkipped('Ignore this due to --prefer-lowest CI build, see https://travis-ci.org/facile-it/symfony-functional-testcase/jobs/633306679');
        }

        $this->assertStatusCode(404, $client);
    }
}
