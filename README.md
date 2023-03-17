# Algolia Plugin for Sylius

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]

Use Algolia search and recommendations in your Sylius store.

## Installation

```shell
composer require setono/sylius-algolia-plugin
```

### Import configuration

```yaml
# config/packages/setono_sylius_algolia.yaml
imports:
    - { resource: "@SetonoSyliusAlgoliaPlugin/Resources/config/app/config.yaml" }

setono_sylius_algolia:
    indexable_resources:
        sylius.product:
            document: 'Setono\SyliusAlgoliaPlugin\Document\Product'
    app_id: '%env(ALGOLIA_APP_ID)%'
    search_only_api_key: '%env(ALGOLIA_SEARCH_ONLY_API_KEY)%'
    admin_api_key: '%env(ALGOLIA_ADMIN_API_KEY)%'
```

In your `.env.local` add your parameters: 

```dotenv
###> setono/sylius-algolia-plugin ###
ALGOLIA_APP_ID=YOUR_APPLICATION_ID
ALGOLIA_ADMIN_API_KEY=YOUR_ADMIN_API_KEY
ALGOLIA_SEARCH_ONLY_API_KEY=YOUR_SEARCH_ONLY_KEY
###< setono/sylius-algolia-plugin ###
```

### Import routing

```yaml
# config/routes/setono_sylius_algolia.yaml
setono_sylius_algolia:
    resource: "@SetonoSyliusAlgoliaPlugin/Resources/config/routes.yaml"
```

or if your app doesn't use locales:

```yaml
# config/routes/setono_sylius_algolia.yaml
setono_sylius_algolia:
    resource: "@SetonoSyliusAlgoliaPlugin/Resources/config/routes_no_locale.yaml"
```

### Move plugin class in your `bundles.php`

Move the plugin at the top of your bundles list, else you might have an exception like `You have requested a non-existent parameter "setono_sylius_algolia.cache.adapter"`

```php
<?php
$bundles = [
    Setono\SyliusAlgoliaPlugin\SetonoSyliusAlgoliaPlugin::class => ['all' => true],
    // ...
];
```

### Implement the `IndexableInterface` in your configured indexable resources

You have to implement the `Setono\SyliusAlgoliaPlugin\Model\IndexableInterface` in the indexable resources you
configured in `setono_sylius_algolia.indexable_resources`. In a typical Sylius application for the `Product` entity
it could look like this:

```php
<?php
declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusAlgoliaPlugin\Model\IndexableAwareTrait;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product")
 */
class Product extends BaseProduct implements IndexableInterface
{
    use IndexableAwareTrait;
}
```

### Implement the `IndexableResourceRepositoryInterface` in applicable repositories

The configured indexable resources' associated repositories has to implement the `Setono\SyliusAlgoliaPlugin\Repository\IndexableResourceRepositoryInterface`.
If you're configuring the `sylius.product` there is a trait available you can use: `Setono\SyliusAlgoliaPlugin\Repository\ProductRepositoryTrait`.

## Usage

TODO

[ico-version]: https://poser.pugx.org/setono/sylius-algolia-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-algolia-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusAlgoliaPlugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/SyliusAlgoliaPlugin/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/setono/sylius-algolia-plugin
[link-github-actions]: https://github.com/Setono/SyliusAlgoliaPlugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/SyliusAlgoliaPlugin
