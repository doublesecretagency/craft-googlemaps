# Editing an Address

After you've added the field to your field layout (of any element type),

[Show gif or video of the Address field in use]

<img :src="$withBase('/images/address-field/blueprint-address-field.png')" alt="Blueprint of Address Field">

## Search as you type

The Address field will perform an automatic lookup as you type, quickly narrowing down your search results to find the best match.

Only the **first** subfield will conduct a lookup. This is usually the "Street Address", but the autocomplete mechanism will be bound to whatever subfield appears first.

## Latitude & Longitude

Based on the field settings, the coordinates of each address will either:

 - Appear as editable fields
 - Appear as static data
 - Be hidden
 
See the [field settings](/address-field/field-setting.md#show-coordinates-as) for more information.

## See it on a map

By checking the box, you can reveal the location on a map. You can zoom in or out, and even move the marker itself.

## Drag & Drop Pin

You can easily move the pin on the map. Moving the pin will update the related Latitude & Longitude, but won't otherwise affect the Address data.
