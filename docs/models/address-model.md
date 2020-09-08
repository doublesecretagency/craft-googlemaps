# Address Model

The properties and methods of an Address Model are identical whether you are accessing them via Twig or PHP.

::: code
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

_int_ - ID of the address.

### `elementId`

_int_ - ID of the element containing the address.

### `fieldId`

_int_ - ID of the field containing the address.

### `formatted`

_string_ - A nicely-formatted single-line interpretation of the address, provided by Google during the initial geocoding.

### `raw`

_array_ - The original data used to create this Address Model. Contains the full response from the original Google API call.

### `street1`

_string_ - The first line of the street address. Usually contains the street name & number of the location.

### `street2`

_string_ - The second line of the street address. Usually contains the apartment, unit, or suite number.

### `city`

_string_ - The city of the location.

### `state`

_string_ - The state or province of the location.

### `zip`

_string_ - The zip or postal code of the location.

### `country`

_string_ - The country of the location.

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

### `getDistance(coords = false, units = 'miles')`

The distance between this address, and your proximity search target. Will be returned in whatever unit of measurement (miles or kilometers) was specified in the proximity search parameters.

:::warning Proximity Search Only
This value will only be available if the address was returned as part of a [proximity search](/proximity-search/). Otherwise, the value for `distance` will be null.
:::

Behaves just as described in the [Location Model](/models/location-model/#getdistance-coords-units-mi), you can optionally pass in coordinates to measure the distance between two points. However, it is slightly more powerful in the context of an Address Model.

If the address has been returned as part of a proximity search, the method will use the coordinates of your search target by default.

---
---

### `isEmpty()`

_bool_ - Returns whether _all_ of the non-coordinate address fields are empty, or whether they contain any data at all. Specifically looks to see if data exists in any of the following subfields:

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

### `multiline(maxLines)`

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

<hr>

:::warning Two ways to display a single line
If you need to output an address on a single line, `multiline` may not be the best choice. Consider just **outputting the model directly** instead.
:::

## Outputting the model directly

When you output the model directly, it will render the entire address on a single line. The internal `__toString` method attempts to use the `formatted` value if it exists. Otherwise, it will generate a single line address by using the `multiline` method.

:::code
```twig By outputting directly
{{ entry.address }}

{# 123 Main St, Suite #101, Springfield, CO 81073, USA #}
```
```twig By using multiline
{{ entry.address.multiline(1) }}

{# 123 Main St, Suite #101, Springfield, CO 81073 #}
```
:::

If the model contains a Google-supplied `formatted` address, that value will be used as the single-line interpretation of the Address model.

Otherwise, `multiline(1)` will be used to generate a single-line version of the Address.

:::warning Multiline vs. Formatted
When using the `multiline` method, the various subfield components are explicitly compiled as described above.

When using the `formatted` property, you will get a complete one-line string that has been pre-formatted by the Google API. 
:::
