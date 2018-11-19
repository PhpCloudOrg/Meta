<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\CodeQualityChecker\FileSignatureResolver;

use RuntimeException;

class FileSignatureResolver implements FileSignatureResolverInterface
{
    public function getSignature(string $file_path): string
    {
        if (is_file($file_path)) {
            return md5_file($file_path);
        }

        throw new RuntimeException(sprintf('File %s not found.', $file_path));
    }
}
