---
description: If you are uncertain about the health of your Google API keys, it may be necessary to test them.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/test-google-api-keys/
- property: og:title
  content: Test Google API Keys | Google Maps plugin for Craft CMS
- property: og:description
  content: If you are uncertain about the health of your Google API keys, it may be necessary to test them.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/test-google-api-keys.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/test-google-api-keys/
- property: twitter:title
  content: Test Google API Keys | Google Maps plugin for Craft CMS
- property: twitter:description
  content: If you are uncertain about the health of your Google API keys, it may be necessary to test them.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/test-google-api-keys.png
---

# Test Google API Keys

If you are uncertain about the health of your [Google API keys](/getting-started/api-keys/), it may be necessary to test them.

In your site's control panel, go to **Utilities > Test Google API Keys**.

<img class="dropshadow" :src="$withBase('/images/guides/test-google-api-keys.png')" alt="" width="642" style="margin-top:4px">

Each of the [required API services](/getting-started/api-keys/#authorize-required-services) will be tested with the appropriate key. If a service is not working, its status will include further details, and you can follow the "Enable" link to turn it on in your Google Cloud project.

If Address field autocompletion is still using the legacy Places API, the **Places API (New)** status will say so.

<img class="dropshadow" :src="$withBase('/images/guides/test-google-api-keys-legacy-places.png')" alt="" width="788" style="margin-top:4px">
