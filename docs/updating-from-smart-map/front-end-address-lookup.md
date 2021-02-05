# 🔧 Front-End Address Lookup

<update-message/>

Under the hood, the entire concept of [geocoding](/geocoding/) has evolved significantly. Fortunately, the required templating changes are minimal.

```twig
{# OLD METHODS #}
{% set results = craft.smartMap.lookup(target) %} {# became "all" #}
{% set coords  = craft.smartMap.lookupCoords(target) %}
```
```twig
{# NEW METHODS #}
{% set results = googleMaps.lookup(target).all() %}
{% set address = googleMaps.lookup(target).one() %}
{% set coords  = googleMaps.lookup(target).coords() %}
```

The general syntax has changed slightly. The `lookup` method now creates a [Lookup Model](/models/lookup-model/), which doesn't actually ping the API until you apply a subsequent method (`all`, `one`, or `coords`).

:::tip New Documentation
See the complete new [Geocoding Methods](/geocoding/methods/) documentation.
:::

## Lookup via AJAX

The premise is effectively the same, but the POST endpoints have changed:

```js
// OLD ENDPOINTS
var endpoint = '/actions/smart-map/lookup'; // became "all"
var endpoint = '/actions/smart-map/lookup/coords';
```
```js
// NEW ENDPOINTS
var endpoint = '/actions/google-maps/lookup/all';
var endpoint = '/actions/google-maps/lookup/one';
var endpoint = '/actions/google-maps/lookup/coords';
```

:::tip New Documentation
See the complete new [Geocoding via AJAX](/geocoding/via-ajax/) documentation.
:::
