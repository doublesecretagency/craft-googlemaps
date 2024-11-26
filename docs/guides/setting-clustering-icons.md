---
description:
---

# Setting Clustering Icons

If you are using [marker clustering](/dynamic-maps/clustering-markers/), it's relatively easy to override the default clustering icons with your own custom icons. The default clustering icons are created using a **dynamic SVG**.

Here is a helpful script which replicates the default behavior...

## Download the `renderer.js` file

Download this file, and place it into a public front-end directory.

- [**Latest version on GitHub**](https://github.com/doublesecretagency/craft-googlemaps/blob/v4/docs/examples/js/renderer.js)

:::tip This file belongs to you now
Once you have copied the `renderer.js` file locally, you are free to make any further adjustments as you deem necessary.

If multiple renderers are required, you may copy and/or rename the files and functions.
:::

## Load JS script into the page

Now that you have a new JavaScript file, load it into the page...

```twig
{% js url('path/to/renderer.js') at head %}
```

The file should be loaded `at head` to ensure that it's available when needed.

## Enable custom icon rendering

Pass the name of your new renderer function in as the `renderer` option value.

:::code
```js
// Set a custom cluster rendering function
const options = {
    'cluster': {
        'renderer': CustomRenderer
    }
};
```
```twig
{# Set a custom cluster rendering function #}
{% set options = {
    'cluster': {
        'renderer': 'CustomRenderer'
    }
} %}
```
```php
// Set a custom cluster rendering function
$options = [
    'cluster' => [
        'renderer' => 'CustomRenderer'
    ]
];
```
:::

In **JavaScript**, you are directly specifying a **callback function**. Do not put quotes around the renderer name, since you are explicitly referencing a callback _variable_.

In **Twig/PHP**, you must specify the renderer as a **string**. The callback will be parsed correctly.

## Customize renderer function

From here, you are free to customize the renderer function until you are satisfied with the results.

:::tip More Info
For more information, see the official Google docs for a [custom renderer...](https://googlemaps.github.io/js-markerclusterer/interfaces/MarkerClustererOptions.html#renderer)
:::
