# Linking to a Map

There are four different ways to link externally to a Google Map...

 - `linkToSearch`
 - `linkToDirections`
 - `linkToMap`
 - `linkToStreetView`

Each of these methods can be called from a typical [Location Model](/models/location-model/). Don't forget, an [Address Model](/models/address-model/) extends the Location Model (as does the [Visitor Model](/models/visitor-model/)).

In other words, your typical usage may look something like this...

```twig
<a href="{{ address.linkToDirections() }}">Get Directions</a>
```

Each method allows a set of optional parameters to be specified... which parameters are available depends entirely on which method you are using. You'll notice that the methods correlate directly with [Google's URL API](https://developers.google.com/maps/documentation/urls/get-started#forming-the-url), so you can pick the appropriate optional parameters.

---
---

## `linkToSearch`

To see the location marker appear on a standalone Google Map, use the [`linkToSearch` method](/models/location-model/#linktosearch-parameters).

The `query` and/or `query_place_id` parameters will be set automatically based on the specified location.

:::tip Location Marker
Use this method to show a map containing a location marker.
:::

## `linkToDirections`

To get directions to a specific location, use the [`linkToDirections` method](/models/location-model/#linktodirections-parameters-origin-null). It will prepopulate the origin and/or destination for you.

The `destination` and/or `destination_place_id` parameters will be set automatically based on the specified location. If an `origin` is specified, it will be used to automatically set the `origin` and/or `origin_place_id` parameters.

## `linkToMap`

To see a map of the area, centered on the specified location, use the [`linkToMap` method](/models/location-model/#linktomap-parameters). No markers will appear on this map.

The `center` parameter will be set automatically based on the specified location.

:::warning Displaying a marker on the map
If you want a map which displays a marker representing the specified location, be sure to use the [`linkToSearch` method](#linktosearch) instead of the `linkToMap` method.

It's a bit counterintuitive, but the `linkToMap` method will not show any markers on a map, whereas the `linkToSearch` method shows a marker for the specified location.
:::

## `linkToStreetView`

To see a street level panorama of a specified location, use the [`linkToStreetView` method](/models/location-model/#linktostreetview-parameters).

The `viewpoint` parameter will be set automatically based on the specified location.
