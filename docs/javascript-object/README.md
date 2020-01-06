# JavaScript Object

Under the hood, the Google Maps plugin is just a giant wrapper for the Google Maps API. The plugin is simply creating all of the necessary JavaScript objects for you, automatically.

Fortunately, those Google Maps JavaScript objects are still available to you after the map has been rendered! See all about the [Google Maps Objects...](/javascript-object/google-maps-objects/)

What's more, the `googleMapsPlugin` provides several helpful methods for you to navigate these maps, markers, and info windows.

## Data Arrays

### .maps

An array of rendered Map Objects. [See more...](/javascript-object/google-maps-objects/#map-objects)

### .markers

A nested array of rendered Marker Objects. [See more...](/javascript-object/google-maps-objects/#marker-objects)

### .infoWindows

A nested array of rendered Info Window Objects. [See more...](/javascript-object/google-maps-objects/#info-window-objects)

## Create & Delete Objects

::: warning "MAP-ID" AND "MARKER-ID"
For the following examples, `mapId` and `markerId` follow the pattern spelled out by `MAP-ID` and `MARKER-ID` on [this page...](/javascript-object/google-maps-objects/)
:::

### .createMap(mapId, options = {})

Create a new map. Specify the `mapId` (can be anything).

`options` can be an object in the form of a [MapOptions](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) interface.

 - **Returns:** A new [Map](https://developers.google.com/maps/documentation/javascript/reference/map#Map) JS object.

### .createMarker(mapId, markerId, options = {})

Create a new marker. Specify the `mapId` of where you want the marker to be placed, and the new `markerId` (can be anything).

`options` can be an object in the form of a [MarkerOptions](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) interface.

 - **Returns:** A new [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker) JS object.

### .createInfoWindow(mapId, markerId, options = {})

Create an new info window. Specify the `mapId` and `markerId` of which marker you'd like to attach it to.

`options` can be an object in the form of an [InfoWindowOptions](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) interface.

 - **Returns:** A new [Info Window](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindow) JS object. 

### .createKmlLayer(mapId, kmlUrl, options = {})

Create an new KML layer. Specify the `mapId` of which map you'd like to apply it to.

`kmlUrl` must be a string of a publicly accessible KML file. For more details, see the [KML Files](/guides/kml-files/#using-javascript) guide.

`options` can be an object in the form of an [KmlLayerOptions](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) interface.

 - **Returns:** A new [KML Layer](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayer) JS object.

### .deleteMarker(mapId, markerId)

Remove a marker from the map. The marker will be destroyed.

### .deleteInfoWindow(mapId, markerId)

Remove a info window from the map. The info window will be destroyed.

## Map & Marker Adjustments

### .refreshMap(mapId)

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved.

### .styleMap(mapId, styles = [])

Apply a set of styles to an existing map. For more details, see the [Styling a Map](/guides/styling-a-map/) guide.

### .setMarkerIcon(mapId, markerId, icon)

Set the icon of an existing marker. For more details, see the [Setting Marker Icons](/guides/setting-marker-icons/) guide.

### .zoomOnMarker(mapId, markerId, zoom)

Re-center map to the specified marker, and `zoom` to the specified level.
