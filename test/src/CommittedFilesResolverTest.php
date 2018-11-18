<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeRepository\CodeRepository;
use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommittedFilesResolverTest extends TestCase
{
    public function testWillGetCommittedFilesFromDiffCommandOutput()
    {
        $command_output = implode(
            "\n",
            [
                'A       src/CodeQualityChecker/CodeQualityChecker.php',
                'A       src/CodeRepository/GitRepository.php',
                'A       src/CommandRunner/CommandRunner.php',
                'A       src/CommandRunner/CommandRunnerInterface.php',
                'A       src/CommittedFilesResolver/CommittedFilesResolverInterface.php',
                'A       test/src/CommandRunnerTest.php',
                'A       test/src/GitCommittedFilesResolverTest.php',
            ]
        );

        /** @var CommandRunnerInterface|MockObject $command_runner */
        $command_runner = $this->createMock(CommandRunnerInterface::class);

        $command_runner
            ->expects($this->once())
            ->method('runCommand')
            ->willReturn($command_output);

        $repo = new CodeRepository(__DIR__, $command_runner);

        $committed_files = $repo->getCommittedFiles();

        $this->assertContains('test/src/CommandRunnerTest.php', $committed_files);
        $this->assertNotContains(dirname(__DIR__) . '/bootstrap.php', $committed_files);
    }
}
