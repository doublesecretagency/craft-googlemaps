# Geocoding in PHP

## `all()`

Returns an array of [Address Models](/models/address-model/), or `null` if nothing is found.

```php
$allMatches = GoogleMaps::lookup($parameters)->all();
```

[_See full details of `all()`_](/services/lookup-services/#all)

## `one()`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

```php
$bestMatch = GoogleMaps::lookup($parameters)->one();
```

[_See full details of `one()`_](/services/lookup-services/#one)

## `coords()`

Returns a single set of coordinates, or `null` if nothing is found.

```php
$coords = GoogleMaps::lookup($parameters)->coords();
```

[_See full details of `coords()`_](/services/lookup-services/#coords)
