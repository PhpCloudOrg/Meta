<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher;

interface FilePathMatcherInterface
{
    public function shouldCheck(string $file_path): bool;
}
