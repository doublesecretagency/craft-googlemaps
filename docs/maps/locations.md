# Locations

There are several methods of the [Dynamic Map Model](/models/dynamic-map-model/) and [Static Map Model](/models/static-map-model/) which have a `locations` parameter. Additionally, the [JavaScript model](/models/javascript/) also contains several methods which call for `locations`.

What are "locations"? Glad you asked.

## Coordinates

The `locations` value can be a set of [coordinates](/models/coordinates/), or an array of coordinate sets.

:::code
```js JSON
{
    "lat": 32.3113966,
    "lng": -64.7527469
}
```
:::

:::warning Coordinates Only for JavaScript
If you are working in JavaScript, then your only option is to work with coordinates. It is physically impossible to translate Address Models and Elements into JavaScript.
:::

---
---

## Address Models

The `locations` value can be set as an individual [Address Model](/models/address-model/), or as an array of Address Models.

:::code
```twig
{# Just one Address Model #}
{% set locations = entry.myAddressField %}

{# An array of Address Models #}
{% set locations = [
    entry.homeAddress,
    entry.businessAddress
] %}
```
```php
// Just one Address Model
$locations = $entry->myAddressField;

// An array of Address Models
$locations = [
    $entry->homeAddress,
    $entry->businessAddress
];
```
:::

---
---

## Elements

The `locations` value can be set as an individual [Element](https://craftcms.com/docs/3.x/elements.html), or as an array of Elements. This can include any native Element Types (ie: Entries, Categories, Users, etc), as well as any custom Element Types introduced by other plugins or modules.

:::code
```twig
{# Just one Element #}
{% set locations = entry %}

{# An array of Elements #}
{% set locations = craft.entries.all() %}
```
```php
// Just one Element
$locations = $entry;

// An array of Elements
$locations = Entry::find()->all();
```
:::

:::tip Elements must have a valid Address field
When using an Element, ensure that it has at least one Address field attached to it. The plugin will automatically render all valid addresses on the map.
:::