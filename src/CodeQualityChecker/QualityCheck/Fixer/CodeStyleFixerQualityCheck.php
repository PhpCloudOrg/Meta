<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\Fixer;

use Exception;
use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcherInterface;
use PhpCloudOrg\Meta\CodeQualityChecker\FileSignatureResolver\FileSignatureResolverInterface;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\CheckException;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\QualityCheck;
use PhpCloudOrg\Meta\CodeRepository\CodeRepositoryInterface;
use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;

class CodeStyleFixerQualityCheck extends QualityCheck
{
    private $php_cs_fixer_binary;
    private $php_cs_fixer_config_file;
    private $file_path_matchers;

    public function __construct(
        CodeRepositoryInterface $repository,
        CommandRunnerInterface $command_runner,
        FileSignatureResolverInterface $file_signature_resolver,
        string $php_cs_fixer_binary = 'php-cs-fixer',
        string $php_cs_fixer_config_file = '.php_cs.php',
        callable $output_callback = null,
        FilePathMatcherInterface ...$file_path_matchers
    )
    {
        parent::__construct($repository, $command_runner, $file_signature_resolver, $output_callback);

        $this->php_cs_fixer_binary = $php_cs_fixer_binary;
        $this->php_cs_fixer_config_file = $php_cs_fixer_config_file;
        $this->file_path_matchers = $file_path_matchers;
    }

    public function check(string $project_path, array $changed_files): void
    {
        $this->printToOutput('Running PHP Code Style Fixer...');
        $this->printToOutput('');

        foreach ($changed_files as $changed_file) {
            if ($this->shouldFixFile($changed_file)) {
                $this->fixFile($changed_file);
            } else {
                $this->printToOutput(sprintf('    Skipping %s...', $changed_file));
            }
        }

        $this->printToOutput('');
    }

    private function shouldFixFile(string $file_path): bool
    {
        if (!$this->repository->fileExists($file_path)) {
            return false;
        }

        foreach ($this->file_path_matchers as $file_path_matcher) {
            if ($file_path_matcher->shouldCheck($file_path)) {
                return true;
            }
        }

        return false;
    }

    private function fixFile(string $file_path): void
    {
        $this->printToOutput(sprintf('    Fixing file %s...', $file_path));

        try {
            $file_signature = $this->getFileSignature($file_path);

            $this->runCommand(
                $this->prepareCodeStyleFixerCommand(
                    $this->php_cs_fixer_binary,
                    $this->php_cs_fixer_config_file,
                    $file_path
                )
            );

            if ($file_signature != $this->getFileSignature($file_path)) {
                $this->printToOutput(sprintf('    File %s has been modified. Staging changes...', $file_path));
                $this->repository->stageFile($file_path);
            }
        } catch (Exception $e) {
            throw new CheckException(
                sprintf('Failed to fix file %s. Run php-cs-fixer on your code.', $file_path),
                0,
                $e
            );
        }
    }

    private function prepareCodeStyleFixerCommand(
        string $php_cs_fixer_binary,
        string $php_cs_fixer_config_file,
        string $file_path
    ): string
    {
        return sprintf(
            '%s --config=%s --verbose fix %s',
            $php_cs_fixer_binary,
            escapeshellarg($php_cs_fixer_config_file),
            escapeshellarg($file_path)
        );
    }
}
