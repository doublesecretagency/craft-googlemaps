# Google Maps Objects

When the map is created, there are several Google Maps objects which are created in JavaScript. After the map has been rendered on the page, you are free to continue manipulating it further using JavaScript.

The following arrays contain all of the maps, markers, and info window objects...

## Map Objects

Read more information about the Google Maps [Map Object...](https://developers.google.com/maps/documentation/javascript/reference/map#Map)

**Formatting Example:**

```js
googleMapsPlugin.maps['<MAP-ID>']
```

**Functional Example:**

```js
var map = googleMapsPlugin.maps['gm-map-1'];
map.setZoom(12);
```

The value of **MAP-ID** will be `gm-map-{#}` [by default](/maps/dynamic/#options). The numeric value is just a counter for the number of maps on the page. 

::: tip 
You can override the **MAP-ID** value by setting the `id` parameter when you [create the map](/maps/dynamic/).

```twig
{{ googleMaps.dynamic(locations, {
    id: 'my-custom-id'
}) }}
```

Remember, this value is used as the `id` attribute on the map's `<div>` container.
:::

## Marker Objects

Read more information about the Google Maps [Marker Object...](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker)

**Formatting Example:**

```js
googleMapsPlugin.markers['<MAP-ID>']['<MARKER-ID>']
```

**Functional Example:**

```js
var marker = googleMapsPlugin.markers['gm-map-1']['42.myAddressField'];
marker.setDraggable(true);
```

See the description of [**MAP-ID**](#map-objects) above.

The value of **MARKER-ID** will be in the format of `<ELEMENT-ID>.<FIELD-HANDLE>`.

The "element" is the parent element of the Address field. The "field" is the Address field which contained this location.

::: warning LD TODO
**// How does the MARKER-ID work for abstract Address Models added to the map?? (they would have no field or element)**
:::


## Info Window Objects

Read more information about the Google Maps [Info Window Object...](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindow)


**Formatting Example:**

```js
googleMapsPlugin.infoWindows['<MAP-ID>']['<MARKER-ID>']
```

**Functional Example:**

```js
var infoWindow = googleMapsPlugin.infoWindows['gm-map-1']['42.myAddressField'];
infoWindow.setContent('<h2>New Info</h2>');
```

See the descriptions of [**MAP-ID**](#map-objects) and [**MARKER-ID**](#marker-objects) above.
