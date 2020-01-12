# Geocoding in PHP

## `all()`

Returns an array of [Address Models](/models/address-model/), or `null` if nothing is found.

```php
$allMatches = GoogleMaps::lookup($parameters)->all();
```

[_See full details of `all()`_](/models/lookup-model/#all)

## `one()`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

```php
$bestMatch = GoogleMaps::lookup($parameters)->one();
```

[_See full details of `one()`_](/models/lookup-model/#one)

## `coords()`

Returns a single set of coordinates, or `null` if nothing is found.

```php
$coords = GoogleMaps::lookup($parameters)->coords();
```

[_See full details of `coords()`_](/models/lookup-model/#coords)
