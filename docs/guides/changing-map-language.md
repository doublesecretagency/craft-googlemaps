# Changing the Map Language

If you want to manually specify the **language** and/or **region** of a map, you must append additional query parameters to the URL of the Google Maps API.

However, the API URL is typically called _automatically_. You don't have direct access to it, and will rarely need to manage it directly:

:::code
```twig Default API URL
https://maps.googleapis.com/maps/api/js?key=[KEY]
```
:::

But there are those times when you need to manually manipulate the API URL. For example, if you want the map to appear in Japanese (biased around the region of Japan), you would specify the `language` and `region` as part of the API URL...

:::code
```twig API URL with Language & Region Parameters
https://maps.googleapis.com/maps/api/js?key=[KEY]&language=ja&region=JP
```
:::

Fortunately, you can append query parameters via the [`api` option](/dynamic-maps/twig-php-methods/#tag-options) of the `tag` method...

```twig
{{ googleMaps.map(locations).tag({
    'api': {
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
