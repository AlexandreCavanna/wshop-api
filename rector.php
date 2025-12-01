<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/config', __DIR__ . '/tests'])
    ->withSkip([
        __DIR__ . '/config/bundles.php',
        __DIR__ . '/config/reference.php',
        RenamePropertyToMatchTypeRector::class => __DIR__ . '/src/Entity/Store.php',
    ])
    ->withRootFiles()
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true
    )
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true);
