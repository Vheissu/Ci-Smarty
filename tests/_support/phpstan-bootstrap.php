<?php

declare(strict_types=1);

// Boot CodeIgniter the same way the test suite does, so PHPStan and the
// CodeIgniter PHPStan extension can see the framework's configuration.
define('HOMEPATH', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
define('CONFIGPATH', HOMEPATH . 'vendor/codeigniter4/framework/app/Config/');
define('PUBLICPATH', HOMEPATH . 'vendor/codeigniter4/framework/public/');

require HOMEPATH . 'vendor/codeigniter4/framework/system/Test/bootstrap.php';
