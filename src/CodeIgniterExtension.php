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

use Closure;
use Smarty\Exception as SmartyException;
use Smarty\Extension\Base;
use Smarty\FunctionHandler\Base as FunctionHandler;
use Smarty\FunctionHandler\FunctionHandlerInterface;
use Smarty\Template;

/**
 * CodeIgniter functions for templates.
 *
 * Tags, which print HTML:
 *   {css file="app.css" media="print"}
 *   {js file="app.js" defer=true}
 *   {img file="logo.png" alt="Logo"}
 *   {csrf_field}
 *
 * Functions, usable anywhere in an expression and escaped like any other value:
 *   {base_url('uploads/a.jpg')}  {site_url('blog')}  {theme_url('fonts/x.woff2')}
 *   {route_to('Blog::show', $id)}  {lang('App.welcome')}
 *   {csrf_token()}  {csrf_hash()}
 */
class CodeIgniterExtension extends Base
{
    public function __construct(protected SmartyRenderer $renderer)
    {
        helper('url');
    }

    public function getFunctionHandler(string $functionName): ?FunctionHandlerInterface
    {
        return match ($functionName) {
            'css'        => $this->handler(fn (array $params): string => $this->css($params)),
            'js'         => $this->handler(fn (array $params): string => $this->js($params)),
            'img'        => $this->handler(fn (array $params): string => $this->img($params)),
            'csrf_field' => $this->handler(static fn (array $params): string => csrf_field($params['id'] ?? null), false),
            default      => null,
        };
    }

    public function getModifierCallback(string $modifierName)
    {
        return match ($modifierName) {
            'base_url'   => static fn ($path = ''): string => base_url($path),
            'site_url'   => static fn ($path = ''): string => site_url($path),
            'theme_url'  => fn (string $path = ''): string => $this->renderer->themeUrl($path),
            'route_to'   => static fn (string $route, ...$params): string => (string) route_to($route, ...$params),
            'lang'       => static fn (string $line, array $args = [], ?string $locale = null): array|string => lang($line, $args, $locale),
            'csrf_token' => static fn (): string => csrf_token(),
            'csrf_hash'  => static fn (): string => csrf_hash(),
            default      => null,
        };
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function css(array $params): string
    {
        $href = $this->assetUrl('css', $this->requireFile('css', $params));
        unset($params['file']);

        return '<link' . $this->attributes(['rel' => $params['rel'] ?? 'stylesheet', 'href' => $href] + $params) . '>';
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function js(array $params): string
    {
        $src = $this->assetUrl('js', $this->requireFile('js', $params));
        unset($params['file']);

        return '<script' . $this->attributes(['src' => $src] + $params) . '></script>';
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function img(array $params): string
    {
        $src = $this->assetUrl('img', $this->requireFile('img', $params));
        unset($params['file']);

        return '<img' . $this->attributes(['src' => $src, 'alt' => $params['alt'] ?? ''] + $params) . '>';
    }

    /**
     * Absolute URLs are used as they are. Anything else points into the
     * theme's css, js or img folder.
     */
    protected function assetUrl(string $folder, string $file): string
    {
        if (preg_match('#\A(https?:)?//#i', $file) === 1) {
            return $file;
        }

        return $this->renderer->themeUrl($folder . '/' . ltrim($file, '/'));
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function requireFile(string $tag, array $params): string
    {
        $file = $params['file'] ?? '';

        if (! is_string($file) || $file === '') {
            throw new SmartyException("{{$tag}} needs a file attribute.");
        }

        return $file;
    }

    /**
     * Builds an escaped attribute string. true prints a bare attribute
     * (defer, async), false and null leave it out.
     *
     * @param array<string, mixed> $attributes
     */
    protected function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            $name = (string) $name;

            if ($value === false || $value === null || preg_match('/\A[a-zA-Z_:][a-zA-Z0-9_:.-]*\z/', $name) !== 1) {
                continue;
            }

            $html .= $value === true
                ? ' ' . $name
                : ' ' . $name . '="' . htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
        }

        return $html;
    }

    /**
     * @param bool $cacheable False keeps the tag live inside cached output.
     */
    protected function handler(Closure $callback, bool $cacheable = true): FunctionHandlerInterface
    {
        return new class ($callback, $cacheable) extends FunctionHandler {
            public function __construct(private readonly Closure $callback, bool $cacheable)
            {
                $this->cacheable = $cacheable;
            }

            /**
             * @param array<string, mixed> $params
             */
            public function handle($params, Template $template): string
            {
                return ($this->callback)($params);
            }
        };
    }
}
