# Info Windows

An [info window](https://developers.google.com/maps/documentation/javascript/infowindows) is the formal name for the bubble that pops up when you click on a marker...

<img class="dropshadow" :src="$withBase('/images/maps/info-window.png')" alt="Example of an info window">

In order to add info windows to your markers you'll want to specify an `infoWindowTemplate` value. This template can be applied via either of the [map](/models/dynamic-map-model/#construct-locations-options) or [markers](/models/dynamic-map-model/#markers-locations-options) parameters.

:::code
```twig
{% set options = {
    'infoWindowTemplate': 'example/my-info-window'
} %}

{% set map = googleMaps.map(locations, options) %}
```
```php
$options = [
    'infoWindowTemplate' => 'example/my-info-window'
];

$map = GoogleMaps::map($locations, $options);
```
:::

The Twig template can live anywhere you want within your templates folder.

## Info Window Twig Template

Within the context of an info window Twig template, a few magic variables will already be set. Depending on what type of value you provided for the [locations](/dynamic-maps/locations/) parameter, some of these variables may or may not be available to you.

|       | Element | Address | Coords |             |
|-------|:-------:|:-------:|:------:|:------------|
| `mapId`         | ✅ | ✅ | ✅ | ID of map |
| `markerId`      | ✅ | ❌ | ❌ | ID of marker |
| `element`       | ✅ | ❌ | ❌ | Element origin of the marker |
| `entry` _(etc)_ | ✅ | ❌ | ❌ | Alias `element` (named per type) |
| `address`       | ✅ | ✅ | ❌ | Address represented by the marker |
| `coords`        | ✅ | ✅ | ✅ | Coordinates of the marker |

:::warning Element Type Variables
The `entry` variable is synonymous with the `element` variable, assuming the marker was created using an Entry. Similar logic applies with any other element type (`asset`, `user`, `category`, etc), including custom element types.
:::

This example (using Tailwind CSS) produces the screenshot seen at the top of the page...

```twig
{# Get the entry's thumbnail image #}
{% set image = entry.thumbnail.one() %}

{# This example uses Tailwind CSS #}
<div class="pl-1 pt-1">

    {# Show thumbnail if it exists #}
    {% if image %}
        <img
            alt="Thumbnail image"
            src="{{ image.url }}"
            style="max-width:320px"
            class="rounded border border-gray-500"
        >
    {% endif %}

    {# Show entry and address information #}
    <div class="px-1 pb-1">
        <div class="pt-2 text-gray-800 text-xl font-bold">{{ entry.title }}</div>
        <div class="pt-1 text-gray-800 text-base">{{ address }}</div>
        <div class="pt-2 text-base">
            <a class="text-blue-700" href="{{ entry.url }}#hours">Hours</a>
            &nbsp;&bull;&nbsp;
            <a class="text-blue-700" href="{{ address.linkToDirections() }}">Directions</a>
            &nbsp;&bull;&nbsp;
            <a class="text-blue-700" href="{{ entry.url }}#info">More Information</a>
        </div>
    </div>

</div>
```

## Info Window Template Errors

In the event of an error in your Twig code, an error message will be thrown. The message will be contained within the info window itself, in order to streamline debugging.

<img class="dropshadow" :src="$withBase('/images/maps/info-window-error.png')" alt="Example of an info window Twig template error">

The info window will display the template path, alongside the error message being returned.
