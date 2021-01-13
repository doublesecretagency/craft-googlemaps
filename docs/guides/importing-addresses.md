# Importing Addresses

To import a collection of Addresses, use the free [Feed Me](https://plugins.craftcms.com/feed-me) plugin.

## Feed Me

When configuring the feed, simply assign each column to its respective [Address](/address-field/) subfield...

<img class="dropshadow" :src="$withBase('/images/guides/feed-me.png')" alt="Screenshot of Feed Me import" style="max-width:586px; margin-top:16px;">

## Bulk Geocoding

At the moment, this plugin does not support automatic [geocoding](/geocoding/) during the import process. If you are importing address data that **does not already contain valid coordinates**, you may want to generate the coordinates before importing your data.

:::warning Bulk Geocode First
Before running the import, make sure you already have the latitude & longitude in your CSV. Otherwise, it will likely be a minor pain to generate each set of Address coordinates later.
:::

Assuming you are conducting the import via a CSV file, we recommend running the data through an external bulk lookup service. Here are a few examples...

| Service | Analysis
|:--------|:---------
| [SmartyStreets](https://smartystreets.com) | Really nice data and easy to use, but expensive.
| [GPS Visualizer](https://www.gpsvisualizer.com/geocoder/) | Not as nice, but it’s free.
| [Geocodio](https://www.geocod.io) | Nice and cheap.

Once you've generated coordinates for each address in the CSV file, you can then import the complete set of data into Craft.
