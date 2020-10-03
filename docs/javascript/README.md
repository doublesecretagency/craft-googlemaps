# JavaScript

There are two JavaScript files which get automatically loaded into the front-end (though this can be disabled) whenever you add a map to a page. They will be dynamically loaded into the `cpresources` folder.

```
/cpresources/{hash}/js/
   - dynamicmap.js
   - googlemaps.js
```

The `googlemaps.js` file is the [main entry point](/javascript/googlemaps.js/). It allows you to create a new map, load an existing map, and initialize one or more maps. The globally-accessible `googleMaps` JavaScript object will be automatically preloaded by this file.

The `dynamicmap.js` file contains a [`DynamicMap` JavaScript model](/javascript/dynamicmap.js/), which is used to generate individual `DynamicMap` objects for each map. Each one is a chainable instance of a fully functional Google Map.

:::warning Defer to googleMaps.js
You will virtually never need to interact with the `DynamicMap` model directly. Use the `googleMaps` object to create (or access) a `DynamicMap` model, then simply chain methods from within the `DynamicMap` model.
:::
