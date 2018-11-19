<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcher;
use PhpCloudOrg\Meta\CodeQualityChecker\FileSignatureResolver\FileSignatureResolverInterface;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\CodeStyleFixerQualityCheck;
use PhpCloudOrg\Meta\CodeRepository\CodeRepositoryInterface;
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

        /** @var MockObject|CodeRepositoryInterface $repository */
        $repository = $this->createMock(CodeRepositoryInterface::class);
        $repository
            ->expects($this->atLeast(3))
            ->method('fileExists')
            ->willReturn(true);

        /** @var MockObject|FileSignatureResolverInterface $file_signature_resolver */
        $file_signature_resolver = $this->createMock(FileSignatureResolverInterface::class);
        $file_signature_resolver
            ->expects($this->atLeast(3))
            ->method('getSignature')
            ->willReturn('file-sig');

        $check = new CodeStyleFixerQualityCheck(
            $repository,
            $command_runner,
            $file_signature_resolver,
            'php-cs-fixer',
            '.php_cs.php',
            null,
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
