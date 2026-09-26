# CI Smarty

Use [Smarty 5](https://smarty-php.github.io/smarty/) templates in CodeIgniter 4 applications.

[![Tests](https://github.com/Vheissu/Ci-Smarty/actions/workflows/tests.yml/badge.svg)](https://github.com/Vheissu/Ci-Smarty/actions/workflows/tests.yml)

## Requirements

- PHP 8.1 or newer
- CodeIgniter 4.5 or newer
- Smarty 5.8.4 or newer (installed for you by Composer)

Looking for the CodeIgniter 3 version? It is tagged `v3.0.0` and is no longer updated.

## Installation

```sh
composer require vheissu/ci-smarty
```

CodeIgniter finds the package's config, service and helper on its own, as long as Composer package discovery is on (it is by default in `app/Config/Modules.php`).

Smarty writes compiled templates to `writable/smarty/`, so that folder needs to be writable by your web server. It's created on first use.

## Rendering templates

Put templates in `app/Views` with a `.tpl` extension and render them with the `smarty()` helper, which works like CodeIgniter's `view()`:

```php
namespace App\Controllers;

class Blog extends BaseController
{
    public function show(int $id): string
    {
        helper('smarty');

        return smarty('blog/show', [
            'post' => model('PostModel')->find($id),
        ]);
    }
}
```

```smarty
{* app/Views/blog/show.tpl *}
{extends file="layout.tpl"}

{block name=title}{$post.title}{/block}

{block name=body}
    <h1>{$post.title}</h1>
    {$post.body_html nofilter}
{/block}
```

Add `'smarty'` to `$helpers` in `BaseController` if you'd rather not call `helper('smarty')` every time.

The renderer is also available as a service. It implements CodeIgniter's `RendererInterface`, so `setData()`, `setVar()`, `render()` and `renderString()` behave the way they do on the built-in view renderer:

```php
$smarty = service('smarty');

$smarty->setVar('user', $user)
       ->setData(['title' => 'Account']);

return $smarty->render('account/index');
```

`getSmarty()` returns the underlying `Smarty\Smarty` object when you need something this package doesn't wrap, such as registering your own plugins or clearing caches.

### Escaping

Every `{$variable}` is HTML-escaped. To print markup you trust, use `nofilter`:

```smarty
{$comment}               {* escaped *}
{$trusted_html nofilter} {* printed as-is *}
```

You can switch this off with `$escapeHtml = false`, but then every template has to escape its own output, and one missed `|escape` is an XSS hole.

### Namespaced views

Views inside modules or packages are loaded by namespace, the same way CodeIgniter loads them:

```php
return smarty('Acme\Blog\Views\post', $data);
```

A namespaced template can `{extends}` and `{include}` other templates from its own `Views` folder.

## Configuration

Create `app/Config/Smarty.php` and override whatever you need:

```php
<?php

namespace Config;

use Vheissu\CiSmarty\Config\Smarty as BaseSmarty;

class Smarty extends BaseSmarty
{
    public ?string $theme = 'default';
    public bool $compileCheck = ENVIRONMENT !== 'production';
}
```

| Option | Default | What it does |
|---|---|---|
| `extension` | `'tpl'` | Added to template names that don't have an extension. |
| `templateDirectories` | `[APPPATH . 'Views']` | Where templates are looked up, in order. |
| `compileDirectory` | `WRITEPATH . 'smarty/compiled'` | Compiled templates. Must be writable. |
| `cacheDirectory` | `WRITEPATH . 'smarty/cache'` | Cached output. Must be writable. |
| `configDirectories` | `[]` | Folders `{config_load}` reads from. |
| `extensions` | `[]` | Class names of Smarty extensions to register. |
| `registerHelpers` | `true` | Register the template functions listed below. |
| `escapeHtml` | `true` | Escape every `{$variable}`. |
| `securityPolicy` | `Smarty\Security::class` | Security policy class, or `null` for none. |
| `caching` | `false` | Cache the output of every template. See [Caching](#caching). |
| `cacheLifetime` | `3600` | Default cache lifetime in seconds. |
| `compileCheck` | `true` | Recompile a template when its source changes. |
| `forceCompile` | `false` | Recompile on every request. For debugging only. |
| `debugging` | `false` | Show the Smarty debug console. Never enable this in production. |
| `errorReporting` | `null` | Error level while templates run. `null` keeps PHP's. |
| `muteUndefinedOrNullWarnings` | `false` | Treat undefined variables as null instead of warning. |
| `saveData` | `true` | Keep variables between renders, like `Config\View::$saveData`. |
| `theme` | `null` | Active theme. See [Themes](#themes). |
| `themeDirectory` | `ROOTPATH . 'themes'` | Where theme templates live. |
| `themeAssetsPath` | `'themes'` | Public path, under `base_url()`, for theme assets. |

## Themes

A theme is a folder of templates that override the ones in `app/Views`. If a theme doesn't have a template, the one in `app/Views` is used, so a theme only needs the files it changes.

Theme templates and theme assets are kept apart so templates are never served straight from the web root:

```
themes/
    dark/
        layout.tpl          overrides app/Views/layout.tpl
public/
    themes/
        dark/
            css/app.css
            js/app.js
            img/logo.png
```

Set the theme in `app/Config/Smarty.php`, or switch it per request:

```php
service('smarty')->setTheme('dark');
service('smarty')->setTheme(null); // back to app/Views only
```

Theme names may only contain letters, numbers, dashes and underscores. Anything else throws an `InvalidArgumentException`, so it is safe to set a theme from a user preference.

## Template functions

These tags print HTML. File names are relative to the active theme's `css`, `js` or `img` folder. Full `https://` or `//` URLs are used unchanged. Any other attribute you pass is added to the tag and escaped. `true` prints a bare attribute, and `false` leaves the attribute out.

```smarty
{css file="app.css"}
{css file="print.css" media="print"}
{js file="app.js" defer=true}
{img file="logo.png" alt="Acme" class="brand"}
{csrf_field}
```

These functions return values, so you can use them anywhere a variable can go. Their output is escaped like any other value:

```smarty
<a href="{site_url('blog')}">Blog</a>
<a href="{route_to('Blog::show', $post.id)}">{$post.title}</a>
<img src="{base_url('uploads/')}{$photo.file}" alt="">
<link rel="preload" href="{theme_url('fonts/body.woff2')}" as="font">
<p>{lang('App.welcome')}</p>
<meta name="{csrf_token()}" content="{csrf_hash()}">
```

Set `$registerHelpers = false` if you don't want any of them.

## Caching

Output caching is off unless you ask for it. When a template is cached, Smarty returns the cached output without running the template, so every visitor gets the same page. Always give each variation of a page its own `cache_name`:

```php
return smarty('blog/show', ['post' => $post], [
    'cache'      => 300,              // seconds
    'cache_name' => 'post|' . $post['id'],
]);
```

`{csrf_field}` is never cached. Wrap other per-visitor output in `{nocache}...{/nocache}`.

To clear caches, use the Smarty object: `service('smarty')->getSmarty()->clearAllCache()`.

## Security

- Smarty's security policy is on by default. Templates can only load files from the template, theme and config directories. For a stricter policy (limiting tags, modifiers or static class access), extend `Smarty\Security` and set `$securityPolicy` to your class.
- **Never pass user input to `renderString()` as the template.** A template can run code on your server. Pass user input as data, where it is escaped.
- `renderString()` stores a compiled copy of each distinct string in the compile directory. Don't render an endless stream of unique strings.
- View names containing `..` are rejected.
- If you turn off `compileCheck` in production, empty `writable/smarty/compiled` on every deploy, or old templates will keep being served.

## Upgrading from the CodeIgniter 3 version

The 4.x release is a rewrite for CodeIgniter 4 and Smarty 5. It's installed with Composer, and Smarty is no longer bundled in the repository.

- `$this->parser->parse('view', $data)` becomes `smarty('view', $data)` or `service('smarty')->render('view')`. The third argument (`$return`) is gone, because CodeIgniter 4 controllers return their output.
- `parse_string()` and `string_parse()` become `renderString()`.
- Variables are now escaped by default. Add `nofilter` where a template printed HTML on purpose.
- Caching used to be on by default, with a one-hour lifetime. That served one visitor's page to everyone else for an hour. It's now off. See [Caching](#caching) if you want it back.
- `{$this}` is no longer assigned. Pass templates the data they need.
- The CodeIgniter 3 helpers `userdata()`, `flashdata()` and `uriseg()` are gone. Pass those values in as data.
- `{css('app.css')}` becomes `{css file="app.css"}`, and the same goes for `js` and `img`.
- Themes no longer load a `functions.php`. Register plugins through `$extensions` or `getSmarty()` instead.
- Theme templates move out of `public/` into `themes/<name>/` at the project root, and there is no `views/` subfolder. Assets stay in `public/themes/<name>/`.
- The `MY_Parser`, `MY_Output` and `CI_Smarty` classes are gone.
- Smarty 5 itself changed a lot compared with Smarty 3. Read its [upgrade notes](https://smarty-php.github.io/smarty/stable/upgrading/), particularly the part about calling PHP functions from templates, which now requires registering them.

## Development

```sh
composer install
composer test      # PHPUnit
composer analyze   # PHPStan
```

## License

MIT. See [LICENSE](LICENSE).
