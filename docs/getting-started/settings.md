# Settings Page

In order to access the settings, log in to your control panel and visit **Settings > Google Maps**. This is where you will be able to manage all API connection information regarding the Google API, as well as any credentials for whichever geolocation service you may be using. 

## Google API Keys

In order to use the Google API, access keys **are required**. You will need to ensure that the credentials are configured correctly, and have access to the correct Google API services.

Read more about [configuring the Google API keys...](/getting-started/api-keys/)

<img class="dropshadow" :src="$withBase('/images/settings/google-api-keys.png')" alt="Screenshot of Google API keys settings">

## Visitor Geolocation

Need to know where your visitors are coming from? You can use a third-party geolocation service to (roughly) pinpoint each visitor. Once you have the visitor geolocation results, you can use that data to better personalize a visitor's experience.

For more information, see the [Visitor Geolocation docs...](/geolocation/)

<img class="dropshadow" :src="$withBase('/images/settings/visitor-geolocation.png')" alt="Screenshot of geolocation service provider options">

## "Geolocation Service" options

If visitor geolocation is enabled, it will be necessary to enter the API credentials for the geolocation service of your choosing.

### `None`

**Default.** No IP-based geolocation will be performed.

### `ipstack`

 - **API Access Key** - Enter the unique authentication key used to gain access to the ipstack API.

<img class="dropshadow" :src="$withBase('/images/settings/ipstack-api-configuration-settings.png')" alt="Screenshot of Google API keys settings">

### `MaxMind`

 - **User ID** - Enter your user ID for calling the MaxMind API.
 - **License Key** - Enter your license key for calling the MaxMind API.
 - **GeoIP2 Service** - Select which GeoIP2 Precision Service has been subscribed to.

<img class="dropshadow" :src="$withBase('/images/settings/maxmind-api-configuration-settings.png')" alt="Screenshot of Google API keys settings">

:::warning List of Service Providers
For more information about the various services, take a look at the complete list of [Geolocation Service Providers](/geolocation/service-providers/).
:::

---
---

:::tip Internal Use Only
While you will never need to interact with it directly, the [Settings Model](/models/settings-model/) is the standard Craft model which manages the plugin settings internally.
:::
