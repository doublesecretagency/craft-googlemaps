---
description: Like the Twig methods, it's also possible to perform an address lookup via an AJAX request. These endpoints mirror the three standard geocoding methods.
---

# Geocoding via AJAX

If your users need to perform an address lookup without loading a new page, you would of course use AJAX to handle that. The format of the results depends on which endpoint you are using.

## `/all`

Returns `results` as an array of [Address Models](/models/address-model/), or an empty array if nothing is found.

```js
const endpoint = '/actions/google-maps/lookup/all';
```

[_See full details of `all()`_](/models/lookup-model/#all)

## `/one`

Returns `results` as a single [Address Model](/models/address-model/), or `null` if nothing is found.

```js
const endpoint = '/actions/google-maps/lookup/one';
```

[_See full details of `one()`_](/models/lookup-model/#one)

## `/coords`

Returns `results` as a single set of coordinates, or `null` if nothing is found.

```js
const endpoint = '/actions/google-maps/lookup/coords';
```

[_See full details of `coords()`_](/models/lookup-model/#coords)

## AJAX Response

The response of each AJAX call will come in the following format:

```json
{
    success: bool,
    error: string|null,
    results: mixed // depends on which endpoint
}
```

:::tip Formatting Results
The `results` value will be formatted according to which endpoint you are pinging.
:::

The Google Maps plugin is not opinionated, how you perform the AJAX call is entirely up to you. You can get an idea of how the endpoints work by studying the [AJAX geocoding example...](/guides/ajax-geocoding-example/)
