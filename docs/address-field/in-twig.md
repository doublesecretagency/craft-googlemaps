# Using an Address in Twig

<img class="dropshadow" :src="$withBase('/images/address-field/basic-example.png')" alt="Example using the address of the Empire State Building">

The screenshot above is a very simple example of what is possible with the Address field. Each Address field will provide an [Address Model](/models/address-model/), which is extremely powerful and flexible.

Here is the Twig code which generated that screenshot...

```twig
{# Show the entry title #}
<h1>{{ entry.title }}</h1>

{# Get an Address Model from the Address field #}
{% set address = entry.address %}

{# Show the complete address with line breaks #}
<div class="full-address">
    {{ address.multiline() }}
</div>

{# Show links to the full map and directions #}
<div class="links-to-google">
    <a href="{{ address.linkToSearch() }}" target="_blank">Map</a> &bull;
    <a href="{{ address.linkToDirections() }}" target="_blank">Directions</a>
</div>

{# Show the location on a map #}
{{ googleMaps.map(address, {'zoom': 16}).tag() }}
```

## Breaking it down

Every Address field returns an **Address Model**. The example above is relying on several distinct features of the Address Model...

 - We used `multiline` to display a complete address, formatted across multiple lines.
 - We used `linkToSearch` and `linkToDirections` to display direct links to Google Maps.
 - We used `googleMaps.map` to show the location as a marker on a map.
 
To get a better understanding of the various methods available, take a look at the [Address Model](/models/address-model/) documentation.

To learn how to render a map, check out the [Dynamic Maps](/dynamic-maps/) documentation to see what is possible.
