# Geocoding Methods

At the heart of each geocoding lookup is the [Lookup Model](/models/lookup-model/). It contains the following methods...

## `all()`

Returns an array of [Address Models](/models/address-model/) sorted by best match, or `null` if nothing is found.

:::code
```twig
{% set allMatches = googleMaps.lookup(parameters).all() %}
```
```php
$allMatches = GoogleMaps::lookup($parameters)->all();
```
:::

## `one()`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

:::code
```twig
{% set bestMatch = googleMaps.lookup(parameters).one() %}
```
```php
$bestMatch = GoogleMaps::lookup($parameters)->one();
````
:::

## `coords()`

Returns a single set of [coordinates](/models/coordinates/), or `null` if nothing is found.

:::code
```twig
{% set coords = googleMaps.lookup(parameters).coords() %}
```
```php
$coords = GoogleMaps::lookup($parameters)->coords();
````
:::
