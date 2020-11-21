# Settings Model

The Settings Model is a structural staple of most Craft plugins. It is generally not something you need to interact with directly.

If you want to manage the plugin's settings, go to **Settings > Google Maps** in the control panel. For more information, see the documentation regarding [the plugin settings page...](/getting-started/settings/)

::: warning FOR EDGE CASES ONLY
You will rarely need to call the Settings Model directly, it is for internal use only.

If you need to dynamically update the Google API Keys, take a look at the methods provided by the [API Service...](/services/api-service/)
:::

## Public Properties

### `serverKey`

_string_ - (Required) Google API Server Key.

### `browserKey`

_string_ - (Required) Google API Browser Key.

::: warning REQUIRED API KEYS
The Google API Server Key and Google API Browser Key are both **required**. Please make sure your setup includes valid API keys to access the Google services.
:::

### `geolocationService`

_string_ - Optional geolocation service. Can be **ipstack**, **maxmind**, or `null`.

::: tip 
The value specified here will determine the relevance of any settings related to a specific geolocation service. For example, if you are using the ipstack geolocation service, you will also need to include an ipstack API Access Key (see below).
:::

### `ipstackApiAccessKey`

_string_ - ipstack API Access Key. Only relevant if `geolocationService` is set to **ipstack**.

### `maxmindUserId`

_string_ - MaxMind User ID. Only relevant if `geolocationService` is set to **maxmind**.

### `maxmindLicenseKey`

_string_ - MaxMind License Key. Only relevant if `geolocationService` is set to **maxmind**.

### `maxmindService`

_string_ - MaxMind Service. Only relevant if `geolocationService` is set to **maxmind**.
