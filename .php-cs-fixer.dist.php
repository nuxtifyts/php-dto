<?php

$finder = new PhpCsFixer\Finder()->in(__DIR__ . DIRECTORY_SEPARATOR . 'src');

return new PhpCsFixer\Config()
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder($finder);
