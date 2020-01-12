# Lookup Model

When a Lookup Model is initiated, it will be passed a collection of parameters which specify the lookup details.

```php
->lookup($parameters)->all();
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
