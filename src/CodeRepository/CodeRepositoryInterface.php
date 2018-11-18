<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeRepository;

interface CodeRepositoryInterface
{
    public function getRepositoryPath(): string;
    public function getChangedFiles(): iterable;
}
