# `googleMaps` in JavaScript

When working with [dynamic maps](/dynamic-maps/), there are two JavaScript files which are automatically loaded into the front-end (although this can be [disabled](/guides/required-js-assets/)) whenever a map is included on the page. The files will be copied, and loaded from the public `cpresources` folder.

```
/web/cpresources/{hash}/js/
   - dynamicmap.js
   - googlemaps.js
```

The `googlemaps.js` file is the [main entry point](/javascript/googlemaps.js/). It allows you to create a new map, load an existing map, or initialize one or more map containers. The globally-accessible `googleMaps` JavaScript object will be automatically preloaded by this file.

The `dynamicmap.js` file contains a [`DynamicMap` JavaScript model](/javascript/dynamicmap.js/), which is used to generate individual `DynamicMap` objects for each map. Each one is a chainable instance of a fully functional Google Map.

:::warning Defer to googlemaps.js
You will virtually never need to interact with the `DynamicMap` model directly. Use the `googleMaps` object to create (or access) a `DynamicMap` model, then simply chain methods from within the `DynamicMap` model.
:::
