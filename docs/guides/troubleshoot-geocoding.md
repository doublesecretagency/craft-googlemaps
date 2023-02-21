---
description:
---

# Troubleshoot Proximity Search & Geocoding

:::warning Location can't be found?
Use the [Test Address Lookup](/guides/test-address-lookup/) utility to check that the Google Geocoding API is returning the expected results.
:::

## Search or geocoding is yielding no results

If you are certain that your [proximity search](/proximity-search/) or [address lookup](/geocoding/) should be returning results, and is instead returning nothing, it's possible that something is wrong with your **Google API keys**. Please check your keys thoroughly to ensure that they have the proper credentials, and that you have enabled all [required API services](/getting-started/api-keys/#authorize-required-services).

:::tip More Info
Check out the instructions for configuring your [Google Maps API Keys...](/getting-started/api-keys/)
:::

## Search or geocoding is focused on the wrong location

If your [proximity search](/proximity-search/) or [address lookup](/geocoding/) is centering on the wrong location, it's possible that Google is getting confused by that particular search query. To correct for this, you can force the API to hone in on a more specific geographical region.

:::tip More Info
Check out the guide about [Region Biasing...](/guides/region-biasing/)
:::
