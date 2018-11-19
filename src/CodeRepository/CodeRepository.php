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

    public function getRepositoryPath(): string
    {
        return $this->repository_path;
    }

    public function getFilePath(string $file_path): string
    {
        return $this->repository_path . '/'. $file_path;
    }

    public function fileExists(string $file_path): bool
    {
        return (bool) file_exists($this->getFilePath($file_path));
    }

    public function getChangedFiles(): iterable
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
            if (empty($line)) {
                continue;
            }

            $result[] = trim(substr($line, 1));
        }

        return $result;
    }

    public function stageFile(string $file_path)
    {
        $this->command_runner->runCommand(
            $this->prepareGitCommand(
                sprintf('add %s', escapeshellarg($file_path))
            )
        );
    }

    private function prepareGitCommand($command): string
    {
        return sprintf('%s %s', $this->git_binary, $command);
    }
}
