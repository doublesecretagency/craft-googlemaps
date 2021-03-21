# Address Field Settings

When you create a new Address field, you will see the following settings...

<img class="dropshadow" :src="$withBase('/images/address-field/address-settings.png')" alt="Screenshot of Address Field Settings">

These fields only apply to the **content author**, or whomever is viewing the field in the control panel. These settings do not have any impact on how the address data is used on the front-end of your site.

-----

## Preview

Pay close attention to this section, you will see all of your selections reflected here in real-time. When you change any of the related settings, those changes will appear here immediately.

This area is a snapshot of the final Address field. What you see here is a direct representation of how your content editors will see this field.

## Subfield Manager

You can use the subfield manager to finely customize all of the address component fields. With this tool, you can...

 - Change the text label of each subfield.
 - Change the width of each subfield.
 - Set whether each subfield is visible.
 - Rearrange the order of all subfields.

Feel free to move these subfields into whatever configuration you see fit!

## Show map on initial load?

This dropdown allows you to select how the map should appear when the edit page is initially loaded.

 - `Open` - The map will be open when the page is loaded.
 - `Closed` (default) - The map will be closed when the page is loaded.

## Show map when address search is triggered?

This dropdown allows you to select how the map should react when an address lookup is performed.

 - `Open` (default) - The map will automatically open when a lookup is performed.
 - `Close` - The map will automatically close when a lookup is performed.
 - `No Change` - The map will not react when a lookup is performed.

## Map Visibility Toggle

The map visibility toggle appears just above the address component subfields, in the upper-right corner of the field...

<img class="dropshadow" :src="$withBase('/images/address-field/map-visibility-toggle.png')" alt="Screenshot of the Map Visibility Toggle" width="327" height="177">

You can control how the toggle is displayed by selecting whether to show the text label, icon, both, or neither.

 - `Text & Icon` (default) - Show both the text label and icon.
 - `Text Only` - Show only the text label.
 - `Icon Only` - Show only the icon.
 - `Hidden` - Neither the text label nor icon are shown.

## Display Coordinates

The location coordinates appear directly below the address components subfields, and can be shown in one of three ways.

 - `Editable` - The coordinates and zoom fields are directly editable by the content editor.
 - `Read Only` (default) - The values of the coordinates and zoom fields will be visible, but not editable by the content author.
 - `Hidden` - The location coordinates and zoom will not be displayed at all. Note that while the coordinate fields are _visually_ hidden, the data will continue to be saved normally.

If you select `Hidden`, bear in mind that this does not prevent the coordinates or zoom from being saved in the database. The field will still save that data normally, as if the coordinates were actually visible. The coordinates and zoom fields will simply be invisible to the content editor.

When viewing the map, you can drag & drop the pin to alter the geographic coordinates (it will not change the address).

## Setting a Default Map Starting Position

When a brand new Address is being added to the system, it's possible for the map to have a default location. This means that you can set the _default center position_ of the map, along with the _default zoom level_ of the map.
