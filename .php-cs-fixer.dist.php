<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'blank_line_after_opening_tag' => false,
        'cast_spaces' => ['space' => 'none'],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => ['strategy' => 'enforce'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'yoda_style' => ['identical' => false],
    ])
    ->setFinder(
        (new Finder)
            ->in(__DIR__ . '/src')
            ->append([__DIR__ . '/tests'])
            ->notPath('vendor')
            ->name(['*.php', '*.phpt'])
            ->ignoreVCS(true)
    )
;
