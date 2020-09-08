# Getting Directions

As long as you have a valid [Location Model](/models/location-model/), you can easily link back to Google Maps itself.

## Linking to directions

```twig
{% set href = address.linkToDirections() %}
<a href="{{ href }}">Directions in Google Maps</a>
```

::: tip MORE INFO
For full details, see the `linkToDirections` method of the [Location Model](/models/location-model/#linktodirections-options).
:::

## Linking to a map

```twig
{% set href = address.linkToGoogleMap() %}
<a href="{{ href }}">Open in Google Maps</a>
```
