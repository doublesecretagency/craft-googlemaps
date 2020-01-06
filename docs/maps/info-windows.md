# Info Windows

## Basic Usage

It's fairly straightforward to add an [info window](https://developers.google.com/maps/documentation/javascript/infowindows) to your map. Simply specify a template path for your `infoWindowTemplate` in the [dynamic map options](/maps/dynamic/#options).

```twig
{% set options = {
    infoWindowTemplate: 'path/to/info-window.twig'
} %}

{{ googleMaps.dynamic(locations, options) }}
```

The info window will be triggered when a user clicks on the marker. If you have more than one marker, the info window template will be applied to each marker.

The info window template can be as simple or complex as you want...

```twig
<h3>{{ element.title }}</h3>
<div>{{ element.myAddressField.format() }}</div>
```

[SCREENSHOT OF WHAT THAT CODE SNIPPET WOULD OUTPUT]

## Available Variables

These variables are predefined in your info window template:

| Variable               | Type     | Description                           |
|------------|:--------:|---------------------------------------|
| `mapId`                | _string_ | The unique ID of the map              |
| `markerId`             | _string_ | The unique ID of the marker           |
| `coords`               | [coords](/models/coordinates/) | Coordinates of the marker |
| `element`              | _object_ | Full element data (see below)         |
| _alias for_ `element`  | _object_ | See explanation below :arrow_down:         |

### Automatic aliasing of the `element` variable

A second variable gets aliased to represent the exact same element object. The name of the variable is determined by the **Element Type** of `element`.

 - If your marker is an Entry, then `element == entry`
 - If your marker is an Asset, then `element == asset`
 - If your marker is a Category, then `element == category`
 - If your marker is a User, then `element == user`
 - etc.

Aliasing `element` is done purely as a convenience, it makes no difference which variable you use. 

::: warning IF THE ADDRESS IS IN A MATRIX FIELD
If the address field exists within a Matrix field, the `element` variable may not be exactly what you expect. In this case, the value will be a **Matrix Block**.

To reach the parent element, refer to the `element.owner` or `matrixBlock.owner`.

```twig
{# element == matrixBlock #}
{% set entry = matrixBlock.owner %}
```
:::

## Advanced Usage

Here's an example of a more complex info window template...

```twig
<h1>{{ entry.title }}</h1>
<div>
    {# Formatted address #}
    {{ entry.myAddressField.format() }}
</div>
<div>
    {# Other custom fields #}
    {{ entry.telephone }}<br>
    {{ entry.hours }}
</div>

{% set directions = entry.myAddressField.linkToDirections(entry.title) %}

<div>
    {# Additional links #}
    <a href="{{ entry.url }}">More Info</a> | 
    <a href="{{ directions }}" target="_blank">Get Directions</a>
</div>
```

[SCREENSHOT OF WHAT THAT CODE SNIPPET WOULD OUTPUT]

## Template Errors

If you get Twig errors in your info window template, the error message will be generated inside of your info window. In addition to logging the error normally, you can also click on the marker to view the error.

![](/images/maps/template-error.png)
