<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeQualityChecker\FileSignatureResolver\FileSignatureResolver;
use PHPUnit\Framework\TestCase;

class FileSignatureResolverTest extends TestCase
{
    public function testWillReturnMd5Hash()
    {
        $file_to_check = dirname(__DIR__) . '/fixtures/codestyle-fixer-scan-test/file_to_find.php';

        $this->assertSame(md5_file($file_to_check), (new FileSignatureResolver())->getSignature($file_to_check));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage not found
     */
    public function testWillThrowExceptionOnMissingFile()
    {
        $file_to_check = dirname(__DIR__) . '/fixtures/not-found.php';

        $this->assertFileNotExists($file_to_check);

        (new FileSignatureResolver())->getSignature($file_to_check);
    }
}
