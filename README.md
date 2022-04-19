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
        'app_path' => 'Filament',
        'view_path' => 'filament',
        'register' => [
            // your contexts here
        ],
    ],
];
```

## Usage

Publish the package views. The will be published into the `resources/views/vendor/filament` folder. They will override all the default uses of the `Fillament` facade in views but does not change anything else from views of the base package:

```bash
php artisan vendor:publish --tag="filament-multi-context-views"
```

changes
```php
Filament::getNavigationItems()
```

to
```php
Filament::context(request()->context())->getNavigationItems()
```


create a new filament context using

```bash
php artisan make:filament-context Admin"
```

The above command will create a `ContextManager` class named `AdminContext` in the `app/Filament/AdminContext` folder (you may change the base folder where contexts are registered in the config file)

```php
namespace App\Filament\AdminContext;

use Artificertech\FilamentMultiContext\ContextManager;

class AdminContext extends ContextManager
{

}
```

Your context is now created and ready for use. You may configure any context-wide settings in that file (see Configuring a Context)

Now you may generate resources and pages for that context. Lets create your first ContextualPage

# !!! The commands are still a work in progress. For now just create a Page/Resource normally and move it into the appropriate context folder and add the ContextualPage trait to the class


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

Your page is will now be registered in the AdminContext at localhost/admin/dashboard if you navigate to that page you wont see any other Filament navigation items listed in the sidebar.

## Page/Resource context helper

If you are working with static methods for a Filament Page or Resource and need to access the context of that Page you should utilize the `static::getContext()` method. This will return the class name of the context for that Page or Resource.

## The Filament::context() helper

If you are working with contexts in other parts of your filament application and want to know what the current context is using you may use the `Filament::context()` helper method. This will return the slug of the current context. If you pass true `Filament::context(true)` you will get the ContextManager object. This helper returns null if there is no active context or the default FilamentManager object if you passed true as a parameter.

### !!!Context helper availability

This value is set  as part of the route middleware for the Filament Page or Resource Page and is included in the livewire persistent middleware

This ensures that this helper method is available for use at during the inital request to the page and is available on all subsequent livewire requests

You should not depend on this helper in other parts of your application outside of filament

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
