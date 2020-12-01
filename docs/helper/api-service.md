# API Service

getApiUrl(array $params = []): string
getServerKey(): string
getBrowserKey(): string
setServerKey(string $key): string
setBrowserKey(string $key): string


---
---



## Manually Load JS Files

You can manually load the necessary JavaScript files, if they are not being loaded automatically...

:::code
```twig
{# Manually load required JS files #}
{% do googleMaps.loadAssets() %}
```
```php
// Manually load required JS files
GoogleMaps::loadAssets();
```
:::


## Override Google API keys

If you need to override the Google API keys via Twig, you can...

:::code
```twig
{# Override server key #}
{% do googleMaps.setServerKey('lorem') %}

{# Override browser key #}
{% do googleMaps.setBrowserKey('ipsum') %}
```
```php
// Override server key
GoogleMaps::setServerKey('lorem');

// Override browser key
GoogleMaps::setBrowserKey('ipsum');
```
:::

There are parallel methods for retrieving the API keys...

:::code
```twig
{# Get server key #}
{% set serverKey = googleMaps.getServerKey() %}

{# Get browser key #}
{% set browserKey = googleMaps.getBrowserKey() %}
```
```php
// Get server key
$serverKey = GoogleMaps::getServerKey();

// Get browser key
$browserKey = GoogleMaps::getBrowserKey();
```
:::

And since Twig is very forgiving about using magic methods, you can abbreviate those even further...

:::code
```twig
{{ googleMaps.serverKey }}
{{ googleMaps.browserKey }}
```
```php
GoogleMaps::serverKey;
GoogleMaps::browserKey;
```
:::
