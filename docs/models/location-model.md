# Location Model

Both the [Address Model](/models/address-model/) and [Visitor Model](/models/visitor-model/) are extensions of the Location Model.

## Public Properties

### `lat`

_float_ - Latitude of location.

### `lng`

_float_ - Longitude of location.

### `coords`

_object_ - Alias for `getCoords()`.

### `data`

_object_ - The raw original data returned by the service which generated the model.

::: tip What information is contained in "data"?
If the model is an [Address Model](/models/address-model/), the `data` will contain a subset of the Google API geocoding results.

If the model is a [Visitor Model](/models/visitor-model/), the `data` will contain whatever was returned by the geolocation service.
:::

## Public Methods

### `hasCoords()`

_bool_ - Whether or not the location has functional coordinates.

```twig
{% if location.hasCoords() %}
    Latitude:  {{ location.lat }}
    Longitude: {{ location.lng }}
{% endif %}
```

### `getCoords()`

_object_ - Get the location coordinates as a [coords](/models/coordinates/) JSON object.

### `getDistance(coords, units = 'miles')`

Pass in coordinates to measure the distance between points. Accepts coordinates in a JSON format.

In this example, the `home` and `office` variables are both Location Models. We are measuring the distance between the two locations.

```twig
{% set homeCoords = home.getCoords() %}
{% set distance = office.getDistance(homeCoords) %}
```

### `linkToGoogleMap()`

_string_ - Generate a link to see this location in Google Maps.

```twig
{% set href = location.linkToGoogleMap() %}
<a href="{{ href }}">Open in Google Maps</a>
```

### `linkToDirections(options = {})`

_string_ - Generate a link to get directions in Google Maps.

```twig
{% set href = location.linkToDirections() %}
<a href="{{ href }}">Directions in Google Maps</a>
```

| Option             | Type     | Default  | Description |
|--------------------|:--------:|:--------:|------------------------------------|
| `destinationTitle` | _string_ | Title of Element (if possible)   | Title of the destination marker. |
| `startingAddress`  | `"auto"`, [coords](/models/coordinates/), or _null_  | `"auto"` | Coordinates of a _separate_ location (see more below). |
| `startingTitle`    | _string_ | _null_   | Title of the starting marker. |

::: warning linkToDirections('Destination Title')
You can also pass a _string_ in as the sole parameter. If you pass only a string, it will be used for the `destinationTitle` value.
:::

::: tip Titles may not always appear
Depending on the browser and usage, the `destinationTitle` and `startingTitle` may or may not be utilized.
:::

### Starting Address

A path will be calculated between the Starting Address and Destination Address.

The starting address can be one of these things:

 - `"auto"` - (Default) Automatically use the visitor's current location as the starting address.
 - [coords](/models/coordinates/) - An object derived from another [location](/models/location-model/#getcoords) or compiled manually.
 - _null_ - If you want to disable auto-detection of the current address, set this to _null_.

::: tip
The `"auto"` autodetection simply uses "Current Location" as the `saddr` value.

```twig
...&saddr=Current+Location
```
:::
