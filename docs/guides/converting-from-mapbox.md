---
description: If migrating from the Mapbox plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
meta:
  - property: og:type
    content: website
  - property: og:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/converting-from-mapbox/
  - property: og:title
    content: Converting from Mapbox | Google Maps plugin for Craft CMS
  - property: og:description
    content: If migrating from the Mapbox plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
  - property: og:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/convert-from-mapbox.png
  - property: twitter:card
    content: summary_large_image
  - property: twitter:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/converting-from-mapbox/
  - property: twitter:title
    content: Converting from Mapbox | Google Maps plugin for Craft CMS
  - property: twitter:description
    content: If migrating from the Mapbox plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
  - property: twitter:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/convert-from-mapbox.png
---

# Converting from Mapbox

## Importing Address Data

Converting from an "Address (Mapbox)" field to an "Address (Google Maps)" field is relatively straightforward... simply **switch the field type** on the field's settings page.

**Just change the field type!**

<img class="dropshadow" :src="$withBase('/images/guides/convert-from-mapbox.png')" alt="Screenshot of field type select being switched to Address (Google Maps)" width="210" style="margin-left:20px; margin-bottom:4px;">

After saving the field as an "Address (Google Maps)" field, all data associated with that field will automatically be imported into the Google Maps plugin.

### One Field at a Time

When you update a single field, only the data for that specific field will be migrated over. Each Address field will need to be converted individually.

:::tip Field Configuration Not Included
The field's **settings** will not be ported, only the field's existing **data** will be transferred over.

You may still want to configure the Google Maps field to your liking, it will not automatically reflect how you had it configured with Mapbox.
:::

### Deploying to Production

When deploying to a production environment, you'll most likely be using [Project Config](https://craftcms.com/docs/5.x/system/project-config.html) to keep all of your configuration settings in sync. Don't worry, there will be little (or no) action required on your part.

All relevant Address data will be converted **once your Project Config changes are applied**.
