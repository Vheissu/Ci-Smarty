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

use CodeIgniter\Config\BaseService;
use Vheissu\CiSmarty\Config\Smarty as SmartyConfig;
use Vheissu\CiSmarty\SmartyRenderer;

class Services extends BaseService
{
    /**
     * The Smarty renderer: service('smarty')->render('blog/post').
     */
    public static function smarty(?SmartyConfig $config = null, bool $getShared = true): SmartyRenderer
    {
        if ($getShared) {
            return static::getSharedInstance('smarty', $config);
        }

        $config ??= config('Smarty');

        return new SmartyRenderer($config, service('locator'));
    }
}
