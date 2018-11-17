<?php

/*
 * This file is part of the PhpCloud.org Meta project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

require_once 'vendor/autoload.php';

use PhpCloudOrg\Meta\CodeStyleFixer\ConfigFactory;

$code_style = new ConfigFactory(
    __DIR__,
    'PhpCloud.org Meta',
    'PhpCloud.org Core Team',
    'core@phpcloud.org'
);

return $code_style->getConfig();
