<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker;

use LogicException;
use PhpCloudOrg\Meta\CodeQualityChecker\QualityCheck\QualityCheckInterface;
use PhpCloudOrg\Meta\CodeRepository\CodeRepositoryInterface;
use Throwable;

class CodeQualityChecker
{
    private $code_repository;
    private $output_callback;
    private $quality_checks;

    public function __construct(
        CodeRepositoryInterface $code_repository,
        ?callable $output_callback,
        QualityCheckInterface ...$quality_checks
    )
    {
        $this->code_repository = $code_repository;
        $this->output_callback = $output_callback;

        if (empty($quality_checks)) {
            throw new LogicException("Code quality can't be checked without quality checks.");
        }

        $this->quality_checks = $quality_checks;
    }

    public function check()
    {
        $changed_files = $this->code_repository->getChangedFiles();

        foreach ($this->quality_checks as $quality_check) {
            $quality_check->check($this->code_repository->getRepositoryPath(), $changed_files);
        }
    }

    public function communicateFailure(Throwable $e, callable $output_callback)
    {
        $this->printMessage('');
        $this->printMessage('Configured checks failed!');

        $this->outputExceptionDetails($e, $output_callback, '    ');
    }

    private function outputExceptionDetails(Throwable $e, callable $output_callback, string $indent)
    {
        $this->printMessage('');
        $this->printMessage(
            sprintf(
                $indent . '(%s) %s',
                get_class($e),
                $e->getMessage()
            )
        );
        $this->printMessage(
            sprintf(
                $indent . 'File %s on line %d',
                $e->getFile(),
                $e->getLine()
            )
        );

        if ($e->getPrevious()) {
            $this->outputExceptionDetails(
                $e->getPrevious(),
                $output_callback,
                $indent . '    '
            );
        }
    }

    private function printMessage(string $message): void
    {
        if ($this->output_callback) {
            call_user_func($this->output_callback, "{$message}\n");
        }
    }
}
