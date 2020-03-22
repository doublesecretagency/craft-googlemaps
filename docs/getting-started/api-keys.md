# Google Maps API keys

To use Google Maps, you'll need to generate two API keys. One key will serve calls from the server (ie: address lookups), and the other will serve calls from the browser (ie: rendering a map).

Here's how you'll go about creating both keys, and entering them into the plugin...

## Generating your API keys

[Instructions and screenshots of the Google interface]

## Selecting which services to authorize

You'll need to enable the following services within the Google interface...

- Maps JavaScript API
- Maps Static API
- Geocoding API

## Adding your API keys into Craft

Add your new API keys to the Settings page...

<img :src="$withBase('/images/getting-started/api-fields.png')" alt="Screenshot of Google Maps API keys as stored in the plugin's settings page">


## Testing your API keys

If you have any problems with your Google Maps API keys, you can check out the [Diagnostics tool](/getting-started/diagnostics.md#testing-your-google-maps-api-keys) to get it all sorted out.
