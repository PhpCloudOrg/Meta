<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

require_once 'vendor/autoload.php';

use PhpCloudOrg\Meta\CodeQualityChecker\CodeQualityChecker;
use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcher;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\CodeStyleFixerQualityCheck;
use PhpCloudOrg\Meta\CodeRepository\CodeRepository;
use PhpCloudOrg\Meta\CommandRunner\CommandRunner;

$command_runner = new CommandRunner(__DIR__);
$code_repository = new CodeRepository(__DIR__, $command_runner);

return new CodeQualityChecker(
    $code_repository,
    new CodeStyleFixerQualityCheck(
        $code_repository,
        $command_runner,
        'php vendor/bin/php-cs-fixer',
        '.php_cs.php',
        function (string $message) {
            print "{$message}\n";
        },
        new FilePathMatcher('src', 'php'),
        new FilePathMatcher('test/src', 'php')
    )
);
