<?php

declare(strict_types=1);

namespace Tests;

use Smarty\CompilerException;
use Smarty\Exception as SmartyException;
use Tests\Support\RendererTestCase;
use Vheissu\CiSmarty\SmartyRenderer;

/**
 * @internal
 */
final class CodeIgniterExtensionTest extends RendererTestCase
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $config
     */
    private function render(string $template, array $data = [], array $config = []): string
    {
        return $this->renderer($config)->setData($data)->renderString($template);
    }

    private function themed(): SmartyRenderer
    {
        return $this->renderer(['theme' => 'dark']);
    }

    public function testBaseAndSiteUrl(): void
    {
        $this->assertSame('http://example.com/uploads/a.jpg', $this->render("{base_url('uploads/a.jpg')}"));
        $this->assertSame('http://example.com/index.php/blog', $this->render("{site_url('blog')}"));
    }

    public function testUrlFunctionsAreEscaped(): void
    {
        $output = $this->render('{base_url($path)}', ['path' => '"><script>']);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('">', $output);
    }

    public function testThemeUrl(): void
    {
        $this->assertSame('http://example.com/themes/dark/fonts/a.woff2', $this->themed()->renderString("{theme_url('fonts/a.woff2')}"));
        $this->assertSame('http://example.com/themes/dark', $this->themed()->renderString('{theme_url()}'));
        $this->assertSame('http://example.com/fonts/a.woff2', $this->render("{theme_url('fonts/a.woff2')}"));
    }

    public function testThemeUrlUsesAssetsPath(): void
    {
        $output = $this->renderer(['theme' => 'dark', 'themeAssetsPath' => '/assets/themes/'])->renderString("{theme_url('x.png')}");

        $this->assertSame('http://example.com/assets/themes/dark/x.png', $output);
    }

    public function testCss(): void
    {
        $this->assertSame(
            '<link rel="stylesheet" href="http://example.com/themes/dark/css/app.css">',
            $this->themed()->renderString('{css file="app.css"}'),
        );
        $this->assertSame(
            '<link rel="stylesheet" href="http://example.com/themes/dark/css/print.css" media="print">',
            $this->themed()->renderString('{css file="print.css" media="print"}'),
        );
    }

    public function testJsWithBooleanAttributes(): void
    {
        $this->assertSame(
            '<script src="http://example.com/themes/dark/js/app.js" defer></script>',
            $this->themed()->renderString('{js file="app.js" defer=true async=false}'),
        );
    }

    public function testImg(): void
    {
        $this->assertSame(
            '<img src="http://example.com/themes/dark/img/logo.png" alt="Logo" class="brand">',
            $this->themed()->renderString('{img file="logo.png" alt="Logo" class="brand"}'),
        );
        $this->assertSame(
            '<img src="http://example.com/themes/dark/img/logo.png" alt="">',
            $this->themed()->renderString('{img file="logo.png"}'),
        );
    }

    public function testAbsoluteAssetUrlsAreUsedAsIs(): void
    {
        $this->assertSame(
            '<script src="https://cdn.example.org/lib.js"></script>',
            $this->render('{js file="https://cdn.example.org/lib.js"}'),
        );
        $this->assertSame(
            '<link rel="stylesheet" href="//cdn.example.org/lib.css">',
            $this->render('{css file="//cdn.example.org/lib.css"}'),
        );
    }

    public function testAssetAttributesAreEscaped(): void
    {
        $output = $this->render('{img file=$file alt=$alt}', [
            'file' => 'x.png" onerror="alert(1)',
            'alt'  => '"><script>alert(1)</script>',
        ]);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('" onerror="', $output);
        $this->assertStringContainsString('alt="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"', $output);
    }

    public function testJavascriptUrlsCannotBeUsedAsAssets(): void
    {
        $output = $this->render('{js file="javascript:alert(1)"}');

        $this->assertStringStartsWith('<script src="http://example.com/js/javascript:', $output);
    }

    public function testExtraAttributesArePassedThrough(): void
    {
        $output = $this->render('{img file="a.png" data-id="1"}');

        $this->assertSame('<img src="http://example.com/img/a.png" alt="" data-id="1">', $output);
    }

    public function testAssetTagsNeedAFile(): void
    {
        $this->expectException(SmartyException::class);
        $this->expectExceptionMessage('{css} needs a file attribute.');

        $this->render('{css}');
    }

    public function testCsrf(): void
    {
        $this->assertSame(csrf_token(), $this->render('{csrf_token()}'));
        $this->assertSame(csrf_hash(), $this->render('{csrf_hash()}'));
        $this->assertSame(csrf_field(), $this->render('{csrf_field}'));
        $this->assertSame(csrf_field('token'), $this->render('{csrf_field id="token"}'));
    }

    public function testRouteTo(): void
    {
        $this->assertSame('/', $this->render("{route_to('Home::index')}"));
    }

    public function testLang(): void
    {
        $this->assertSame('Page Not Found', $this->render("{lang('HTTP.pageNotFound')}"));
    }

    public function testHelpersCanBeTurnedOff(): void
    {
        $this->expectException(CompilerException::class);

        $this->render('{css file="app.css"}', [], ['registerHelpers' => false]);
    }
}
