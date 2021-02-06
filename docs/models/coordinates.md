# Coordinates

A `coords` value is a basic set of coordinates (latitude & longitude) in a simple JSON format:

:::code
```js JSON
{
    "lat": 32.3113966,
    "lng": -64.7527469
}
```
:::

To be clear, coordinates are not actually a Model (and so this page technically doesn't belong here). But the **format** of `coords` is commonly used throughout the plugin, which gives coordinates a pseudo-model behavior.

## Common Format

A set of coordinates is handled in this common format. Any place you encounter a `coords` value, it will be handled in this format. Here are just a few examples of where you'd see a set of coordinates in this format:

| Where               | Specifically |   |
|:--------------------|:-------------|---|
| Maps                | As the center point of a map. | [Reference](/dynamic-maps/) |
| Info Windows        | As a predefined value in an info window template. | [Reference](/dynamic-maps/info-windows/#info-window-twig-template) |
| Proximity Search    | As the target of a proximity search. | [Reference](/proximity-search/options/#target) |
| Geocoding (Lookup)  | In a set of geocoding results. | [Reference](/geocoding/methods/#coords) |
| Address Fields      | Available on the parent Location Model. | [Reference](/models/location-model/#coords) |
| Visitor Geolocation | Available on the parent Location Model. | [Reference](/models/location-model/#coords) |
 
## Google Maps Coordinates

The internal format of `coords` aligns with the established format of a [Google Maps `LatLngLiteral` object](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngLiteral). Since the two formats are effectively identical, you can use the values interchangeably. 

:::tip Nearly Identical
Roughly speaking, you can say that the Google Maps plugin "uses" the `LatLngLiteral` to handle coordinates internally. There may be some subtle differences, but you can generally treat them as the same thing and not encounter any issues.
:::
