# Creating a dynamic map in Twig

Simply and easily display a normal map.

```twig
{{ googleMaps.map(mapOptions, locations).html() }}
```

Make sure you include the `.html()` call at the end. That ensures that a `<div>` container will be rendered automatically.
