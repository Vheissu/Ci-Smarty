<?php

declare(strict_types=1);

namespace Tests;

use CodeIgniter\View\Exceptions\ViewException;
use InvalidArgumentException;
use Smarty\Exception as SmartyException;
use Tests\Support\RendererTestCase;

/**
 * @internal
 */
final class SmartyRendererTest extends RendererTestCase
{
    public function testRendersTemplateWithData(): void
    {
        $output = $this->renderer()->setData(['name' => 'World'])->render('hello');

        $this->assertSame("Hello World!\n", $output);
    }

    public function testAcceptsExplicitExtension(): void
    {
        $renderer = $this->renderer()->setVar('name', 'World');

        $this->assertSame("Hello World!\n", $renderer->render('hello.tpl'));
        $this->assertSame("Plain World\n", $renderer->render('page.html'));
    }

    public function testUsesConfiguredExtension(): void
    {
        $output = $this->renderer(['extension' => 'html'])->setVar('name', 'World')->render('page');

        $this->assertSame("Plain World\n", $output);
    }

    public function testEscapesVariablesByDefault(): void
    {
        $output = $this->renderer()->setVar('name', '<script>alert(1)</script>')->render('hello');

        $this->assertSame("Hello &lt;script&gt;alert(1)&lt;/script&gt;!\n", $output);
    }

    public function testNofilterPrintsTrustedMarkup(): void
    {
        $output = $this->renderer()->setVar('html', '<b>bold</b>')->render('raw');

        $this->assertSame("<b>bold</b>\n", $output);
    }

    public function testEscapingCanBeTurnedOff(): void
    {
        $output = $this->renderer(['escapeHtml' => false])->setVar('name', '<b>')->render('hello');

        $this->assertSame("Hello <b>!\n", $output);
    }

    public function testSetDataEscapesForContext(): void
    {
        $renderer = $this->renderer(['escapeHtml' => false]);

        $renderer->setData(['name' => '<b>'], 'html');
        $this->assertSame("Hello &lt;b&gt;!\n", $renderer->render('hello'));

        $renderer->setVar('name', '<i>', 'html');
        $this->assertSame("Hello &lt;i&gt;!\n", $renderer->render('hello'));
    }

    public function testSetDataAcceptsIntegerKeys(): void
    {
        $renderer = $this->renderer()->setData(['first', 'second']);

        $this->assertSame(['0' => 'first', '1' => 'second'], $renderer->getData());
    }

    public function testSavedDataIsKeptBetweenRenders(): void
    {
        $renderer = $this->renderer();
        $renderer->setVar('name', 'World')->render('hello');

        $this->assertSame(['name' => 'World'], $renderer->getData());
        $this->assertSame("Hello World!\n", $renderer->render('hello'));
    }

    public function testUnsavedDataIsDroppedAfterRender(): void
    {
        $renderer = $this->renderer(['muteUndefinedOrNullWarnings' => true]);

        $renderer->setVar('name', 'Saved')->render('hello', null, true);
        $this->assertSame("Hello Once!\n", $renderer->setVar('name', 'Once')->render('hello', null, false));

        $this->assertSame(['name' => 'Saved'], $renderer->getData());
    }

    public function testSaveDataCanBeTurnedOffInConfig(): void
    {
        $renderer = $this->renderer(['saveData' => false]);
        $renderer->setVar('name', 'World')->render('hello');

        $this->assertSame([], $renderer->getData());
    }

    public function testResetDataClearsEverything(): void
    {
        $renderer = $this->renderer();
        $renderer->setVar('name', 'World')->render('hello');
        $renderer->setVar('other', 'value');

        $renderer->resetData();

        $this->assertSame([], $renderer->getData());
    }

    public function testVariablesDoNotLeakIntoTheSmartyInstance(): void
    {
        $renderer = $this->renderer(['saveData' => false]);
        $renderer->setVar('name', 'World')->render('hello');

        $this->assertNull($renderer->getSmarty()->getTemplateVars('name'));
    }

    public function testRendersStrings(): void
    {
        $output = $this->renderer()->setVar('name', '<World>')->renderString('Hi {$name}');

        $this->assertSame('Hi &lt;World&gt;', $output);
    }

    public function testTemplateInheritanceAndIncludes(): void
    {
        $output = $this->renderer()->setVar('title', 'Child')->render('child');

        $this->assertSame("<title>Child</title>\n<main>footer from app\n</main>\n", $output);
    }

    public function testMissingTemplateThrowsViewException(): void
    {
        $this->expectException(ViewException::class);

        $this->renderer()->render('does/not/exist');
    }

    public function testThemeTemplatesOverrideAppTemplates(): void
    {
        $renderer = $this->renderer(['theme' => 'dark'])->setVar('name', 'World');

        $this->assertSame('dark', $renderer->getTheme());
        $this->assertSame("Dark hello World!\n", $renderer->render('hello'));
    }

    public function testThemeFallsBackToAppTemplates(): void
    {
        $output = $this->renderer(['theme' => 'dark'])->setVar('title', 'Child')->render('child');

        // layout.tpl comes from the theme, child.tpl and the footer from the app.
        $this->assertSame("<title>Child</title>\n<main class=\"dark\">footer from app\n</main>\n", $output);
    }

    public function testThemeCanBeSwitchedAndCleared(): void
    {
        $renderer = $this->renderer()->setVar('name', 'World');

        $renderer->setTheme('dark');
        $this->assertSame("Dark hello World!\n", $renderer->render('hello'));

        $renderer->setTheme(null);
        $this->assertSame("Hello World!\n", $renderer->render('hello'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidThemeNames(): iterable
    {
        yield 'parent directory' => ['..'];
        yield 'traversal' => ['../../etc'];
        yield 'slash' => ['dark/evil'];
        yield 'backslash' => ['dark\\evil'];
        yield 'empty' => [''];
        yield 'null byte' => ["dark\0"];
        yield 'whitespace' => [' dark '];
    }

    /**
     * @dataProvider provideInvalidThemeNames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidThemeNames')]
    public function testRejectsUnsafeThemeNames(string $theme): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->renderer()->setTheme($theme);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideTraversingViewNames(): iterable
    {
        yield 'empty' => [''];
        yield 'relative' => ['../outside/secret'];
        yield 'nested' => ['partials/../../outside/secret'];
        yield 'windows' => ['partials\\..\\..\\outside\\secret'];
        yield 'namespaced' => ['Tests\\Support\\Module\\Views\\..\\..\\outside\\secret'];
    }

    /**
     * @dataProvider provideTraversingViewNames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideTraversingViewNames')]
    public function testRejectsViewNamesThatLeaveTemplateDirectories(string $view): void
    {
        $this->expectException(ViewException::class);

        $this->renderer(['securityPolicy' => null])->render($view);
    }

    public function testRendersNamespacedViews(): void
    {
        $output = $this->renderer()->setVar('name', 'Box')->render('Tests\Support\Module\Views\widget');

        $this->assertSame("<div>Widget Box</div>\n", $output);
    }

    public function testMissingNamespacedViewThrowsViewException(): void
    {
        $this->expectException(ViewException::class);

        $this->renderer()->render('Tests\Support\Module\Views\missing');
    }

    public function testSecurityPolicyBlocksFilesOutsideTemplateDirectories(): void
    {
        $renderer = $this->renderer()->setVar('path', SUPPORTPATH . 'outside/secret.tpl');

        $this->expectException(SmartyException::class);
        $this->expectExceptionMessageMatches('/not trusted|not allowed/');

        $renderer->render('include_outside');
    }

    public function testSecurityPolicyCanBeTurnedOff(): void
    {
        $renderer = $this->renderer(['securityPolicy' => null])->setVar('path', SUPPORTPATH . 'outside/secret.tpl');

        $this->assertSame("top secret\n", $renderer->render('include_outside'));
    }

    public function testOutputIsNotCachedByDefault(): void
    {
        $renderer = $this->renderer();

        $this->assertSame("Hello First!\n", $renderer->setVar('name', 'First')->render('hello'));
        $this->assertSame("Hello Second!\n", $renderer->setVar('name', 'Second')->render('hello'));
    }

    public function testCacheOptionCachesPerCacheName(): void
    {
        $renderer = $this->renderer();

        $first = $renderer->setVar('name', 'First')->render('hello', ['cache' => 60, 'cache_name' => 'user|1']);
        $stale = $renderer->setVar('name', 'Changed')->render('hello', ['cache' => 60, 'cache_name' => 'user|1']);
        $other = $renderer->setVar('name', 'Other')->render('hello', ['cache' => 60, 'cache_name' => 'user|2']);

        $this->assertSame("Hello First!\n", $first);
        $this->assertSame("Hello First!\n", $stale);
        $this->assertSame("Hello Other!\n", $other);
    }

    public function testRegistersConfiguredExtensions(): void
    {
        $output = $this->renderer(['extensions' => [Support\ShoutExtension::class]])->renderString('{"hi"|shout}');

        $this->assertSame('HI', $output);
    }

    public function testExplicitResourcesArePassedThrough(): void
    {
        $output = $this->renderer()->setVar('name', 'World')->render('string:Hey {$name}');

        $this->assertSame('Hey World', $output);
    }
}
