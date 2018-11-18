<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcher;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\CodeStyleFixerQualityCheck;
use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CodeStyleFixerQualityCheckTest extends TestCase
{
    public function testWillRunCommandForEachMatchingFile()
    {
        /** @var MockObject|CommandRunnerInterface $command_runner */
        $command_runner = $this->createMock(CommandRunnerInterface::class);
        $command_runner
            ->expects($this->exactly(3))
            ->method('runCommand')
            ->willReturn('');

        $check = new CodeStyleFixerQualityCheck(
            __DIR__,
            $command_runner,
            'php-cs-fixer',
            '.php_cs.php',
            new FilePathMatcher('src', 'php'),
            new FilePathMatcher('test/src', 'php')
        );

        $check->check(
            __DIR__,
            [
                'src/index.html',
                'src/stdClass.php',
                'src/Authentication.php',
                'test/bootstrap.php',
                'test/src/Authentication.php',
            ]
        );
    }
}
