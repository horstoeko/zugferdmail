<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
    ])
    ->withSkip([
        __DIR__ . '/../examples',
        __DIR__ . '/../make',
    ])
    ->withSkip([
        //ClassPropertyAssignToConstructorPromotionRector::class
    ])
    ->withPhpSets(php73: true)
    ->withConfiguredRule(EncapsedStringsToSprintfRector::class, [
        'always' => true,
    ])
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        deadCode: false,
        typeDeclarations: true,
        privatization: true,
        naming: false,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        phpunitCodeQuality: true
    );
