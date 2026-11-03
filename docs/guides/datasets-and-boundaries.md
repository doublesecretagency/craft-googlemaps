---
description: Shade countries, states, cities, or ZIP codes using Google's own boundaries. Or show a large set of shapes which has been uploaded to Google Cloud.
---

# Datasets & Boundaries

When you need to highlight an area on a map (a country, a state, a city, or a ZIP code), you don't need to draw its outline yourself. Google already knows where those **boundaries** are, and can shade them for you.

Similarly, if you have a large collection of shapes, you can upload it to Google Cloud as a **dataset**, then show it on any map.

:::warning Only Google's boundaries
The `boundaries` method can only shade areas which Google already knows about (countries, states, cities, and so on). To draw your own custom shapes, use [`kml`](/guides/kml-layers/) or [`geoJson`](/guides/geojson-layers/) instead.
:::

Both features require a little setup in Google Cloud first.

## Setting up a Map ID

Datasets and boundaries only work on a map which uses a **vector Map ID**. A Map ID is created in Google Cloud, in the same project as your [API keys](/getting-started/api-keys/).

:::tip Not the same as the map's "id"
A Google Map ID has nothing to do with the `id` option of a dynamic map. One is a setting in Google Cloud, the other is simply a name for your map.
:::

### 1. Create a Map ID

In the [Google Cloud Console](https://console.cloud.google.com/), go to **Google Maps Platform > Map Management** and click **Create map ID**.

 - Set the **Map type** to **JavaScript**.
 - Select **Vector**.

Once saved, copy the new Map ID. You'll need it in a moment.

### 2. Create a map style

Go to **Google Maps Platform > Map Styles** and click **Create style**. Choose the **Light** style (boundaries will only appear on a light map style), then save it.

On the page for your new style, open the **Feature layers** drop-down and add each type of boundary you plan to use.

Then connect the style to your Map ID. Go back to **Map Management**, open your Map ID, and under **Map styles** click **Edit**. Select your new style for **Light**, then save.

### 3. Upload a dataset (optional)

If you only need boundaries, you can skip this step.

Go to **Google Maps Platform > Datasets** and click **Create Dataset**. Upload your file (GeoJSON, KML, or CSV), then wait until its status reads **Completed**.

On the dataset's **Preview** tab, click **ADD MAP STYLE**, tick the style you created above, and click **SAVE**.

The Dataset ID can be found on the dataset's **Details** tab.

:::warning Uploaded to Google, not Craft
Datasets are uploaded and managed entirely in Google Cloud. The plugin only needs the Dataset ID in order to show it on a map.
:::

## Using your Map ID

Pass the Map ID into your map via `mapOptions`...

:::code
```js
const map = googleMaps.map([], {
    'center': {'lat': 39.8283, 'lng': -98.5795},
    'zoom': 4,
    'mapOptions': {
        'mapId': 'YOUR_MAP_ID'
    }
});
```
```twig
{% set map = googleMaps.map([], {
    'center': {'lat': 39.8283, 'lng': -98.5795},
    'zoom': 4,
    'mapOptions': {
        'mapId': 'YOUR_MAP_ID'
    }
}) %}
```
```php
$map = GoogleMaps::map([], [
    'center' => ['lat' => 39.8283, 'lng' => -98.5795],
    'zoom' => 4,
    'mapOptions' => [
        'mapId' => 'YOUR_MAP_ID'
    ]
]);
```
:::

Google can't tell the plugin how big a dataset or boundary is, so the map won't zoom to fit them. **Be sure to set a `center` and `zoom` for the map.**

:::warning Map IDs take over styling
Once a Map ID is set, Google will ignore any JSON styles applied via the [`styles`](/dynamic-maps/universal-methods/#styles-styleset) method or option. The map's appearance will be controlled by its map style in Google Cloud instead.
:::

## Boundaries

To shade one or more areas, specify the type of boundary and the Place ID of each area...

:::code
```js
map.boundaries(featureType, placeIds, options);
```
```twig
{% do map.boundaries(featureType, placeIds, options) %}
```
```php
$map->boundaries($featureType, $placeIds, $options);
```
:::

The `featureType` can be any of the following...

| Feature Type                  | Shades |
|-------------------------------|--------|
| `COUNTRY`                     | Countries |
| `ADMINISTRATIVE_AREA_LEVEL_1` | States (or provinces) |
| `ADMINISTRATIVE_AREA_LEVEL_2` | Counties |
| `LOCALITY`                    | Cities |
| `POSTAL_CODE`                 | ZIP codes |
| `SCHOOL_DISTRICT`             | School districts |

Each type must also be added to the **Feature layers** of your map style (see [step 2](#_2-create-a-map-style)). Google's coverage of each type varies by country.

The `placeIds` can be a single [Place ID](https://developers.google.com/maps/documentation/places/web-service/place-id), or an array of Place IDs. Each one must be the Place ID of the **area itself**, not of an address inside it.

Conveniently, you can get a Place ID by [geocoding](/geocoding/) the name of the area. The resulting [Address Model](/models/address-model/#placeid) will contain its `placeId`...

:::code
```twig
{# Get the Place ID of California #}
{% set california = googleMaps.lookup('California').one() %}

{# Shade California on the map #}
{% do map.boundaries('ADMINISTRATIVE_AREA_LEVEL_1', california.placeId, {
    'id': 'my-boundaries'
}) %}
```
```php
// Get the Place ID of California
$california = GoogleMaps::lookup('California')->one();

// Shade California on the map
$map->boundaries('ADMINISTRATIVE_AREA_LEVEL_1', $california->placeId, [
    'id' => 'my-boundaries'
]);
```
:::

### Boundary options

| Option  | Type     | Description |
|---------|----------|-------------|
| `id`    | _string_ | Reference point for the boundaries. Defaults to the `featureType`. |
| `style` | _object_ | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties. Defaults to a blue shade. |

The `style` accepts the following properties...

 - `fillColor` - Fill color.
 - `fillOpacity` - Fill opacity.
 - `strokeColor` - Line color.
 - `strokeOpacity` - Line opacity.
 - `strokeWeight` - Line width.
 - `pointRadius` - Size of each point (datasets only).

To shade several areas of the same type in different colors, add each group separately, and give each group its own `id`...

:::code
```js
map
    .boundaries('LOCALITY', chicagoPlaceId, {
        'id': 'chicago',
        'style': {fillColor: '#226666', fillOpacity: 0.4}
    })
    .boundaries('LOCALITY', seattlePlaceId, {
        'id': 'seattle',
        'style': {fillColor: '#D9480F', fillOpacity: 0.4}
    });
```
```twig
{% do map
    .boundaries('LOCALITY', chicagoPlaceId, {
        'id': 'chicago',
        'style': {'fillColor': '#226666', 'fillOpacity': 0.4}
    })
    .boundaries('LOCALITY', seattlePlaceId, {
        'id': 'seattle',
        'style': {'fillColor': '#D9480F', 'fillOpacity': 0.4}
    }) %}
```
```php
$map
    ->boundaries('LOCALITY', $chicagoPlaceId, [
        'id' => 'chicago',
        'style' => ['fillColor' => '#226666', 'fillOpacity' => 0.4]
    ])
    ->boundaries('LOCALITY', $seattlePlaceId, [
        'id' => 'seattle',
        'style' => ['fillColor' => '#D9480F', 'fillOpacity' => 0.4]
    ]);
```
:::

## Datasets

To show a dataset, simply specify its Dataset ID...

:::code
```js
map.dataset('YOUR_DATASET_ID', {'id': 'my-dataset'});
```
```twig
{% do map.dataset('YOUR_DATASET_ID', {'id': 'my-dataset'}) %}
```
```php
$map->dataset('YOUR_DATASET_ID', ['id' => 'my-dataset']);
```
:::

Clicking a shape will open an info window showing its `name` and `description` attributes, if the dataset has them.

### Dataset options

| Option                | Type     | Description |
|-----------------------|----------|-------------|
| `id`                  | _string_ | Reference point for the dataset. Defaults to the Dataset ID. |
| `style`               | _object_ | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties. Defaults to a blue shade. |
| `suppressInfoWindows` | _bool_   | If `true`, clicking a shape will not open an info window. |

## Hiding or showing

To dynamically hide or show a dataset or a set of boundaries, use these [universal methods](/dynamic-maps/universal-methods/#hidedataset-datasetid)...

:::code
```js
// Hide or show a dataset
map.hideDataset('my-dataset');
map.showDataset('my-dataset');

// Hide or show boundaries
map.hideBoundaries('my-boundaries');
map.showBoundaries('my-boundaries');
```
```twig
{# Hide or show a dataset #}
{% do map.hideDataset('my-dataset') %}
{% do map.showDataset('my-dataset') %}

{# Hide or show boundaries #}
{% do map.hideBoundaries('my-boundaries') %}
{% do map.showBoundaries('my-boundaries') %}
```
```php
// Hide or show a dataset
$map->hideDataset('my-dataset');
$map->showDataset('my-dataset');

// Hide or show boundaries
$map->hideBoundaries('my-boundaries');
$map->showBoundaries('my-boundaries');
```
:::

### Beyond hiding and showing

When you need to do more than hide or show them, you can get the raw [Google Feature Layer object](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer), and manipulate it further using Google's own API.

These methods are only available [in JavaScript](/dynamic-maps/javascript-methods/#getdataset-datasetid).

```js
// Get the raw Google Feature Layer objects
const dataset = map.getDataset('my-dataset');
const boundaries = map.getBoundaries('my-boundaries');
```

## Troubleshooting

If nothing appears, check the JavaScript console for a `[GM]` message...

 - **"Datasets and boundaries require a vector Map ID."** - No Map ID was passed into the map. See [Using your Map ID...](#using-your-map-id)
 - **"Map ID ... can't show datasets or boundaries."** - The Map ID exists, but it can't show them yet. Make sure it's a **vector** Map ID, and that its map style has the right **feature layers** turned on. See [Setting up a Map ID...](#setting-up-a-map-id)
