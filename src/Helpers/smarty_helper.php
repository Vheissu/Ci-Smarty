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

if (! function_exists('smarty')) {
    /**
     * Renders a Smarty template, the Smarty version of view().
     *
     *     return smarty('blog/post', ['post' => $post]);
     *
     * @param array<string, mixed> $data    Template variables
     * @param array<string, mixed> $options See SmartyRenderer::render()
     */
    function smarty(string $name, array $data = [], array $options = []): string
    {
        $saveData = null;

        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        return service('smarty')
            ->setData($data, 'raw')
            ->render($name, $options, $saveData);
    }
}
