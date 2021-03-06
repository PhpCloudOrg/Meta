<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck;

use PhpCloudOrg\Meta\CodeQualityChecker\FileSignatureResolver\FileSignatureResolverInterface;
use PhpCloudOrg\Meta\CodeRepository\CodeRepositoryInterface;
use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;

abstract class QualityCheck implements QualityCheckInterface
{
    protected $repository;
    private $command_runner;
    private $file_signature_resolver;
    private $output_callback;

    public function __construct(
        CodeRepositoryInterface $repository,
        CommandRunnerInterface $command_runner,
        FileSignatureResolverInterface $file_signature_resolver,
        callable $output_callback = null
    )
    {
        $this->repository = $repository;
        $this->command_runner = $command_runner;
        $this->file_signature_resolver = $file_signature_resolver;
        $this->output_callback = $output_callback;
    }

    protected function runCommand(string $command, string $working_directory = null)
    {
        $this->command_runner->runCommand(
            $command,
            $working_directory ?? $this->repository->getRepositoryPath()
        );
    }

    protected function getFileSignature(string $file_path): string
    {
        return $this->file_signature_resolver->getSignature($this->repository->getFilePath($file_path));
    }

    protected function printToOutput(string $message): void
    {
        if ($this->output_callback) {
            call_user_func($this->output_callback, "{$message}\n");
        }
    }
}
