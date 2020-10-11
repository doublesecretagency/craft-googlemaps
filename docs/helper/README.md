# `GoogleMaps` Helper Class

Whether you are working in Twig or PHP, the `GoogleMaps` helper class exists to make your life a little easier. It includes wrapper methods for some of the most common use-cases you will encounter.

### When using it in Twig

As long as this plugin is installed and enabled, the `googleMaps` object will be globally available in Twig. Nothing else needs to be done to instantiate it.

### When using it in PHP

You must be sure to `use` the helper class in order to reference it in your own plugin or module.

```php
use doublesecretagency\googlemaps\helpers\GoogleMaps;
```

Throughout these docs, you will see references to `GoogleMaps` in PHP. Although it's rarely stated, in each instance you _must_ make sure to `use` the Helper class.

:::warning When should I use the helper methods?
**As much as possible.** If there is a helper method which directly addresses the problem that you are trying to solve, it is preferable to use the helper method rather than the underlying service method (or whatever the helper method may be wrapping). The goal of this is to provide consistency and uniformity across and within projects.

However, if your use case is _not_ covered by this collection of helper methods, then feel free to rely directly on the underlying service methods. It's unrealistic to expect the helper methods to cover 100% of use-cases, so do what you must to get the job done.
:::

---

## Generate Maps

:::code
```twig
{# Generate a dynamic map #}
{{ googleMaps.map(locations).tag() }}

{# Generate a static map #}
{{ googleMaps.img(locations).tag() }}
```
```php
// Generate a dynamic map
GoogleMaps::map($locations)->tag();

// Generate a static map
GoogleMaps::img($locations)->tag();
```
:::

:::code
```twig
{# Retrieve an existing dynamic map #}
{% set map = googleMaps.getMap(mapId) %}
```
```php
// Retrieve an existing dynamic map
$map = GoogleMaps::getMap($mapId);
```
:::

For more information, check out the documentation on [Dynamic Maps](/maps/dynamic/), [Static Maps](/maps/static/), and the maps [API](/maps/api/).

---

## Geocoding (Address Lookups)

:::code
```twig
{# Get all matching results (sorted by best match) #}
{% set allMatches = googleMaps.lookup(target).all() %}

{# Get the first (best) matching result #}
{% set bestMatch = googleMaps.lookup(target).one() %}

{# Get only the coordinates of the first matching address #}
{% set coords = googleMaps.lookup(target).coords() %}
```
```php
// Get all matching results (sorted by best match)
$allMatches = GoogleMaps::lookup($target).all();

// Get the first (best) matching result
$bestMatch = GoogleMaps::lookup($target).one();

// Get only the coordinates of the first matching address
$coords = GoogleMaps::lookup($target).coords();
```
:::

For more information, check out the documentation on [Geocoding](/geocoding/).

---

## Perform Visitor Geolocation

The `visitor` attribute relies on the magic getter of `getVisitor`. They are functionally identical.

Optionally, you can override the `service` and/or `ip` when performing a visitor geolocation.

:::code
```twig
{# Get visitor geolocation information #}
{% set visitor = googleMaps.visitor %}

{# The exact same method, with optional overrides #}
{% set visitor = googleMaps.getVisitor({
    'service': 'ipstack',
    'ip': '1.2.3.4'
}) %}
```
```php
// Get visitor geolocation information
$visitor = GoogleMaps::visitor;

// The exact same method, with optional overrides
$visitor = GoogleMaps::getVisitor([
    'service' => 'ipstack',
    'ip' => '1.2.3.4'
]);
```
:::

For more information, check out the documentation on [Geolocation](/geolocation/).

---

## Manually Load JS Files

You can manually load the necessary JavaScript files, if they are not being loaded automatically...

:::code
```twig
{# Manually load required JS files #}
{% do googleMaps.loadAssets() %}
```
```php
// Manually load required JS files
GoogleMaps::loadAssets();
```
:::

---

## Override Google API keys

If you need to override the Google API keys via Twig, you can...

:::code
```twig
{# Override server key #}
{% do googleMaps.setServerKey('lorem') %}

{# Override browser key #}
{% do googleMaps.setBrowserKey('ipsum') %}
```
```php
// Override server key
GoogleMaps::setServerKey('lorem');

// Override browser key
GoogleMaps::setBrowserKey('ipsum');
```
:::

There are parallel methods for retrieving the API keys...

:::code
```twig
{# Get server key #}
{% set serverKey = googleMaps.getServerKey() %}

{# Get browser key #}
{% set browserKey = googleMaps.getBrowserKey() %}
```
```php
// Get server key
$serverKey = GoogleMaps::getServerKey();

// Get browser key
$browserKey = GoogleMaps::getBrowserKey();
```
:::

And since Twig is very forgiving about using magic methods, you can abbreviate those even further...

:::code
```twig
{{ googleMaps.serverKey }}
{{ googleMaps.browserKey }}
```
```php
GoogleMaps::serverKey;
GoogleMaps::browserKey;
```
:::
