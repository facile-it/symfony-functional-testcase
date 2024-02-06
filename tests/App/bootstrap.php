<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../../vendor/autoload.php';

if (class_exists(AnnotationRegistry::class)) {
    AnnotationRegistry::registerLoader([$loader, 'loadClass']);
}

return $loader;
