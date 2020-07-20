# Google Maps API keys

To use Google Maps, you'll need to generate two API keys. One key will serve calls from the server (ie: address lookups), and the other will serve calls from the browser (ie: rendering a map).

Here's how you'll go about creating both keys, and entering them into the plugin...

## Generating your API keys

(INSTRUCTIONS)

(SCREENSHOTS OF THE GOOGLE INTERFACE)

## Selecting which services to authorize

You'll need to enable the following services within the Google interface...

- Maps JavaScript API
- Maps Static API
- Geocoding API

## Adding your API keys into Craft

To add your newly created Google API keys into the Google Maps plugin, go to the settings page by visiting [Settings > Google Maps](/settings/#google-api-keys) in your control panel...

<img class="dropshadow" :src="$withBase('/images/settings/google-api-keys.png')" alt="Screenshot of Google API keys settings">

<!--

@TODO: Create API key diagnostics tool

## Testing your API keys

If you have any problems with your Google Maps API keys, you can check out the [Diagnostics tool](/getting-started/diagnostics/#testing-your-google-maps-api-keys) to get it all sorted out.

-->
