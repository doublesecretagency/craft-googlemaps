---
description:
---

# Address Model

The properties and methods of an Address Model are identical whether you are accessing them via Twig or PHP.

:::code
```twig
{% set address = entry.myAddressField %}
```
```php
$address = $entry->myAddressField;
```
:::

:::warning Additional Properties and Methods
The Address Model is an extension of the [Location Model](/models/location-model/). It contains all properties and methods of the Location Model, plus the properties and methods shown below.

You can access `lat` and `lng` just as easily as `street1` and `street2`.
:::

---
---

## Public Properties

### `id`

_int_ - ID of the Address.

### `elementId`

_int_ - Element ID of the element containing the Address.

### `fieldId`

_int_ - Field ID of the Address field.

### `formatted`

_string_ - A nicely-formatted single-line interpretation of the address, provided by Google during the initial geocoding.

### `raw`

_array_ - The original data used to create this Address Model. Contains the full response from the original Google API call.

### `name`

_string_ - The location's official name. Commonly used for landmarks and business names.

### `street1`

_string_ - The first line of the street address. Usually contains the street name & number of the location.

### `street2`

_string_ - The second line of the street address. Usually contains the apartment, unit, or suite number.

### `city`

_string_ - The city. (aka: town)

### `state`

_string_ - The state. (aka: province)

### `zip`

_string_ - The zip code. (aka: postal code)

### `neighborhood`

_string_ - The neighborhood.

### `county`

_string_ - The local county. (aka: district)

### `country`

_string_ - The country. (aka: nation)

:::warning Similar sounding, but very different
We recognize that `county` and `country` are extremely similar words, and apologize on behalf of the English language.
:::

### `countryCode`

_string_ - The country code.

### `placeId`

_string_ - The official `place_id` as specified by the [Google API](https://developers.google.com/maps/documentation/places/web-service/place-id).

### `distance`

_float_ - Alias for `getDistance()`.

### `zoom`

_int_ - Zoom level of the map as shown in the control panel.

---
---

## Public Methods

### `getElement()`

Get the corresponding **element** which contains this Address. It's possible that no element ID exists (for example, if the Address Model was created manually, instead of using data from the database).

---
---

### `getField()`

Get the corresponding **field** which contains this Address. It's possible that no field ID exists (for example, if the Address Model was created manually, instead of using data from the database).

---
---

### `getDistance(location = null, units = 'miles')`

Calculate the distance between this Address, and a second location. Behaves just as described in the [Location Model](/models/location-model/#getdistance-location-units-miles), pass in a separate location to measure the distance between them.

If the Address was retrieved as part of a [proximity search](/proximity-search/), you can optionally omit all parameters. Skipping both parameters will make the `getDistance()` method behave as an alias for the dynamically-calculated `distance` property.

When conducting a proximity search, the `distance` property is the distance between this Address, and your proximity search target. It will be returned in whatever unit of measurement (miles or kilometers) was specified in the [proximity search options](/proximity-search/options/).

:::tip One Way or the Other
If the Address was **not** returned in a set of proximity search results, you must functionally declare a second `location`. Otherwise, there will be no distance for the method to measure.
:::

---
---

### `linkToMap(parameters = {})`

Identical to [parent method](/models/location-model/#linktomap-parameters), except more data can be extracted from an Address Model.

---
---

### `linkToDirections(parameters = {}, origin = null)`

Identical to [parent method](/models/location-model/#linktodirections-parameters-origin-null), except more data can be extracted from an Address Model.

---
---

### `isEmpty()`

_bool_ - Returns whether _all_ the non-coordinate address fields are empty, or whether they contain any data at all. Specifically looks to see if data exists in any of the following subfields:

 - `street1`
 - `street2`
 - `city`
 - `state`
 - `zip`
 - `country`

:::code
```twig
{% if not address.isEmpty %}
    {{ address.multiline() }}
{% endif %}
```
```php
if (!$address->isEmpty) {
    return $address->multiline();
}
```
:::

---
---

### `multiline(maxLines = 3)`

- `maxLines` - Maximum number of lines (1-4) allocated to display the address.

:::code
```twig 1
{{ address.multiline(1) }}

   123 Main St, Suite #101, Springfield, CO 81073
```
```twig 2
{{ address.multiline(2) }}

   123 Main St, Suite #101
   Springfield, CO 81073
```
```twig 3
{{ address.multiline(3) }}

   123 Main St
   Suite #101
   Springfield, CO 81073
```
```twig 4
{{ address.multiline(4) }}

   123 Main St
   Suite #101
   Springfield, CO 81073
   United States
```
:::

### `1`

All information will be condensed into a single line. Very similar to `formatted`, although the `country` value will be omitted here. Other minor formatting differences are also possible, since the formatting is being handled by different sources.

### `2`

The `street1` and `street2` will appear on the first line, everything else (except `country`) will appear on the second line.

### `3`

If a `street2` value exists, it will be given its own line. Otherwise, that line will be skipped.

### `4`

Exactly like `3`, with only the addition of the `country` value.

## Multiline vs. Formatted

When using the `multiline` method, the various subfield components will be explicitly compiled as described in the examples above.

When using the `formatted` property, you will get a pre-formatted string which was originally set by the Google API.


:::code
```twig Multiline
{{ address.multiline(1) }}

{# 123 Main St, Suite #101, Springfield, CO 81073 #}
```
```twig Formatted
{{ address.formatted }}

{# 123 Main St, Suite #101, Springfield, CO 81073, USA #}
```
:::

The differences between the two are subtle, but they do exist. We can't know how Google compiles each `formatted` value, but we have a very specific formula to follow when using `multiline`.

Please be mindful of these differences when deciding which to use. When in doubt, you can always just output the model directly as a string.

## Output as a String

```twig
{{ address }}
```

When you output the model directly, it attempts to render the entire address on a single line. This triggers the internal `__toString` method, which then does one of the following things...

1. If the `formatted` value exists, that will be returned.
2. Otherwise, it will generate a single line address by using the `multiline(1)` method.

:::warning Google Formatted Preferred 
The `formatted` value will be preferred, because it was supplied by Google as a pre-compiled string. However, the end result of `multiline(1)` should (theoretically) be similar enough for a reasonable fallback. 
:::
