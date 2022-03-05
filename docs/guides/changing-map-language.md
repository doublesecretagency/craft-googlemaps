---
description: It's easy to change the language of your map by adding a few extra query parameters to the Google Maps API URL.
---

# Changing the Map Language

If you want to control the **language** and/or **region** of a map, you'll need additional [query parameters](https://developers.google.com/maps/documentation/javascript/localization#Language) appended to the URL of the Google Maps API.

## Default API URL

Internally, the API URL is generated **automatically**. You don't have direct access to it, and will rarely need to manage it directly.

```twig 
https://maps.googleapis.com/maps/api/js?key=[KEY]
```

The `key` parameter will always be applied automatically, there's no need to ever include it manually.

## Custom API URL

Sometimes you'll need to adjust the URL even further. For various reasons, you may decide to add some extra [query parameters](https://developers.google.com/maps/documentation/javascript/url-params) beyond what gets applied automatically.

For example, if you want the map to appear in Japanese (biased around the region of Japan), you would need to specify the `language` and `region` as part of the API URL.

```twig 
https://maps.googleapis.com/maps/api/js?key=[KEY]&language=ja&region=JP
```

Fortunately, it's easy to add query parameters via the `params` option of the [`tag` method](/dynamic-maps/twig-php-methods/#tag-options):

```twig
{{ googleMaps.map(locations).tag({
    'params': {
        'language': 'ja',
        'region': 'JP'
    }
}) }}
```

Adding these URL parameters forces the map to appear in Japanese:

<img class="dropshadow" :src="$withBase('/images/maps/japanese.png')" alt="Example of map in Japanese">

:::warning Official Google Reference
For more information about the `language` and `region` parameters, consult the official Google guide for [Localizing the Map](https://developers.google.com/maps/documentation/javascript/localization).
:::
