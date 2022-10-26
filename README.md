# A package for adding multiple contexts to the filament admin panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/artificertech/filament-multi-context.svg?style=flat-square)](https://packagist.org/packages/artificertech/filament-multi-context)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/artificertech/filament-multi-context/run-tests?label=tests)](https://github.com/artificertech/filament-multi-context/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/artificertech/filament-multi-context/Check%20&%20fix%20styling?label=code%20style)](https://github.com/artificertech/filament-multi-context/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/artificertech/filament-multi-context.svg?style=flat-square)](https://packagist.org/packages/artificertech/filament-multi-context)

This package allows you to register multiple filament contexts in your
application with their own set of resources and pages

## Installation

You can install the package via composer:

```bash
composer require artificertech/filament-multi-context
```

## Usage

create a new filament context using

```bash
php artisan make:filament-context FilamentTeams
```

The above command will create the following files and directories:

```
app/FilamentTeams/Pages/
app/FilamentTeams/Resources/
app/FilamentTeams/Widgets/
app/Providers/FilamentTeamsServiceProvider.php
config/filament-teams.php
```

`Filament` cannot be passed as a context to this command as it is reserved for
the default filament installation

> **_Register Provider:_** Be sure to add the `FilamentTeamsServiceProvider`
> class to your providers array in `config/app.php`

You may now add filament resources in your FilamentTeams directories.

> **_Context Traits:_** be sure to add the ContextualPage and ContextualResource
> traits to their associated classes inside of your context directories. (I
> tried really hard with v2 to make this unnecessary but sadly here we are).
> Without this when filament generates navigation links it will try to use
> `filament.pages.*` and `filament.resources.{resource}.*` instead of
> `{context}.pages.*` and `{context}.resources.{resource}.*` as the route names

### ContextualPage & ContextualResource traits

Resources:

```php
namespace App\FilamentTeams\Resources;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use ContextualPage;
}
```

Resources:

```php
namespace App\FilamentTeams\Resources;

use Artificertech\FilamentMultiContext\Concerns\ContextualResource;
use Filament\Resources\Resource;

class UserResource extends Resource
{
    use ContextualResource;
}
```

## Configuration

The `config/filament-teams.php` file contains a subset of the
`config/filament.php` configuration file. The values in the `filament-teams.php`
file can be adjusted and will only affect the pages, resources, and widgets for
the `filament-teams` context.

Currently the configuration values that can be modified for a specific context
are:

```
'path'
'domain'
'pages'
'resources'
'widgets'
'livewire'
'middleware'
```

### ContextServiceProvider

Your `ContextServiceProvider` found in your
`app/Providers/FilamentTeamsServiceProvider.php` is an extension of the Filament
`PluginServiceProvder` so features of the `PluginServiceProvider` may be used
for your context

### Custom Page and Resource Routing

If you would like more control over the way pages and resources are routed you
may override the `componentRoutes()` function in your
`FilamentTeamsServiceProvider`

```php
protected function componentRoutes(): callable
    {
        return function () {
            Route::name('pages.')->group(function (): void {
                foreach (Facades\Filament::getPages() as $page) {
                    Route::group([], $page::getRoutes());
                }
            });

            Route::name('resources.')->group(function (): void {
                foreach (Facades\Filament::getResources() as $resource) {
                    Route::group([], $resource::getRoutes());
                }
            });
        };
    }
```

### Changing the context guard

By default all contexts will use the guard defined in the primary `filament.php`
config file. However if you need to specify the guard for a specific context you
may add the following lines to your context config file:

```php
/*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | This is the configuration that Filament will use to handle authentication
    | into the admin panel.
    |
    */

    'auth' => [
        'guard' => 'my-custom-guard',
    ],
```

## !!! The Filament Facade

In order for this package to work the `filament` app service has been overriden.
Each context is represented by its own `Filament\FilamentManager` object. Within
your application calls to the filament facade (such as `Filament::serving`) will
be proxied to the appropriate `Filament\FilamentManager` object based on the
current context of your application (which is determined by the route of the
request)

### Context Functions

The following functions have been added to facilitate multiple
`Filament\FilamentManger` objects in your application:

```php
// retrieve the string name of the current application context
// defaults to `filament`

Filament::currentContext(): string
```

```php
// retrieve the Filament\FilamentManager object for the current app context

Filament::getContext()
```

```php
// retrieve the array of Filament\FilamentManager objects keyed by the context name

Filament::getContexts()
```

```php
// set the current app context.
// Passing null or nothing sets the context to 'filament'

Filament::setContext(string|null $context)
```

```php
// sets the context for the duration of the callback function, then resets it back to the original value
Filament::forContext(string $context, function () {
    // ...
})
```

```php
// loops through each registered context (including the default 'filament' context),
// sets that context as the current context,
// runs the callback, then resets to the original value
Filament::forAllContexts(function () {
    // ...
})
```

```php
// creates a new FilamentManager object and registers it under the $name context
// this method is used by your ContextServiceProvider to register your context
// you shouldn't need to use this method during normal development
Filament::addContext(string $name)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed
recently.

## Contributing

Please see
[CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for
details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report
security vulnerabilities.

## Credits

- [Cole Shirley](https://github.com/cole.shirley)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more
information.
