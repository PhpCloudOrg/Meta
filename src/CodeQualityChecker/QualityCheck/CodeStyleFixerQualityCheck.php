<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck;

use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcherInterface;
use PhpCloudOrg\Meta\CommandRunner\CommandRunnerInterface;

class CodeStyleFixerQualityCheck extends QualityCheck
{
    private $project_path;
    private $command_runner;
    private $php_cs_fixer_binary;
    private $php_cs_fixer_config_file;
    private $file_path_matchers;

    public function __construct(
        string $project_path,
        CommandRunnerInterface $command_runner,
        string $php_cs_fixer_binary = 'php-cs-fixer',
        string $php_cs_fixer_config_file = '.php_cs.php',
        FilePathMatcherInterface ...$file_path_matchers
    )
    {
        $this->project_path = $project_path;
        $this->command_runner = $command_runner;
        $this->php_cs_fixer_binary = $php_cs_fixer_binary;
        $this->php_cs_fixer_config_file = $php_cs_fixer_config_file;
        $this->file_path_matchers = $file_path_matchers;
    }

    public function check(string $project_path, array $changed_files): void
    {
        foreach ($changed_files as $changed_file) {
            foreach ($this->file_path_matchers as $file_path_matcher) {
                if ($file_path_matcher->shouldCheck($changed_file)) {
                    $this->fixFile($this->project_path, $changed_file);
                }
            }
        }
    }

    private function fixFile(string $project_path, string $file_path): void
    {
        $this->command_runner->runCommand(
            $this->prepareCodeStyleFixerCommand(
                $this->php_cs_fixer_binary,
                $this->php_cs_fixer_config_file,
                $file_path
            ),
            $project_path
        );
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
