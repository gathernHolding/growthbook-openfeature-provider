# This package provides a GrowthBook feature flag service provider for OpenFeature

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gathern/growthbook-openfeature-provider.svg?style=flat-square)](https://packagist.org/packages/gathern/growthbook-openfeature-provider)
[![Tests](https://img.shields.io/github/actions/workflow/status/gathern/growthbook-openfeature-provider/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gathern/growthbook-openfeature-provider/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gathern/growthbook-openfeature-provider.svg?style=flat-square)](https://packagist.org/packages/gathern/growthbook-openfeature-provider)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require gathern/growthbook-openfeature-provider
```

## Usage

```php
use Cache\Adapter\Apcu\ApcuCachePool;

    $growthbook Growthbook::create();
    //By default, there is no caching enabled. You can enable it by passing any PSR16-compatible instance into the withCache method.
    
    // Any psr-16 library will work
    $cache = new ApcuCachePool();

    $growthbook ->withCache($cache);

    $api = OpenFeatureAPI::getInstance();
    $api->setProvider(new GrowthbookOpenfeatureProvider(
            growthbook: Growthbook,
            clientKey: '<Growthbook_CLIENT_KEY>',
            apiHost: '<Growthbook_API_HOST>',
        ));

    $client = $api->getClient(GrowthbookOpenfeatureProvider::class, 'v1.17');

$client->getStringValue(flagKey: 'enable-success-button', defaultValue: 'wrong');
```
you can follow the usage  of [openfeature-php-package](https://openfeature.dev/docs/reference/technologies/server/php/#usage)  and docs of [growthbook-php-sdk](https://docs.growthbook.io/lib/php) for instance of growthbook

## Testing

```bash
composer test:unit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Credits

- [Mahmoud Shaband](https://github.com/shaband)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
