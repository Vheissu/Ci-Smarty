<?php

declare(strict_types=1);

/**
 * This file is part of CI Smarty.
 *
 * (c) Dwayne Charrington and GitHub contributors
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Vheissu\CiSmarty\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * CI Smarty configuration.
 *
 * To change any of these values, create app/Config/Smarty.php with a
 * class Config\Smarty that extends this one and override the properties
 * you need.
 */
class Smarty extends BaseConfig
{
    /**
     * Extension added to template names that do not have one.
     */
    public string $extension = 'tpl';

    /**
     * Directories searched for templates, in order. Themes are
     * searched before these.
     *
     * @var list<string>
     */
    public array $templateDirectories = [APPPATH . 'Views'];

    /**
     * Where compiled templates are written. Must be writable.
     */
    public string $compileDirectory = WRITEPATH . 'smarty' . DIRECTORY_SEPARATOR . 'compiled';

    /**
     * Where cached output is written when caching is used. Must be writable.
     */
    public string $cacheDirectory = WRITEPATH . 'smarty' . DIRECTORY_SEPARATOR . 'cache';

    /**
     * Directories that {config_load} reads from.
     *
     * @var list<string>
     */
    public array $configDirectories = [];

    /**
     * Smarty extensions to register. Each entry is a class name that
     * implements Smarty\Extension\ExtensionInterface.
     *
     * @var list<class-string<\Smarty\Extension\ExtensionInterface>>
     */
    public array $extensions = [];

    /**
     * Register the built-in CodeIgniter template functions
     * (base_url, site_url, theme_url, css, js, img, csrf_field...).
     */
    public bool $registerHelpers = true;

    /**
     * HTML-escape every {$variable} by default. Use {$var nofilter} to
     * print trusted markup. Leave this on unless you have a good reason.
     */
    public bool $escapeHtml = true;

    /**
     * Security policy applied to templates. Set a class name that
     * extends Smarty\Security to use your own policy, or null to turn
     * the policy off.
     *
     * With the default policy templates can only include files from the
     * configured template, theme and config directories.
     *
     * @var class-string<\Smarty\Security>|null
     */
    public ?string $securityPolicy = \Smarty\Security::class;

    /**
     * Cache rendered output for every template. Off by default: a cached
     * page is served for all visitors until it expires, whatever data you
     * pass in. Prefer caching per render with the 'cache' and
     * 'cache_name' options instead.
     */
    public bool $caching = false;

    /**
     * Default cache lifetime in seconds.
     */
    public int $cacheLifetime = 3600;

    /**
     * Recompile templates when their source changes. You can turn this
     * off in production for a small speed-up, but then you must clear
     * the compile directory on every deploy.
     */
    public bool $compileCheck = true;

    /**
     * Recompile templates on every request. Only useful while debugging.
     */
    public bool $forceCompile = false;

    /**
     * Show the Smarty debug console.
     */
    public bool $debugging = false;

    /**
     * Error reporting level used while templates run. Null uses the
     * current PHP level.
     */
    public ?int $errorReporting = null;

    /**
     * Treat undefined variables and array keys as null instead of
     * raising warnings.
     */
    public bool $muteUndefinedOrNullWarnings = false;

    /**
     * Keep data between render() calls. Matches Config\View::$saveData.
     */
    public bool $saveData = true;

    /**
     * Name of the active theme, or null to not use themes.
     */
    public ?string $theme = null;

    /**
     * Directory containing theme template folders. A theme called
     * "default" keeps its templates in {themeDirectory}/default.
     * Keep this outside the public web root.
     */
    public string $themeDirectory = ROOTPATH . 'themes';

    /**
     * Public path, relative to base_url(), that holds theme assets.
     * Assets for the "default" theme are served from
     * {base_url}/{themeAssetsPath}/default/{css,js,img}/.
     */
    public string $themeAssetsPath = 'themes';
}
