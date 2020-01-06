# Static Maps

You can create a static map which contains as many markers as you'd like.

## googleMaps.static

``` twig
{{ googleMaps.static(locations, options = {}) }}
```

### `locations`

Can be any of the following...

 - An individual Address Model
 - An array of Address Models
 - An individual element (ie: an Entry)
 - An array of elements
 
If you skip the `locations` parameter by passing in _null_, the map will try its best to render without any markers.
 
### `options`

An object containing any of the following...

| Option    | Type                | Default     | Description |
|-----------|:-------------------:|:-----------:|-------------|
| `width`   | _int_               | `480`       | Set the width of the map (in px). |
| `height`  | _int_               | `320`       | Set the height of the map (in px). |
| `zoom`    | _int_               | `??`        | Set the zoom level of the map. (1 - 16) |
| `center`  | [coords](/models/coordinates/) | automatic   | Set the center position of the map. |
| `maptype` | _string_            | `"roadmap"` | Type of map ("roadmap", "satellite", "hybrid", "terrain"). |
| `scale`   | _int_               | `2`         | 2 = Retina, 1 = Non-retina |
| `imgSrc`  | _int_               | _false_     | If set to _true_, the method will return the map `src` URL directly (instead of the full `<img>` tag). |
| `field`   | _string_ or _array_ | _null_      | Which field(s) of the element(s) should be included on map? (_null_ will include all Address fields) |

## Basic Example
