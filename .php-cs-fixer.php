<?php declare(strict_types=1);

use function MLL\PhpCsFixerConfig\risky;

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->notPath('/examples\/.*\/expected/')
    ->notPath('/examples\/.*\/generated/')
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return risky($finder);
