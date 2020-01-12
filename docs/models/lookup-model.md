# Lookup Model

When a Lookup Model is initiated, it is passed a collection of parameters which specify the lookup details. This set of parameters is stored internally when the object is initially created.

```php
$lookup = GoogleMaps::lookup($parameters);
```

Once you apply the `->all()` (or `->one()`, or `->coords()`) method, those parameters are used to ping the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/intro#geocoding). The lookup results will be cached for 30 days.

```php
$results = $lookup->all();
```

## Public Methods

### `all()`

Perform lookup and return complete results. Addresses will be sorted by best match.

#### Returns

`Address[]` - An array of [Address Models](), or `null` if nothing was found.

**EXAMPLES:** [Twig](/geocoding/in-twig/#all) | [PHP](/geocoding/in-php/#all) | [AJAX](/geocoding/via-ajax/#all)

### `one()`

Perform lookup and return only the first matching result. Generally speaking, the first address is typically the best match.

#### Returns

`Address` - A single [Address Model](), or `null` if nothing was found.

**EXAMPLES:** [Twig](/geocoding/in-twig/#one) | [PHP](/geocoding/in-php/#one) | [AJAX](/geocoding/via-ajax/#one)

### `coords()`

Perform lookup and return only the coordinates of the first matching address.

#### Returns

`coords` - A [coords](/models/coordinates/) object, or `null` if nothing was found.

**EXAMPLES:** [Twig](/geocoding/in-twig/#coords) | [PHP](/geocoding/in-php/#coords) | [AJAX](/geocoding/via-ajax/#coords)
