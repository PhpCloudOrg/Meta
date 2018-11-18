<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

use PhpCloudOrg\Meta\CodeQualityChecker\CodeQualityChecker;
use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcher;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\CodeStyleFixerQualityCheck;
use PhpCloudOrg\Meta\CodeRepository\CodeRepository;
use PhpCloudOrg\Meta\CommandRunner\CommandRunner;

$command_runner = new CommandRunner(__DIR__);

return new CodeQualityChecker(
    __DIR__,
    new CodeRepository(__DIR__, $command_runner),
    new CodeStyleFixerQualityCheck(
        __DIR__,
        $command_runner,
        'php vendor/bin/php-cs-fixer',
        '.php_cs.php',
        [
            new FilePathMatcher('src', 'php'),
            new FilePathMatcher('test/src', 'php'),
        ]
    )
);
