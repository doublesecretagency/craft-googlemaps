# Plugin Settings

In order to access the settings, log in to the control panel and visit **Settings > Google Maps**. This is where you will be able to manage all API connection information regarding the Google API, as well as any credentials for whichever geolocation service you may be using. 

## Google API Keys

In order to use the Google API, access keys **are required**. You will need to ensure that the credentials are configured correctly, and have access to the correct Google APIs. 

<img :src="$withBase('/images/getting-started/google-api-keys.png')" alt="Screenshot of Google API keys settings">

For more information about configuring the Google API keys, please [read more here...](/getting-started/api-keys/)

## Geolocation Services

All about the various services.

### None

If you selected "None", then there are no other settings to configure. IP-based geolocation will not be performed. This is the default setting.

### ipstack

<img :src="$withBase('/images/geolocation/ipstack-settings.png')" alt="Screenshot of Google API keys settings">

 - **API Access Key** - (paraphrase description from their website)

### MaxMind

<img :src="$withBase('/images/geolocation/maxmind-settings.png')" alt="Screenshot of Google API keys settings">

 - **GeoIP2 Service** - (paraphrase description from their website)
 - **User ID** - (paraphrase description from their website)
 - **License Key** - (paraphrase description from their website)

:::warning MORE INFORMATION
For more information about the various services, take a look at the complete list of [Geolocation Service Providers](/geolocation/service-providers/).
:::

## PHP Config File

You can also configure all of this using an environmentally-aware [PHP config file...](/config/)

:::tip SETTINGS MODEL
Internally, the plugin's settings are managed by the [Settings Model](/models/settings-model/). However, you will never need to interact with the Settings Model directly. Your settings should be handled exclusively via the control panel page, and/or a PHP config file.
:::
