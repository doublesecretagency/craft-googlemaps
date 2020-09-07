# Models

## [Address Model](/models/address-model/)

The Address Model extends the Location Model, and adds data specific to a street address and/or the results of a Google Maps API lookup.

## [Visitor Model](/models/visitor-model/)

The Visitor Model extends the Location Model, and adds data specific to a geolocation lookup. The geolocation service used will be determined in the control panel.

## [Location Model](/models/location-model/)

The Location Model underpins both the Address Model and Visitor Model. This gives the system a stability and universal compatibility for most situations.

## [Lookup Model](/models/lookup-model/)

The Lookup Model is a conduit for the [Geocoding Service](/services/geocoding-service/) to talk to the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/start).

## [Settings Model](/models/settings-model/)

The Settings Model is primarily for internal use. You can generally ignore this model.

## [Dynamic Map Model](/models/dynamic-map-model/)

The Dynamic Map Model is responsible for generating dynamic maps. Initializing it creates a chainable object.

## [Static Map Model](/models/static-map-model/)

The Static Map Model is responsible for generating static maps. Initializing it creates a chainable object.

# Pseudo-Models

## [Coordinates](/models/coordinates/)

Not quite a Model, Coordinates define a standard format for `coords` values used within the plugin. It's also compatible with many facets of the Google Maps JavaScript API.

## [JavaScript](/models/javascript/)

There is a JavaScript model which largely parallels the Dynamic Map Model. It can be used to easily create maps using JS, with a syntax that is similar to how a map would be created in Twig or PHP.
