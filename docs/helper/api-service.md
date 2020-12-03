# API Service

## getServerKey()

:::code
```twig
{# Get server key #}
{% set serverKey = googleMaps.getServerKey() %}
```
```php
// Get server key
$serverKey = GoogleMaps::getServerKey();
```
:::

## getBrowserKey()

:::code
```twig
{# Get browser key #}
{% set browserKey = googleMaps.getBrowserKey() %}
```
```php
// Get browser key
$browserKey = GoogleMaps::getBrowserKey();
```
:::

## setServerKey(key)

:::code
```twig
{# Override server key #}
{% do googleMaps.setServerKey('lorem') %}
```
```php
// Override server key
GoogleMaps::setServerKey('lorem');
```
:::

## setBrowserKey(key)

:::code
```twig
{# Override browser key #}
{% do googleMaps.setBrowserKey('ipsum') %}
```
```php
// Override browser key
GoogleMaps::setBrowserKey('ipsum');
```
:::

## getApiUrl(params = [])

Get the URL used internally for pinging the Google Maps API.

:::code
```twig
{# Get the Google Maps JavaScript API URL #}
{% set apiUrl = googleMaps.getApiUrl() %}
```
```php
// Get the Google Maps JavaScript API URL
$apiUrl = GoogleMaps::getApiUrl();
```
:::

---
---

:::warning Magic Properties
Three of these methods can be used as magic properties in Twig...

```twig
    {# Get API keys #}
    {{ googleMaps.serverKey }}
    {{ googleMaps.browserKey }}

    {# Get API URL #}
    {{ googleMaps.apiUrl }}
```
:::
