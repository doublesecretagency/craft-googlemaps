# Coordinates

A `coords` value is a basic set of coordinates (latitude & longitude) in a simple JSON format:

```json
{
    lat: 34.038136,
    lng: -118.243996
}
```

To be clear, `coords` is not actually a Model (and so this page technically doesn't belong here). But the _format_ of `coords` is universally shared throughout the plugin, which gives the `coords` a pseudo-model behavior.

## Universal Format

Any place you encounter a `coords` value, it will be handled in this format. Here are just a few examples of where you'd see a set of coordinates in this format:

 - On the [Location Model](/models/location-model/#coords).
 - As the [center](/maps/dynamic/#options) point of a map.
 - As the [target](/proximity-search/options/) of a proximity search.
 - As a [predefined value](/maps/info-windows/#available-variables) in an info window template.
 
## Google Maps `LatLngLiteral`

The internal format of `coords` aligns with the established format of a [Google Maps `LatLngLiteral` object](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngLiteral). Since the two formats are identical, you can use the values interchangeably. 

::: tip SEMANTICS
Although the resemblance was never intentional, you could say that the Google Maps plugin "uses" the `LatLngLiteral` to handle coordinates internally.
:::
