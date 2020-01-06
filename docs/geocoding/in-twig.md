# Geocoding in Twig

## `all()`

Returns an array of [Address Models](/models/address-model/), or `null` if nothing is found.

```twig
{% set allMatches = googleMaps.lookup(parameters).all() %}
```

[_See full details of `all()`_](/services/lookup-services/#all)

## `one()`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

```twig
{% set allMatches = googleMaps.lookup(parameters).one() %}
```

[_See full details of `one()`_](/services/lookup-services/#one)

## `coords()`

Returns a single set of coordinates, or `null` if nothing is found.

```twig
{% set allMatches = googleMaps.lookup(parameters).coords() %}
```

[_See full details of `coords()`_](/services/lookup-services/#coords)
