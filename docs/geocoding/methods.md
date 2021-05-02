---
description: Select which format you would like to receive the geocoding results in. Get `all` matches, or just `one` match, or just the first set of `coords`. 
---

# Geocoding Methods

At the heart of each geocoding lookup is the [Lookup Model](/models/lookup-model/). It contains the following methods...

## `all()`

Returns an array of [Address Models](/models/address-model/) sorted by best match, or `null` if nothing is found.

:::code
```twig
{% set results = googleMaps.lookup(target).all() %}
```
```php
$results = GoogleMaps::lookup($target)->all();
```
:::

## `one()`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

:::code
```twig
{% set address = googleMaps.lookup(target).one() %}
```
```php
$address = GoogleMaps::lookup($target)->one();
````
:::

## `coords()`

Returns a single set of [coordinates](/models/coordinates/), or `null` if nothing is found.

:::code
```twig
{% set coords = googleMaps.lookup(target).coords() %}
```
```php
$coords = GoogleMaps::lookup($target)->coords();
````
:::
