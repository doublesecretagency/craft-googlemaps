# API Service

## getBrowserKey()

:::code
```twig
{# Get browser key #}
{% set browserKey = googleMaps.getBrowserKey() %}
{% set browserKey = googleMaps.browserKey %} {# Shorter syntax #}
```
```php
// Get browser key
$browserKey = GoogleMaps::getBrowserKey();
```
:::

## getServerKey()

:::code
```twig
{# Get server key #}
{% set serverKey = googleMaps.getServerKey() %}
{% set serverKey = googleMaps.serverKey %} {# Shorter syntax #}
```
```php
// Get server key
$serverKey = GoogleMaps::getServerKey();
```
:::

## setBrowserKey(key)

:::code
```twig
{# Override browser key #}
{% do googleMaps.setBrowserKey('lorem') %}
```
```php
// Override browser key
GoogleMaps::setBrowserKey('lorem');
```
:::

## setServerKey(key)

:::code
```twig
{# Override server key #}
{% do googleMaps.setServerKey('ipsum') %}
```
```php
// Override server key
GoogleMaps::setServerKey('ipsum');
```
:::

## getApiUrl(params = [])

Get the URL used internally for pinging the Google Maps API.

:::code
```twig
{# Get the Google Maps JavaScript API URL #}
{% set apiUrl = googleMaps.getApiUrl() %}
{% set apiUrl = googleMaps.apiUrl %} {# Shorter syntax #}
```
```php
// Get the Google Maps JavaScript API URL
$apiUrl = GoogleMaps::getApiUrl();
```
:::
