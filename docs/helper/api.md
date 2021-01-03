# Google Maps API

## getApiUrl(params = [])

Get the URL used internally for pinging the Google Maps JavaScript API.

:::code
```twig
{% set apiUrl = googleMaps.getApiUrl() %}  {# As a normal method #}
{% set apiUrl = googleMaps.apiUrl %}       {# As a magic property #}
```
```php
$apiUrl = GoogleMaps::getApiUrl();
// No magic property equivalent
```
:::

## getBrowserKey()

Get the browser key.

:::code
```twig
{% set browserKey = googleMaps.getBrowserKey() %}  {# As a normal method #}
{% set browserKey = googleMaps.browserKey %}       {# As a magic property #}
```
```php
$browserKey = GoogleMaps::getBrowserKey();
// No magic property equivalent
```
:::

## getServerKey()

Get the server key.

:::code
```twig
{% set serverKey = googleMaps.getServerKey() %}  {# As a normal method #}
{% set serverKey = googleMaps.serverKey %}       {# As a magic property #}
```
```php
$serverKey = GoogleMaps::getServerKey();
// No magic property equivalent
```
:::

## setBrowserKey(key)

Override the browser key.

:::code
```twig
{% do googleMaps.setBrowserKey('lorem') %}
```
```php
GoogleMaps::setBrowserKey('lorem');
```
:::

## setServerKey(key)

Override the server key.

:::code
```twig
{% do googleMaps.setServerKey('ipsum') %}
```
```php
GoogleMaps::setServerKey('ipsum');
```
:::
