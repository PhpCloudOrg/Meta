<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CodeStyleFixer\ConfigFactory;
use PhpCsFixer\ConfigInterface;
use PHPUnit\Framework\TestCase;

class CodeStyleFixerTest extends TestCase
{
    private $project_root;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->project_root = dirname(dirname(__DIR__));
    }

    /**
     * @dataProvider produceExpectedFilesForFinderInclusionTest
     * @param string $file_path_expected_to_find
     */
    public function testFinderInclusion(string $file_path_expected_to_find)
    {
        $config = $this->getFactory()->getConfig();

        $this->assertContains(
            $file_path_expected_to_find,
            $this->getFilesFoundByFinder($config)
        );
    }

    public function produceExpectedFilesForFinderInclusionTest()
    {
        return [
            [$this->project_root . '/src/CodeStyleFixer/ConfigFactoryInterface.php'],
            [$this->project_root . '/test/bootstrap.php'],
            [__FILE__],
        ];
    }

    /**
     * @dataProvider produceExpectedFilesForFinderExclusionTest
     * @param string $file_path_expected_to_find
     */
    public function testFinderExclusion(string $file_path_expected_to_find)
    {
        $config = $this->getFactory()->getConfig();

        $this->assertNotContains(
            $file_path_expected_to_find,
            $this->getFilesFoundByFinder($config)
        );
    }

    public function produceExpectedFilesForFinderExclusionTest()
    {
        return [
            [$this->project_root . '/.php_cs.cache'],
            [$this->project_root . '/.php_cs.php'],
            [$this->project_root . '/test/log/.gitignore'],
        ];
    }

    private function getFactory(
        string $project_root = null,
        string $project_name = null,
        string $project_author = null,
        string $project_contact_address = null
    )
    {
        return (new ConfigFactory(
            $project_root ?? $this->project_root,
            $project_name ?? 'Project',
            $project_author ?? 'Author',
            $project_contact_address ?? 'author@example.com'
        ));
    }

    private function getFilesFoundByFinder(ConfigInterface $config): array
    {
        return array_keys(iterator_to_array($config->getFinder()));
    }
}
