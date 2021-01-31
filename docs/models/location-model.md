# Location Model

Both the [Address Model](/models/address-model/) and [Visitor Model](/models/visitor-model/) are extensions of the Location Model.

## Public Properties

### `lat`

_float_ - Latitude of location.

### `lng`

_float_ - Longitude of location.

### `coords`

_object_ - Alias for `getCoords()`.

## Public Methods

### `hasCoords()`

Determine whether the location has valid coordinates.

```twig
{% if location.hasCoords() %}
    Latitude:  {{ location.lat }}
    Longitude: {{ location.lng }}
{% endif %}
```

**Returns**

- _bool_ - Whether the location has functional coordinates.

### `getCoords()`

Get the coordinates of a location.

**Returns**

- _object_ - Get the location coordinates as a [coords](/models/coordinates/) JSON object.

### `getDistance(location, units = 'miles')`

Get the distance between two points.

```twig
{% set distance = entry.homeAddress.getDistance(entry.businessAddress) %}
```

**Arguments**

- `$location` (_mixed_) - A [set of coordinates](/models/coordinates/), or a separate Location Model (can be an [Address](/models/address-model/) or [Visitor](/models/visitor-model/) model).
- `$units` (_string_) - Unit of measurement (`mi`,`km`,`miles`,`kilometers`).

**Returns**

- _float_|_null_ - Calculates the distance between the two points.

### `linkToMap(parameters = {})`

Generate a link to a Google Map, displaying a single marker of this location.

```twig
<a href="{{ location.linkToMap() }}">See Location in Google Maps</a>
```

**Arguments**

- `$parameters` (_array_) - Appended to the generated Google URL. [See full parameter details.](https://developers.google.com/maps/documentation/urls/get-started#parameters)

**Returns**

- _string_ - Generate a link to see this location in Google Maps.

### `linkToDirections(parameters = {}, origin = null)`

Generate a link to a Google Map, displaying directions to this location.

```twig
<a href="{{ location.linkToDirections() }}">Get Directions</a>
```

**Arguments**

- `$parameters` (_array_) - Appended to the generated Google URL. [See full parameter details.](https://developers.google.com/maps/documentation/urls/get-started#directions-action)
- `$origin` (_mixed_) - Must be another **Location Model**. Both [Address Models](/models/address-model/) and [Visitor Models](/models/visitor-model/) are extensions of the Location Model, and therefore considered a valid `origin`.

**Returns**

- _string_ - Generate a link to get directions to this location in Google Maps.

### `linkToStreetView(parameters = {})`

Generate a link to a Google Map, displaying this location as a street view panorama.

```twig
<a href="{{ location.linkToStreetView() }}">Open in Google Street View</a>
```

**Arguments**

- `$parameters` (_array_) - Appended to the generated Google URL. [See full parameter details.](https://developers.google.com/maps/documentation/urls/get-started#street-view-action)

**Returns**

- _string_ - Generate a link to see a street view panorama of this location in Google Maps.

### `linkToArea(parameters = {})`

Generate a link to a Google Map, displaying the broader area surrounding this location.

```twig
<a href="{{ location.linkToArea() }}">See area in Google Maps</a>
```

**Arguments**

- `$parameters` (_array_) - Appended to the generated Google URL. [See full parameter details.](https://developers.google.com/maps/documentation/urls/get-started#map-action)

**Returns**

- _string_ - Generate a link to see this general area in Google Maps.

:::warning Displaying a Marker
The `linkToArea` method will show a map centered on the desired location, but _it will not display a marker_. If you would like a marker to appear, use the `linkToMap` method instead.
:::
