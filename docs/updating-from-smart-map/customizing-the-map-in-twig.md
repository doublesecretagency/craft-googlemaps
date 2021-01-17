# 🔧 Customizing the map in Twig

<update-message/>

The underlying premise is the same between Smart Map and Google Maps... You want to specify a set of `options` as the second parameter when creating a map.

```twig
{# OLD #}
{{ craft.smartMap.map(locations, options) }}

{# NEW #}
{{ googleMaps.map(locations, options).tag() }}
```

With that in mind, it's worth noting that the list of available `options` has changed significantly. You will almost certainly need to refactor which [options](/models/dynamic-map-model/#construct-locations-options) are specified in your template.

Please take a closer look to see which items will need to be updated, added, or removed.

#### List of all changes to available `options`

| Option               |    | What Changed
|:---------------------|----|:----------
| `markerInfo`         | ➡️ | Renamed to `infoWindowTemplate`
| `js`                 | ⭐ | **ADDED!** (new in the Google Maps plugin)
| `styles`             | ⭐ | **ADDED!** (new in the Google Maps plugin)
| `callback`           | ⭐ | **ADDED!** (new in the Google Maps plugin)
| `mapOptions`         | ⭐ | **ADDED!** (new in the Google Maps plugin)
| `scrollwheel`        | ❌ | REMOVED (configure via `mapOptions` instead)
| `maptype`            | ❌ | REMOVED (configure via `mapOptions` instead)
| `scale`              | ❌ | REMOVED (configure via `mapOptions` instead)
| `id`                 | ✅ | No change
| `width`              | ✅ | No change
| `height`             | ✅ | No change
| `zoom`               | ✅ | No change
| `center`             | ✅ | No change
| `markerOptions`      | ✅ | No change
| `infoWindowOptions`  | ✅ | No change
| `field`              | ✅ | No change

Note that `mapOptions` has been extracted into its own subset of values. Any Google-related map options must be specified here, they can no longer be specified in the "root" of `options`.

We recommend consulting the [full list of available options](/dynamic-maps/map-management/#map-locations-options) to determine what needs to be updated within your code.

:::tip New Documentation
See the complete new [Dynamic Map Options](/models/dynamic-map-model/#construct-locations-options) documentation.
:::
