# Setting Marker Icons

## Set a default marker icon

By default, you can set all markers to use the same icon when creating a map. Within the [dynamic map options](/maps/dynamic/#options), you can define the value of `markerOptions.icon`.

**Simple Example:**

```twig
{{ googleMaps.dynamic(locations, {
    markerOptions: {
        icon: 'path/to/icon.png'
    }
}) }}
```

The value of `icon` can be anything allowed as the `icon` property of the Google Maps API [MarkerOptions](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon) interface.

<img :src="$withBase('/images/guides/icon.png')" alt="Screenshot of the Google Maps documentation featuring the definition of icon">

**Complex Example:**

```twig
{{ googleMaps.dynamic(locations, {
    markerOptions: {
        icon: {
            url: 'path/to/icon.png',
            scaledSize: 'new google.maps.Size(32,32)'
        }
    }
}) }}
```

::: warning COMPLEX JS OBJECTS
Any string containing `google.maps` is considered a complex JavaScript object.

Learn more about [Complex JS in Twig...](/guides/complex-js-in-twig/)
:::

## Change a marker icon

For various reasons, you may need to adjust the icon of an existing marker. It's possible to do this in either Twig or JavaScript. The function is effectively the same for both.

**Example in Twig:**

```twig
{% do googleMaps.setMarkerIcon(mapId, markerId, icon) %}
```

**Example in JavaScript:**

```js
googleMapsPlugin.setMarkerIcon(mapId, markerId, icon);
```

### .setMarkerIcon(mapId, markerId, icon)

 - `mapId` as specified by [MAP-ID](/javascript-object/google-maps-objects/#map-objects)
 - `markerId` as specified by [MARKER-ID](/javascript-object/google-maps-objects/#marker-objects)
 - `icon` value is an **icon** as specified in the Google Maps API [MarkerOptions](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon) interface.

::: warning ENSURE MAP HAS LOADED
If you are doing this via JavaScript, make sure the map has had a chance to finish loading.
:::

## A Common Example

Let's say, hypothetically, you need to have different markers represented by different icons on the map. Using the tools outlined above, it's fairly straightforward to apply different icons to different markers, based on your custom criteria.

**Set marker icons based on their entry type:**

```twig
{# Set map ID #}
{% set mapId = 'gm-map-1' %}

{# Get all bars, restaurants, and coffee shops #}
{% set entries = craft.entries.section(['bars','restaurants','coffeeShops']).all() %}

{# Loop through all locations #}
{% for entry in entries %}

    {# Set marker ID #}
    {% set markerId = "#{entry.id}.myAddressField" %}
    
    {# Set icon based on the entry type #}
    {% switch entry.type.handle %}
        {% case 'bars' %}
            {% set icon = 'path/to/bar-icon.png' %}
        {% case 'restaurants' %}
            {% set icon = 'path/to/restaurant-icon.png' %}
        {% case 'coffeeShops' %}
            {% set icon = 'path/to/coffee-shop-icon.png' %}
    {% endswitch %}

    {# Assign the icon to the marker #}
    {% do googleMaps.setMarkerIcon(mapId, markerId, icon) %}

{% endfor %}

{# Generate the map #}
{{ googleMaps.dynamic(entries) }}
```

Likewise, you could work out a similar solution using the [JavaScript version](/javascript-object/#setmarkericon-mapid-markerid-icon) of `.setMarkerIcon()`. If you approach this using JavaScript, remember to run the `.setMarkerIcon()` method _after_ the map has been fully loaded.
