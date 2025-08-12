<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * @return Bundle[]
     */
    public function registerBundles(): array
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new AcmeBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.yml');

        if (Kernel::VERSION_ID >= 5_00_00) {
            if (Kernel::VERSION_ID >= 7_03_00) {
                $loader->load(__DIR__ . '/config_7_3.yml');
            }

            if (Kernel::VERSION_ID >= 6_04_00) {
                $loader->load(__DIR__ . '/config_6_4.yml');
            } elseif (Kernel::VERSION_ID >= 6_03_00) {
                $loader->load(__DIR__ . '/config_6_3.yml');
            }

            $loader->load(__DIR__ . '/config_5.yml');
        }

        if (Kernel::VERSION_ID >= 6_02_00) {
            $loader->load(__DIR__ . '/security.yml');
        } elseif (Kernel::VERSION_ID >= 5_03_00) {
            $loader->load(__DIR__ . '/security_pre_6.2.yml');
        } else {
            $loader->load(__DIR__ . '/security_pre_5.3.yml');
        }
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir() . 'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir() . 'log';
    }

    protected function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/facile-it-testcase/' . (new \ReflectionClass($this))->getShortName() . '/var/';
    }
}
