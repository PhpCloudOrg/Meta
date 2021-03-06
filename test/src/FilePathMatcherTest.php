<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeQualityChecker\FilePathMatcher\FilePathMatcher;
use PHPUnit\Framework\TestCase;

class FilePathMatcherTest extends TestCase
{
    /**
     * @dataProvider provideDataForMatchTest
     * @param string $dir
     * @param string $extension
     * @param string $file_path
     * @param bool   $expected_match
     */
    public function testWillProperlyMatchFilePath(
        string $dir,
        string $extension,
        string $file_path,
        bool $expected_match
    )
    {
        $this->assertSame(
            $expected_match,
            (new FilePathMatcher($dir, $extension))->shouldCheck($file_path)
        );
    }

    public function provideDataForMatchTest(): array
    {
        return [
            ['src', 'php', 'src/AwesomeFile.php', true],
            ['src', 'php', 'src/Namespace/Subspace/AwesomeFile.php', true],
            ['src', 'php', 'src/index.html', false],
            ['src', 'php', 'src2/AwesomeFile.php', false],
        ];
    }
}
