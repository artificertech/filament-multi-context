# A package for adding multiple contexts to the filament admin panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/artificertech/filament-multi-context.svg?style=flat-square)](https://packagist.org/packages/artificertech/filament-multi-context)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/artificertech/filament-multi-context/run-tests?label=tests)](https://github.com/artificertech/filament-multi-context/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/artificertech/filament-multi-context/Check%20&%20fix%20styling?label=code%20style)](https://github.com/artificertech/filament-multi-context/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/artificertech/filament-multi-context.svg?style=flat-square)](https://packagist.org/packages/artificertech/filament-multi-context)

This package allows you to register multiple filament contexts in your application with their own set of resources and pages

## Installation

You can install the package via composer:

```bash
composer require artificertech/filament-multi-context
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-multi-context-config"
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Contexts
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register contexts from. You may also register contexts here.
    |
    */

    'contexts' => [
        'namespace' => 'App\\Filament',
        'path' => app_path('Filament'),
        'register' => [
            // your contexts here
        ],
    ],
];

```

## Usage

create a new filament context using

```bash
php artisan make:filament-context AdminContext
```

The above command will create a `ContextManager` class named `AdminContext` in the `app/Filament/AdminContext` folder (you may change the base folder where contexts are registered in the config file)

```php
namespace App\Filament\AdminContext;

use Artificertech\FilamentMultiContext\ContextManager;

class AdminContext extends ContextManager
{

}
```

Your context is now created and ready for use. You may configure any context-wide settings in that file (see [Configuring a Context](#configuring-a-context)). A directory for your contextual Pages, Resources, and Widgets that has the same name as your ContextManager class will also be created.

<!-- Now you may generate resources and pages for that context. Lets create your first ContextualPage

## !!! The overrides of the default commands are still a work in progress. For now just create a Page/Resource normally and move it into the appropriate context folder and add the ContextualPage trait to the class


```bash
php artisan make:filament-page --context=Admin Dashboard"
```

This command will create a `Dashboard` class in the `app/Filament/AdminContext/Pages` folder

```php
namespace App\Filament\AdminContext\Pages;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use ContextualPage;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin-context.pages.dashboard';
}
```

Your page is will now be registered in the AdminContext at localhost/admin/dashboard if you navigate to that page you wont see any other Filament navigation items listed in the sidebar. -->

## Adding Pages, Resources, and Widgets to your context

You may generate pages, resources, and widgets the same way you do with normal filament. Then move the resources into the context folder that was generated along with your ContextManager class. 

Note that this folder must match the class name of your ContextManager class.

Once you have made your page and moved it into the {Context}/Pages folder make sure to change the namespace and add the `Artificertech\FilamentMultiContext\Concerns\ContextualPage` trait (`Artificertech\FilamentMultiContext\Concerns\ContextualResource` for resources) and example page is provided:

```php
namespace App\Filament\AdminContext\Pages;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use ContextualPage;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin-context.pages.dashboard';
}
```

### Widgets

Widgets that are registered in a context will be available when calling `Filament::getWidgets()`. This means that if you create a page in your context that extends the `Filament\Pages\Dashboard` class it will have a set of widgets available specific to the context.

### --c|context Flag for make:filament- commands

(coming soon)


## Page/Resource context helper

If you are working with static methods for a Filament Page or Resource and need to access the context of that Page you should utilize the `static::getContext()` method. This will return the class name of the context for that Page or Resource.

## The Filament::context() helper

If you are working with contexts in other parts of your filament application and want to know what the current context is using you may use the `Filament::context()` helper method. This will return the slug of the current context. If you pass true `Filament::context(true)` you will get the ContextManager object. This helper returns null if there is no active context or the default FilamentManager object if you passed true as a parameter.

### !!!Context helper availability

This value is set  as part of the route middleware for the Filament Page or Resource Page and is included in the livewire persistent middleware

This ensures that this helper method is available for use at during the inital request to the page and is available on all subsequent livewire requests

You should not depend on this helper in other parts of your application outside of filament

## Configuring a Context

A `ContextManager` class allows you to configure your context just like you would inside the `filament.php` config file except you override static properties and methods in the class instead of in a config file in your application.

Every option listed defaults to what is in the `filament.php` config file unless set. The only exceptions are the registration of pages, resources, and widgets and context-specific options such as the slug and route prefix.

Here is an example of a context manager that has been fully configured

```php
namespace App\Filament\AdminContext;

use Artificertech\FilamentMultiContext\ContextManager;

class AdminContext extends ContextManager
{
    // override filament config
    protected static ?string $domain = 'admin.example.com';

    protected static ?array $baseMiddleware = [
        MyMiddleware::class
    ];

    protected static ?array $authMiddleware = [
        MyAuthMiddlware::class
    ];

    protected static ?string $path = 'admin';

    protected static ?string $auth = 'custom-guard';

    protected static ?string $defaultAvatarProvider = MyAvatarProvider::class;


    // Additional Options

    // the slug used in route names and in the Filament::context() helper (defaults to the class name in kebab case)
    protected static ?string $slug = 'admin-context';

    // The route prefix for all routes in this context (defaults to the slug without -context at the end)
    protected static ?string $prefix = null;

    // add context-specifc middlware without overriding the default filament configuration
    protected static string | array $middlewares = [];


    // Manually Register items
    protected array $pages = [];

    protected array $resources = [];

    protected array $widgets = [];
}
```

### Filament Facade and FilamentServing event

Calls to the Filament facade are passed to the appropriate ContextManager object after the ApplyContext middlware is applied. If you wish to have configurations such as `Filament::registerTheme()` apply to all contexts make sure to call them within `Filament::serving(function() { })` callback. This event is called after the ApplyContext middlware has already run.

If you want to apply changes to only specific contexts you can call `Filament::getContext(AdminContext ::class)->registerTheme()`

If you dont pass a parameter to getContext() it will return the default FilamentManager object

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Cole Shirley](https://github.com/cole.shirley)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
