<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Vheissu\CiSmarty\Config\Smarty as SmartyConfig;
use Vheissu\CiSmarty\SmartyRenderer;

abstract class RendererTestCase extends CIUnitTestCase
{
    protected string $workDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ci-smarty-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir($this->workDirectory)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->workDirectory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST,
            );

            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }

            rmdir($this->workDirectory);
        }
    }

    /**
     * @param array<string, mixed> $overrides
     */
    protected function config(array $overrides = []): SmartyConfig
    {
        $config = new SmartyConfig();

        $config->templateDirectories = [SUPPORTPATH . 'Views'];
        $config->themeDirectory      = SUPPORTPATH . 'themes';
        $config->compileDirectory    = $this->workDirectory . DIRECTORY_SEPARATOR . 'compiled';
        $config->cacheDirectory      = $this->workDirectory . DIRECTORY_SEPARATOR . 'cache';

        foreach ($overrides as $key => $value) {
            $config->{$key} = $value;
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $overrides
     */
    protected function renderer(array $overrides = []): SmartyRenderer
    {
        return new SmartyRenderer($this->config($overrides), service('locator'));
    }
}
