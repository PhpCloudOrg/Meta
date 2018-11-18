<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace PhpCloudOrg\Meta\Test;

use PhpCloudOrg\Meta\CommandRunner\CommandRunner;
use PHPUnit\Framework\TestCase;

class CommandRunnerTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     * @expectedExceptionMessage The command "exit 255" failed
     */
    public function testWillThrowExceptionOnUnsucessfulRun()
    {
        (new CommandRunner(__DIR__))->runCommand('exit 255');
    }

    public function testWillRunCommand()
    {
        $this->assertContains(
            basename(__FILE__),
            (new CommandRunner(__DIR__))->runCommand('ls')
        );
    }
}
