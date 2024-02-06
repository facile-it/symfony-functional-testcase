<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeBundle extends Bundle
{
    public function build(ContainerBuilder $container): void {}
}
