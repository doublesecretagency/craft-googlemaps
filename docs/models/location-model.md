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

_bool_ - Whether the location has functional coordinates.

```twig
{% if location.hasCoords() %}
    Latitude:  {{ location.lat }}
    Longitude: {{ location.lng }}
{% endif %}
```

### `getCoords()`

_object_ - Get the location coordinates as a [coords](/models/coordinates/) JSON object.

### `getDistance(location, units = 'miles')`

_float_|_null_ - Pass a separate Location Model (including an [Address](/models/address-model/) or [Visitor](/models/visitor-model/) model), or [set of coordinates](/models/coordinates/) to measure the distance between the two points.

```twig
{% set distance = entry.homeAddress.getDistance(entry.businessAddress) %}
```

### `linkToSearch(parameters = {})`

_string_ - Generate a link to see this location in Google Maps.

```twig
<a href="{{ location.linkToSearch() }}">See Location in Google Maps</a>
```

:::tip Displaying a Marker
Counterintuitively, this method is preferred over `linkToMap` if you want to display a relevant location marker. If it's confusing, it's because we modeled the method names after [Google](https://developers.google.com/maps/documentation/urls/get-started#forming-the-url).
:::

### `linkToDirections(parameters = {}, origin = null)`

_string_ - Generate a link to see this location in Google Maps.

```twig
<a href="{{ location.linkToDirections() }}">Get Directions</a>
```

The `origin` must be another **Location Model**. Both [Address Models](/models/address-model/) and [Visitor Models](/models/visitor-model/) are extensions of the Location Model, and therefore considered a valid `origin`.

### `linkToMap(parameters = {})`

_string_ - Generate a link to see this location in Google Maps.

```twig
<a href="{{ location.linkToMap() }}">Open a Google Maps</a>
```

:::warning Displaying a Marker
The `linkToMap` method will show a map centered on the desired location, but _it will not display a marker_. If you would like a marker to appear, use the `linkToSearch` method instead.
:::

### `linkToStreetView(parameters = {})`

_string_ - Generate a link to see this location in Google Maps.

```twig
<a href="{{ location.linkToStreetView() }}">Open in Google Street View</a>
```
