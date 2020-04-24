# Creating a static map in Twig

Simply and easily display a static map.

**Fully rendered `<img>` tag**

```twig
{{ googleMaps.img(mapOptions, locations).html() }}
```

Make sure you include the `.html()` call at the end. That ensures that a `<div>` container will be rendered automatically.

**Just the `src` for the relevant image `<img>` tag**

```twig
{{ googleMaps.img(mapOptions, locations).src() }}
```
