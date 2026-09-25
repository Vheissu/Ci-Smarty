<?php

declare(strict_types=1);

namespace Tests\Support;

use Smarty\Extension\Base;

final class ShoutExtension extends Base
{
    public function getModifierCallback(string $modifierName)
    {
        return $modifierName === 'shout' ? static fn (string $value): string => strtoupper($value) : null;
    }
}
