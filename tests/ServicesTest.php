<?php

declare(strict_types=1);

namespace Tests;

use CodeIgniter\Config\Factories;
use Tests\Support\RendererTestCase;
use Vheissu\CiSmarty\Config\Smarty as SmartyConfig;
use Vheissu\CiSmarty\SmartyRenderer;

/**
 * @internal
 */
final class ServicesTest extends RendererTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Factories::injectMock('config', 'Smarty', $this->config());
        $this->resetServices();
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        $this->resetServices();

        parent::tearDown();
    }

    public function testConfigIsDiscovered(): void
    {
        Factories::reset('config');

        $this->assertInstanceOf(SmartyConfig::class, config('Smarty'));
    }

    public function testServiceIsDiscoveredAndShared(): void
    {
        $renderer = service('smarty');

        $this->assertInstanceOf(SmartyRenderer::class, $renderer);
        $this->assertSame($renderer, service('smarty'));
        $this->assertNotSame($renderer, single_service('smarty'));
    }

    public function testHelperRendersTemplates(): void
    {
        helper('smarty');

        $this->assertSame("Hello World!\n", smarty('hello', ['name' => 'World']));
    }

    public function testHelperHonoursSaveDataOption(): void
    {
        helper('smarty');

        smarty('hello', ['name' => 'World'], ['saveData' => false]);

        $this->assertSame([], service('smarty')->getData());
    }

    public function testHelperDoesNotEscapeDataTwice(): void
    {
        helper('smarty');

        $this->assertSame("Hello &lt;b&gt;!\n", smarty('hello', ['name' => '<b>']));
    }
}
