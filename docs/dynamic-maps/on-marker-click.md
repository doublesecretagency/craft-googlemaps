---
description: When a marker is clicked, it can open an info window. Or it can trigger a JavaScript callback, or even navigate to a specified URL.
---

# On Marker Click

When clicking on a marker, there are three possible actions that can be triggered. Each of these scenarios can be configured when generating either the `map` and/or `markers`.

## Open an Info Window

If you've set the [`infoWindowTemplate`](/dynamic-maps/basic-map-management/#dynamic-map-options) option, a complete **info window** will be displayed when you click on each map marker. Read the complete guide to [info windows...](/dynamic-maps/info-windows/)

:::warning Not available in JavaScript
The Twig template specified in `infoWindowTemplate` must be rendered on the server side, so this option is only available when building a map in Twig/PHP.
:::

## Navigate to a URL

If you simply want to go to a different page when a marker is clicked, use the [`markerLink`](/dynamic-maps/basic-map-management/#dynamic-map-options) option.

**Usage in JavaScript**

When creating a map in JavaScript, you can specify a static link. It's not possible to dynamically replace Element data if you are exclusively using JavaScript.

**Usage in Twig/PHP**

In Twig/PHP, you can specify Element data using single-bracket tokens.

:::code
```js
// Link to a static URL
const options = {
    'markerLink': 'https://www.example.com'
};
```
```twig
{# Link to URL of each element #}
{% set options = {
    'markerLink': '{url}'
} %}
```
```php
// Link to URL of each element
$options = [
    'markerLink' => '{url}'
];
```
:::

## Trigger a JS Callback Function

To fire a JavaScript callback function, specify it with the [`markerClick`](/dynamic-maps/basic-map-management/#dynamic-map-options) option.

**Usage in JavaScript**

In JavaScript, you can specify either a **named function** or an **anonymous function**. Either syntax will be passed a single `event` parameter, which is a byproduct of the marker's `click` event.

```js
// Your custom callback function
function myCallbackFunction(event) {
    
    // ... whatever you want to happen when the marker is clicked
    
}
```

**Usage in Twig/PHP**

If you're not specifying the function in JavaScript, you **must** specify the function as a **string**. Whether you are specifying a named function or an anonymous function, wrap the entire thing in quotes.

:::warning Keep it short
It's possible, but tricky, to pass complex JS functions from Twig/PHP. We recommend offloading any complex JS logic into JavaScript directly, ideally in a separate JS file.
:::

In Twig/PHP, you can specify Element data using single-bracket tokens.

:::code
```js
// Using a named JS function
const options = {
    'markerClick': myCallbackFunction
};

// Using an anonymous JS function
const options = {
    'markerClick': function (event) {
        console.log(event);
    }
};
```
```twig
{# Using a named JS function #}
{% set options = {
    'markerClick': 'myCallbackFunction'
} %}

{# Using an anonymous JS function #}
{% set options = {
    'markerClick': 'function (event) {
        console.log("{id} - {title}");
    }'
} %}
```
```php
// Using a named JS function
$options = {
    'markerClick': 'myCallbackFunction'
};

// Using an anonymous JS function
$options = [
    'markerClick' => 'function (event) {
        console.log("{id} - {title}");
    }'
];
```
:::
