# LightAdminBundle

A Symfony bundle for Light Admin features.

## Installation

```bash
composer require tervis/light-admin-bundle
```

## Register the bundle

If using Symfony Flex, the bundle is registered automatically. Otherwise, add to config/bundles.php:

```php
return [
    // ...
    LightAdminBundle\LightAdminBundle::class => ['all' => true],
];
```

