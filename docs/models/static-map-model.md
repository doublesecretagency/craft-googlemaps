# Static Map Model

The properties and methods of the Visitor Model are identical whether you are accessing them via Twig or PHP.

In all examples, `map` will be a Map Model.

## Public Methods

### `map(locations = [], options = [])`

:::code
```twig
{% set map = googleMaps.map(locations) %}
```
```php
$map = GoogleMaps::map($locations);
```
:::

#### Arguments

 - `$parameters` (_array_) - An optional configuration array.

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) containing the approximate location of a visitor.



```php

GoogleMaps::map(locations, options).markers(locations, options)

```


---
---

### `markers($locations, $options = [])`

Append markers to an existing map object.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type                 | Default | Description |
|----------------------|:--------------------:|:-------:|-------------|
| `size` | _string_ | _null_ | `tiny`, `mid`, or `small` |
| `scale` | _int_ | 1 | Multiplied with size to determine output size. (`1`,`2`,`4`) |
| `color` | _string_ | _null_ | A hex code or [predefined color](https://developers.google.com/maps/documentation/maps-static/start#MarkerStyles). |
| `label` | _string_ | _null_ | A single uppercase alphanumeric character from the set `{A-Z, 0-9}`. |
| `icon` | _string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `anchor` | `'bottom'` | _null_ | Set as `x,y` coordinates or a [predefined alignment](https://developers.google.com/maps/documentation/maps-static/start#CustomIcons). |
| `visible` | _string_ | _null_ | blah |
| `markerOptions` | _object_             | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `fields`        | _string_ or _array_  | _null_  | Which field(s) of the element(s) should be included on the map. (_null_ will include all Address fields) |

#### Returns

 - _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.markers(locations) %}
```
```php
$map->markers($locations);
```
:::
