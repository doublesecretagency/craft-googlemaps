---
description:
---

# Linking to a Map

There are four different ways to link externally to a Google Map...

 - `linkToMap`
 - `linkToDirections`
 - `linkToStreetView`
 - `linkToArea`

Each of these methods can be called from a typical [Location Model](/models/location-model/). Remember, an [Address Model](/models/address-model/) extends the Location Model (as does the [Visitor Model](/models/visitor-model/)).

In other words, your typical usage may look something like this...

```twig
<a href="{{ address.linkToDirections() }}">Get Directions</a>
```

Each method allows a set of optional parameters to be specified... which parameters are available depends entirely on which method you are using.

## `linkToMap`

To see the location marker appear on a standalone Google Map, use the [`linkToMap` method](/models/location-model/#linktomap-parameters).

The `query` and/or `query_place_id` parameters will be set automatically based on the specified location.

:::tip Location Marker
Use this method to show a map containing a location marker.
:::

## `linkToDirections`

To get directions to a specific location, use the [`linkToDirections` method](/models/location-model/#linktodirections-parameters-origin-null). It will prepopulate the origin and/or destination for you.

The `destination` and/or `destination_place_id` parameters will be set automatically based on the specified location. If an `origin` is specified, it will be used to automatically set the `origin` and/or `origin_place_id` parameters.

## `linkToStreetView`

To see a street level panorama of a specified location, use the [`linkToStreetView` method](/models/location-model/#linktostreetview-parameters).

The `viewpoint` parameter will be set automatically based on the specified location.

## `linkToArea`

To see a map of the area, centered on the specified location, use the [`linkToArea` method](/models/location-model/#linktoarea-parameters). Please be aware, **no markers will appear on this map.**

The `center` parameter will be set automatically based on the specified location.

:::warning Displaying a marker on the map
If you want a map which displays a marker representing the specified location, be sure to use the [`linkToMap` method](#linktomap) instead of the `linkToArea` method.
:::
