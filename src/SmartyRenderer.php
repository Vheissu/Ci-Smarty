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

namespace Vheissu\CiSmarty;

use CodeIgniter\Autoloader\FileLocatorInterface;
use CodeIgniter\View\Exceptions\ViewException;
use CodeIgniter\View\RendererInterface;
use InvalidArgumentException;
use Smarty\Smarty;
use Vheissu\CiSmarty\Config\Smarty as SmartyConfig;

/**
 * Renders Smarty templates through CodeIgniter's renderer interface.
 */
class SmartyRenderer implements RendererInterface
{
    protected Smarty $smarty;

    /**
     * Data kept between renders.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Data for the next render only. Null when nothing is pending.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $tempData = null;

    protected ?string $theme = null;

    /**
     * Views directories of namespaces that templates were loaded from.
     *
     * @var list<string>
     */
    protected array $namespaceDirectories = [];

    public function __construct(
        protected SmartyConfig $config,
        protected ?FileLocatorInterface $locator = null,
    ) {
        $this->smarty = $this->createEngine();

        $this->setTheme($config->theme);
    }

    /**
     * The underlying Smarty instance, for registering plugins, clearing
     * caches and anything else this class does not wrap.
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }

    /**
     * Renders a template file and returns the output.
     *
     * $view is a path relative to the template directories, such as
     * "blog/post" or "blog/post.tpl", or a namespaced view such as
     * "Acme\Blog\Views\post".
     *
     * Options:
     *  - cache:      cache the output for this many seconds
     *  - cache_name: cache id, so different data gets different cache entries
     *  - compile_id: compile id, for keeping separate compiled versions
     *
     * @param array<string, mixed>|null $options
     * @param bool|null                 $saveData Keep the data for later renders.
     *                                            Null uses Config\Smarty::$saveData.
     */
    public function render(string $view, ?array $options = null, ?bool $saveData = null): string
    {
        return $this->fetch($this->resolveTemplate($view), $options ?? [], $saveData);
    }

    /**
     * Renders a template held in a string.
     *
     * Never pass user input as the template: it would let that user
     * run template code on your server. Pass user input as data instead.
     *
     * @param array<string, mixed>|null $options
     */
    public function renderString(string $view, ?array $options = null, ?bool $saveData = null): string
    {
        return $this->fetch('string:' . $view, $options ?? [], $saveData);
    }

    /**
     * Sets several template variables at once.
     *
     * @param array<string, mixed> $data
     * @param string|null          $context Escape the values for this context
     *                                      ('html', 'js', 'css', 'url', 'attr'
     *                                      or 'raw') before assigning them.
     */
    public function setData(array $data = [], ?string $context = null): static
    {
        if ($context !== null) {
            $data = esc($data, $context);
        }

        $this->tempData ??= $this->data;
        $this->tempData = array_merge($this->tempData, $data);

        return $this;
    }

    /**
     * Sets a single template variable.
     *
     * @param mixed       $value
     * @param string|null $context See setData().
     */
    public function setVar(string $name, $value = null, ?string $context = null): static
    {
        if ($context !== null) {
            $value = esc($value, $context);
        }

        $this->tempData ??= $this->data;
        $this->tempData[$name] = $value;

        return $this;
    }

    /**
     * Removes all template variables.
     */
    public function resetData(): static
    {
        $this->data     = [];
        $this->tempData = null;

        return $this;
    }

    /**
     * The variables the next render will receive.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->tempData ?? $this->data;
    }

    /**
     * Switches theme. Templates in the theme directory are used before
     * the ones in the normal template directories. Pass null to stop
     * using themes.
     *
     * @throws InvalidArgumentException When the name is not a plain folder name.
     */
    public function setTheme(?string $theme): static
    {
        if ($theme !== null) {
            if (preg_match('/\A[A-Za-z0-9_-]+\z/', $theme) !== 1) {
                throw new InvalidArgumentException(
                    'Theme names may only contain letters, numbers, dashes and underscores.',
                );
            }
        }

        $this->theme = $theme;

        $this->updateTemplateDirectories();

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Public URL for a file inside the active theme's asset folder.
     * Without a theme the path is relative to base_url().
     */
    public function themeUrl(string $path = ''): string
    {
        $segments = [];

        if ($this->theme !== null) {
            $segments[] = trim($this->config->themeAssetsPath, '/');
            $segments[] = $this->theme;
        }

        $segments[] = ltrim($path, '/');

        helper('url');

        return base_url(implode('/', array_filter($segments, static fn (string $segment): bool => $segment !== '')));
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function fetch(string $resource, array $options, ?bool $saveData): string
    {
        $saveData ??= $this->config->saveData;

        // Smarty keeps template objects keyed by name and compile id, not
        // by template directory, so the theme has to be part of the
        // compile id or switching themes would keep the old templates.
        $compileId = implode('|', array_filter(
            [$this->theme, isset($options['compile_id']) ? (string) $options['compile_id'] : null],
            static fn (?string $part): bool => $part !== null && $part !== '',
        ));

        $template = $this->smarty->createTemplate(
            $resource,
            isset($options['cache_name']) ? (string) $options['cache_name'] : null,
            $compileId === '' ? null : $compileId,
        );

        if (isset($options['cache'])) {
            $template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            $template->setCacheLifetime((int) $options['cache']);
        }

        $data = $this->tempData ?? $this->data;

        if ($saveData) {
            $this->data = $data;
        }

        $this->tempData = null;

        $template->assign($data);

        return $template->fetch();
    }

    /**
     * Turns a view name into something Smarty can load.
     *
     * @throws ViewException When the template cannot be found.
     */
    protected function resolveTemplate(string $view): string
    {
        // Explicit Smarty resources such as "file:", "string:" or
        // "extends:" are passed through untouched. Two or more letters
        // so Windows drive letters are not mistaken for one.
        if (preg_match('/\A[a-z][a-z0-9_]+:/i', $view) === 1) {
            return $view;
        }

        if (pathinfo($view, PATHINFO_EXTENSION) === '') {
            $view .= '.' . ltrim($this->config->extension, '.');
        }

        if (str_contains($view, '\\')) {
            return $this->resolveNamespacedTemplate($view);
        }

        if (! $this->smarty->templateExists($view)) {
            throw ViewException::forInvalidFile($view);
        }

        return $view;
    }

    /**
     * Finds a namespaced view like "Acme\Blog\Views\post.tpl".
     *
     * The namespace's Views directory is added as a template directory,
     * after the others, so the template can extend and include its
     * neighbours and passes the security policy.
     */
    protected function resolveNamespacedTemplate(string $view): string
    {
        $locator = $this->locator ?? service('locator');
        $file    = $locator->locateFile($view, 'Views', pathinfo($view, PATHINFO_EXTENSION));

        if ($file === false) {
            throw ViewException::forInvalidFile($view);
        }

        $file   = (string) realpath($file);
        $marker = DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
        $offset = strrpos($file, $marker);

        if ($offset !== false) {
            $directory = substr($file, 0, $offset + strlen($marker));

            if (! in_array($directory, $this->namespaceDirectories, true)) {
                $this->namespaceDirectories[] = $directory;
                $this->smarty->addTemplateDir($directory);
            }
        }

        return 'file:' . $file;
    }

    protected function updateTemplateDirectories(): void
    {
        $directories = [];

        if ($this->theme !== null) {
            $directories[] = rtrim($this->config->themeDirectory, '\\/') . DIRECTORY_SEPARATOR . $this->theme;
        }

        $this->smarty->setTemplateDir(array_merge(
            $directories,
            $this->config->templateDirectories,
            $this->namespaceDirectories,
        ));
    }

    protected function createEngine(): Smarty
    {
        $config = $this->config;
        $smarty = new Smarty();

        $smarty->setCompileDir($config->compileDirectory);
        $smarty->setCacheDir($config->cacheDirectory);

        if ($config->configDirectories !== []) {
            $smarty->setConfigDir($config->configDirectories);
        }

        $smarty->setEscapeHtml($config->escapeHtml);
        $smarty->setCaching($config->caching ? Smarty::CACHING_LIFETIME_CURRENT : Smarty::CACHING_OFF);
        $smarty->setCacheLifetime($config->cacheLifetime);
        $smarty->setCompileCheck($config->compileCheck ? Smarty::COMPILECHECK_ON : Smarty::COMPILECHECK_OFF);
        $smarty->setForceCompile($config->forceCompile);
        $smarty->setDebugging($config->debugging);

        if ($config->errorReporting !== null) {
            $smarty->setErrorReporting($config->errorReporting);
        }

        if ($config->muteUndefinedOrNullWarnings) {
            $smarty->muteUndefinedOrNullWarnings();
        }

        if ($config->registerHelpers) {
            $smarty->addExtension(new CodeIgniterExtension($this));
        }

        foreach ($config->extensions as $extension) {
            $smarty->addExtension(new $extension());
        }

        if ($config->securityPolicy !== null) {
            $smarty->enableSecurity($config->securityPolicy);
        }

        return $smarty;
    }
}
