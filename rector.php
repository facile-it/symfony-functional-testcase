<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.neon',
    ])
    ->withPhpSets()
    ->withImportNames(importShortClasses: false)
    ->withComposerBased(
        phpunit: true,
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    )
;
