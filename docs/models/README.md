# Models

## [Address Model](/models/address-model/)

The Address Model extends the Location Model, and adds data specific to a street address and/or the results of a Google Maps API lookup.

## [Visitor Model](/models/visitor-model/)

The Visitor Model extends the Location Model, and adds data specific to a geolocation lookup. The geolocation service used will be determined in the control panel.

## [Location Model](/models/location-model/)

The Location Model underpins both the Address Model and Visitor Model. This gives the system a stability and universal compatibility for most situations.

## [Lookup Model](/models/lookup-model/)

The Lookup Model is a conduit for the [Geocoding Service](/services/geocoding-service/) to talk to the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/start).

# Pseudo-Model

## [Coordinates](/models/coordinates/)

Not quite a Model, Coordinates define a standard format for `coords` values used within the plugin. It's also compatible with many facets of the Google Maps JavaScript API.
