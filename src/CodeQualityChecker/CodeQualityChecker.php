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

class CodeQualityChecker
{
    private $code_repository;
    private $quality_checks;

    public function __construct(
        CodeRepositoryInterface $code_repository,
        QualityCheckInterface ...$quality_checks
    )
    {
        $this->code_repository = $code_repository;

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

    public function communicateFailure(\Throwable $e, callable $output_callback)
    {
        call_user_func($output_callback, '');
        call_user_func($output_callback, 'Configured checks failed!');

        $this->outputExceptionDetails($e, $output_callback, '    ');
    }

    private function outputExceptionDetails(\Throwable $e, callable $output_callback, string $indent)
    {
        call_user_func($output_callback, '');
        call_user_func(
            $output_callback,
            sprintf(
                $indent . '(%s) %s',
                get_class($e),
                $e->getMessage()
            )
        );
        call_user_func(
            $output_callback,
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
}
