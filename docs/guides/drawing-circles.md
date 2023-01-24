---
description: Placing circles on a map is just as simple as adding markers. You can easily control the size and color of each circle.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/drawing-circles/
- property: og:title
  content: Drawing Circles | Google Maps plugin for Craft CMS
- property: og:description
  content: Placing circles on a map is just as simple as adding markers. You can easily control the size and color of each circle.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/circles-map.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/drawing-circles/
- property: twitter:title
  content: Drawing Circles | Google Maps plugin for Craft CMS
- property: twitter:description
  content: Placing circles on a map is just as simple as adding markers. You can easily control the size and color of each circle.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/circles-map.png
---

# Drawing Circles

Using the Google API, it's possible to draw [circles](https://developers.google.com/maps/documentation/javascript/examples/circle-simple) on your dynamic maps.

We've brought this functionality into the plugin. The `circles` method is part of the [Universal API](/dynamic-maps/universal-methods/#circles-locations-options), so you can use it in any available language...

:::code
```js
map.circles(locations, options);
```
```twig
{% do map.circles(locations, options) %}
```
```php
$map->circles($locations, $options);
```
:::

Specify one or more [**locations**](/dynamic-maps/locations/), and optionally provide an array of **options**.

There are only two `options` (both optional)...

| Option          | Type     | Description |
|-----------------|----------|-------------|
| `id`            | _string_ | Reference point for each circle. |
| `circleOptions` | _array_  | Accepts any [`google.maps.CircleOptions`](https://developers.google.com/maps/documentation/javascript/reference/polygon#CircleOptions) properties. |

## Circle Options

Be aware, most of what you want to control will be nested within the `circleOptions` option. In essence, you'll need to go **two layers deep** to configure each set of circles.

Here is a practical example in Twig...

```twig
{# Get all circle locations #}
{% set locations = craft.entries.section('circles').all() %}

{# Note that `circleOptions` are nested within options #}
{% set options = {
    'circleOptions': {
        'radius': 50000,
        'strokeColor': '#7A9F35',
        'strokeOpacity': 0.8,
        'strokeWeight': 2,
        'fillColor': '#7A9F35',
        'fillOpacity': 0.35,
    }
} %}

{# Show a map with circles #}
{{ googleMaps.map().circles(locations, options).tag() }}
```

In the example above, all circles will be the exact same **size** and **color**.

## Setting a Radius

### The radius must be nested below `circleOptions`

As noted above, be sure that the `radius` value is being specified within the `circleOptions` option.

### The radius must have a value in meters

The Google Maps API is expecting the `radius` to be specified in [meters](https://developers.google.com/maps/documentation/javascript/shapes#circles).

:::code
```js
// Draw a circle with a 5 kilometer radius
const options = {
    'circleOptions': {
        'radius': 5000
    }
};
```
```twig
{# Draw a circle with a 5 kilometer radius #}
{% set options = {
    'circleOptions': {
        'radius': 5000
    }
} %}
```
```php
// Draw a circle with a 5 kilometer radius
$options = [
    'circleOptions' => [
        'radius' => 5000
    ]
];
```
:::

If you are performing a calculation to determine the radius value dynamically, be sure that it is ultimately returning a value in **meters**.

## Circles of Different Sizes/Colors

It's possible to add multiple circles that are **different sizes and/or colors**.

### Simple Example

Create each circle individually by looping through all locations and configuring them one at a time...

```twig
{# Initialize a map object #}
{% set map = googleMaps.map() %}

{# Loop over all circles #}
{% for circle in craft.entries.section('circles').all() %}
    
    {# Set the options for each circle individually #}
    {% set options = {
        'circleOptions': {...}
    } %}

    {# Add configured circle to the map #}
    {% do map.circles(circle, options) %}

{% endfor %}
```

### Complex Example

Here's one possible way to dynamically manage the size and color of each circle...

```twig
{# Set the shared options for all circles #}
{% set baseCircleOptions = {
    'strokeOpacity': 0.8,
    'strokeWeight': 2,
    'fillOpacity': 0.35,
} %}

{# Initialize a map object #}
{% set map = googleMaps.map() %}

{# Loop over all circles #}
{% for circle in craft.entries.section('circles').all() %}

    {# Set the options for a single circle #}
    {% set options = {
        'circleOptions': baseCircleOptions|merge({
            'radius': (circle.radius * 1000),
            'fillColor': circle.color.getHex(),
            'strokeColor': circle.color.getHex(),
        })
    } %}

    {# Add configured circle to the map #}
    {% do map.circles(circle, options) %}

{% endfor %}
```

In the example above, we've also created two new fields:

 - **Radius** - Determines the size of the circle.
 - **Color** - Determines the color of the circle.

These fields will live alongside our **Address** field, which determines the location of each circle.

<img class="dropshadow" :src="$withBase('/images/guides/circles-fields.png')" alt="Screenshot of options when exporting" style="max-width:578px; margin-top:4px;">

Once you have those fields and Twig snippet in place, you'll be able to generate a map like this...

<img class="dropshadow" :src="$withBase('/images/guides/circles-map.png')" alt="Screenshot of options when exporting" style="max-width:650px; margin-top:4px;">

:::warning Various Approaches
While the example above uses two additional fields (**Radius** and **Color**) to control the circle options, there are many other ways to manage how your circles are displayed. Feel free to adapt this approach to better fit your existing architecture.
:::
