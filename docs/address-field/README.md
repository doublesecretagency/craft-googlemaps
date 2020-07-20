# Address Field

When you create a new address field, you can add it to anything you want. You can add an address field to an Entry, User, Asset, or any other type of element (including custom elements)!

[A long, animated GIF showing the entire functionality of an Address field.]

<img class="dropshadow" :src="$withBase('/images/address-field/address.png')" alt="Example of Address Field">

<img class="dropshadow" :src="$withBase('/images/address-field/blueprint-address-field.png')" alt="Blueprint of Address Field">

## Search as you type

The Address field will perform an automatic lookup as you type, quickly narrowing down your search results to find the best match.

Only the **first** subfield will conduct a lookup. This is usually the "Street Address", but the autocomplete mechanism will be bound to whatever subfield appears first.

## Latitude & Longitude

Based on the field settings, the coordinates of each address will either:

 - Appear as editable fields
 - Appear as static data
 - Be hidden
 
See the [field settings](/address-field/settings/#display-coordinates) for more information.

## See it on a map

By checking the box, you can reveal the location on a map. You can zoom in or out, and even move the marker itself.

## Drag & Drop Pin

You can easily move the pin on the map. Moving the pin will update the related Latitude & Longitude, but won't otherwise affect the Address data.
