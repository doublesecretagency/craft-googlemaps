# 🔧 Front-End Address Lookup

<update-message/>

Under the hood, the entire concept of [geocoding](/geocoding/) has evolved significantly. From a templating perspective, fortunately, the required changes are minimal.

## Lookup via Twig

The general syntax is relatively unchanged:

```twig
{# OLD #}
{% set results = craft.smartMap.lookup(target) %}

{# NEW #}
{% set results = googleMaps.lookup(target).all() %}
```

Pay attention to the `.all()` parameter at the end. You could alternatively use `.one()` or `.coords()` to fetch the results in a different format.

## Lookup Coordinates

Same as above, except we're using the `.coords()` parameter to fetch only the [coordinates](/models/coordinates/) of the **most likely matching Address**.

```twig
{# OLD #}
{% set coords = craft.smartMap.lookupCoords(target) %}

{# NEW #}
{% set results = googleMaps.lookup(target).coords() %}
```

:::tip New Documentation
See the complete new [Geocoding Methods](/geocoding/methods/) documentation.
:::

## Lookup via AJAX

The premise is effectively the same, but the endpoint has changed:

```js
// OLD
var endpoint = '/actions/smart-map/lookup';

// NEW
var endpoint = '/actions/google-maps/lookup/all';
```

Retrieve coordinates only:

```js
// OLD
var endpoint = '/actions/smart-map/lookup/coords';

// NEW
var endpoint = '/actions/google-maps/lookup/coords';
```

:::tip New Documentation
See the complete new [Geocoding via AJAX](/geocoding/via-ajax/) documentation.
:::
