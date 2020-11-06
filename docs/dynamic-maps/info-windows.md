# Info Windows

<img class="dropshadow" :src="$withBase('/images/maps/info-window.png')" alt="Example of an Info Window">

## Available Variables

The following variables will be automatically set in the info window Twig template. Depending on what type of value you provided for the [locations](/dynamic-maps/locations/), certain variables may or may not be available to you.

|       | Element | Address | Coords | Description |
|-------|:-------:|:-------:|:------:|:------------|
| `mapId`         | ✅ | ✅ | ✅ | ID of map |
| `markerId`      | ✅ | ❌ | ❌ | ID of marker |
| `element`       | ✅ | ❌ | ❌ | Element origin of the marker |
| `entry` _(etc)_ | ✅ | ❌ | ❌ | Alias `element` (named per type) |
| `address`       | ✅ | ✅ | ❌ | Address represented by the marker |
| `coords`        | ✅ | ✅ | ✅ | Coordinates of the marker |

:::warning Element Type Variables
Assuming the marker was created using an Entry, an `entry` variable will be available. It contains the exact same data as `element`.

Similar logic applies with all other Element Types (`asset`, `user`, `category`, etc), including custom Element Types.
:::

:::tip Marker Info Template Errors
In the event of Twig errors in your marker info template, the error will be rendered inside of the info window. This allows for you to more easily debug any problems that may be occurring.
:::
