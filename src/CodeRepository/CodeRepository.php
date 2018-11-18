<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeRepository;

use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;

class CodeRepository implements CodeRepositoryInterface
{
    private $repository_path;
    private $command_runner;
    private $git_binary;

    public function __construct(
        string $repository_path,
        CommandRunnerInterface $command_runner,
        string $git_binary = 'git'
    )
    {
        $this->git_binary = $git_binary;
        $this->repository_path = $repository_path;
        $this->command_runner = $command_runner;
    }

    public function getCommittedFiles(): iterable
    {
        $lines = explode(
            "\n",
            trim(
                $this->command_runner->runCommand(
                    $this->prepareGitCommand('diff --cached --name-status --diff-filter=ACM')
                )
            )
        );

        $result = [];

        foreach ($lines as $line) {
            $result[] = trim(substr($line, 1));
        }

        return $result;
    }

    private function prepareGitCommand($command): string
    {
        return sprintf('%s %s', $this->git_binary, $command);
    }
}
