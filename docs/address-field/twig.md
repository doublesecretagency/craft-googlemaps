---
description: Here's a basic example of how you might use an Address field in a Twig template. There's a lot more that's possible, we're just scratching the surface!
---

# Using an Address in Twig

## Example

This is a very simple example of what is possible with the Address field...

<img class="dropshadow" :src="$withBase('/images/address-field/basic-example.png')" alt="Example using the address of the Empire State Building">

And here is the Twig code which produced the screenshot above...

```twig
{# Show the entry title #}
<h1>{{ entry.title }}</h1>

{# Get an Address Model from the Address field #}
{% set address = entry.myAddressField %}

{# Show the complete address with line breaks #}
<div class="full-address">
    {{ address.multiline() }}
</div>

{# Show links to the full map and directions #}
<div class="links-to-google">
    <a href="{{ address.linkToMap() }}" target="_blank">Map</a> &bull;
    <a href="{{ address.linkToDirections() }}" target="_blank">Directions</a>
</div>

{# Show the location on a map #}
{{ googleMaps.map(address, {'zoom': 16}).tag() }}
```

## Breaking it down

Each Address field returns an [Address Model](/models/address-model/), which is extremely powerful and flexible. The example above is relying on several distinct features of the Address Model...

 - We used `multiline` to display a complete address, formatted across multiple lines.
 - We used `linkToMap` and `linkToDirections` to display direct links to Google Maps.
 - We used `googleMaps.map` to show the location as a marker on a map.
 
To get a better understanding of the various methods available, take a look at the [Address Model](/models/address-model/) documentation.

To learn how to render a map, check out the [Dynamic Maps](/dynamic-maps/) documentation to see what is possible.
