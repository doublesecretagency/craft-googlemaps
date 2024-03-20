<img width="66" align="left" src="https://plugins.doublesecretagency.com/google-maps/images/icon.svg" alt="Plugin icon">

# Google Maps plugin for Craft CMS

**Maps in minutes. Powered by the Google Maps API.**

---

<div align="center">
  <a href="#includes-easy-to-use-address-fields">Address Fields</a> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
  <a href="#create-flexible-dynamic--static-maps">Dynamic & Static Maps</a> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
  <a href="#search-for-the-nearest-locations">Proximity Searching</a> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
  <a href="#simple-ip-based-visitor-geolocation">IP-based Geolocation</a>
</div>

---

### Includes easy-to-use Address Fields

When managing your Craft data, each location can be set with a convenient Address field...

<p align="center">
    <img width="603" src="https://github.com/doublesecretagency/craft-googlemaps/blob/9b27de094d7cf8fc5b1f044354b99ddce9019a89/docs/README-images/address-field.gif?raw=true" alt="Animated GIF of an Address field">
</p>

### Create flexible Dynamic & Static Maps

Add markers, use info windows, style maps, change marker icons, apply KML layers, and much, much more...

<p align="center">
    <img src="https://github.com/doublesecretagency/craft-googlemaps/blob/9b27de094d7cf8fc5b1f044354b99ddce9019a89/docs/README-images/dynamic-map.png?raw=true" alt="Screenshot of a dynamic map">
</p>

>**Universal API**
>
>The plugin features a powerful [universal API](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/universal-api/) which works nearly identically across **JavaScript, Twig, and PHP!**

### Search for the Nearest Locations

Find the closest locations, and sort the results from nearest to furthest...

<p align="center">
    <img src="https://github.com/doublesecretagency/craft-googlemaps/blob/9b27de094d7cf8fc5b1f044354b99ddce9019a89/docs/README-images/proximity-search.png?raw=true" alt="Screenshot of a set of proximity search results">
</p>

### Simple IP-based Visitor Geolocation

Locate your visitors based on their device's IP address...

<p align="center">
    <img src="https://github.com/doublesecretagency/craft-googlemaps/blob/9b27de094d7cf8fc5b1f044354b99ddce9019a89/docs/README-images/geolocation.png?raw=true" alt="Screenshot of visitor geolocation results">
</p>

---

## How to Install the Plugin

### Installation via Plugin Store

See the complete instructions for [installing via the plugin store...](https://plugins.doublesecretagency.com/google-maps/getting-started/#installation-via-plugin-store)

### Installation via Console Commands

To install the **Google Maps** plugin via the console, follow these steps:

1. Open your terminal and go to your Craft project:

```sh
cd /path/to/project
```

2. Then tell Composer to load the plugin:

```sh
composer require doublesecretagency/craft-googlemaps
```

3. Then tell Craft to install the plugin:

```sh
./craft plugin/install google-maps
```

>Alternatively, you can visit the **Settings > Plugins** page to finish the installation. If installed via the control panel, you will automatically be redirected to configure the plugin after installation is complete.

Once installed, you will need to [add Google API keys...](https://plugins.doublesecretagency.com/google-maps/getting-started/api-keys/)

---

## Simple Code Examples

**These examples barely scratch the surface of what is possible!**

For complete details, check out the [official plugin documentation...](https://plugins.doublesecretagency.com/google-maps/)

### Adding a Dynamic Map

```twig
{# Get the entries #}
{% set entries = craft.entries.section('locations').all() %}

{# Place them on a dynamic map #}
{{ googleMaps.map(entries).tag() }}
```

> [Full dynamic maps docs are here...](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/)

### Adding a Static Map

```twig
{# Get the entries #}
{% set entries = craft.entries.section('locations').all() %}

{# Place them on a static map #}
{{ googleMaps.img(entries).tag() }}
```

Or use the `src` attribute directly...

```twig
{# Get only the image URL of a static map #}
{% set src = googleMaps.img(entries).src() %}

{# Display the image tag manually #}
<img src="{{ src }}">
```

> [Full static maps docs are here...](https://plugins.doublesecretagency.com/google-maps/static-maps/)

### Conducting a Proximity Search

```twig
{# Set the geocoding lookup target #}
{% set target = '123 Main St' %}

{# Get a set of entries, sorted by closest to target #}
{% set entries = craft.entries.myAddressField(target).orderBy('distance').all() %}
```

> [Full proximity search docs are here...](https://plugins.doublesecretagency.com/google-maps/proximity-search/)

### Conducting a Geocoding Address Lookup

```twig
{# Set the geocoding lookup target #}
{% set target = '123 Main St' %}

{# Get a set of geocoding results #}
{% set results = googleMaps.lookup(target).all() %}
```

> [Full geocoding docs are here...](https://plugins.doublesecretagency.com/google-maps/geocoding/)

### Geolocating Visitors

```twig
{# Get location data based on each visitor's IP address #}
{% set visitor = googleMaps.visitor %}
```

> [Full geolocation docs are here...](https://plugins.doublesecretagency.com/google-maps/geolocation/)

---

## Further Reading

If you haven't already, flip through the [complete plugin documentation](https://plugins.doublesecretagency.com/google-maps/). The examples above are just the tip of the iceberg, there is so much more that is possible!

And if you have any remaining questions, feel free to [reach out to us](https://www.doublesecretagency.com/contact) (via Discord is preferred).

**On behalf of Double Secret Agency, thanks for checking out our plugin!** 🍺

<p align="center">
    <img width="130" src="https://www.doublesecretagency.com/resources/images/dsa-transparent.png" alt="Logo for Double Secret Agency">
</p>
