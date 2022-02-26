---
description: Add a reactive proximity search with the help of Sprig! The map and results will update automatically whenever the search criteria changes.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/sprig/
- property: og:title
  content: Proximity Search with Sprig | Google Maps plugin for Craft CMS
- property: og:description
  content: Add a reactive proximity search with the help of Sprig! The map and results will update automatically whenever the search criteria changes.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/sprig.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/sprig/
- property: twitter:title
  content: Proximity Search with Sprig | Google Maps plugin for Craft CMS
- property: twitter:description
  content: Add a reactive proximity search with the help of Sprig! The map and results will update automatically whenever the search criteria changes.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/sprig.png
---

# Proximity Search with Sprig

[Sprig](https://putyourlightson.com/plugins/sprig) is a reactive component framework for Craft, which makes it amazingly easy to create dynamic components using Twig. When used together with the Google Maps plugin, it's possible to have a fully featured map populated by the results of a comprehensive proximity search.

Simply follow the instructions below to add a Sprig-powered proximity search to your site. While each implementation will be unique, you can use the code provided below to get started.

## What it Looks Like

Several small pieces play a cooperative role in handling the proximity search...

<img class="dropshadow" :src="$withBase('/images/guides/sprig.png')" alt="Annotated screenshot of Google Maps being used with Sprig" style="max-width:660px">

1. The **search input** is a simple text field for capturing the proximity search `target`.
2. Optional **search filters** can refine the results (eg: `range`).
3. An ordinary **submit button** will trigger the search.
4. A **list of search results** is displayed besides the map.
5. The **dynamic map** will show markers of the search results.

## How it Works

### 1. Download the `proximity-search.twig` file

Download this file, and place it somewhere in your `templates` folder. It's a common practice to put [Sprig components](https://putyourlightson.com/plugins/sprig#how-it-works) into a `_components` folder, but that is not required.

- [**Latest version on GitHub**](https://github.com/doublesecretagency/craft-googlemaps/blob/v4/docs/examples/twig/_components/proximity-search.twig)

:::tip This file belongs to you now
Once you have copied the `proximity-search.twig` file locally, you are free to make any further adjustments as you deem necessary.
:::

### 2. Add the following Twig snippet

Now that you have a copy of the `proximity-search` component, here's how to use it in a template...

```twig
{# Dynamically inject Sprig component #}
{{ sprig('_components/proximity-search') }}

{# Load required Sprig scripts #}
{{ sprig.script }}
```

## Additional Information

### Map IDs must be identical

It is important that the map ID in the [`googleMaps.init` method](/javascript/googlemaps.js/#map-initialization-methods) matches the map ID when it was created. If they don't match, the map can't be properly reloaded when Sprig updates the component.

```twig
{# When the map is created #}
{% set mapOptions = {
    'id': 'my-sprig-map'
} %}

{# When the map is reloaded by Sprig #}
{% if sprig.isRequest %}
    <script>
        googleMaps.init('my-sprig-map');
    </script>
{% endif %}
```

Alternatively, if you call `googleMaps.init()` with no parameters, _all_ maps will be initialized.

### Optional callback on `googleMaps.init`

The `init` method also allows for an optional [callback method](/javascript/googlemaps.js/#init-mapid-null-callback-null), if needed:

```twig
{% if sprig.isRequest %}
    <script>
        googleMaps.init('my-sprig-map', function () {
            console.log("The map has finished loading!");
        });
    </script>
{% endif %}
```

### Autocomplete `target` input

It's possible to use **Google Places Autocomplete** to provide an enhanced `target` input field. This gives users a dynamic list of possible matches while they type their search target. The user can then simply select the matching location from a list of potential matches.

:::tip Enabling the Places API
For more information, see the instructions for [using the Places API](/address-field/front-end-form/#using-the-places-api) in a front-end form.

While a proximity search is not identical to a front-end form, the Autocomplete implementation will be very similar for both cases.
:::

If you choose to enhance your Sprig form in this way, consider using the **latitude & longitude** (which can be passed via hidden fields) as the [target](/proximity-search/options/#target) of your proximity search.

1. Get coordinates from Places API results.
2. Store the **latitude** and **longitude** via hidden input fields.
3. Use those [coordinates](/models/coordinates/) as the `target` of your [proximity search](/proximity-search/options/#target).

This saves the effort of querying Google (again) based on a text string. Since we already know the coordinates, we can just use them directly. Using known coordinates will improve search accuracy and reduce the number of calls to the Google API.
