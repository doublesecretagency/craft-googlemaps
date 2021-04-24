# Exporting Address Data

You can download existing Address data in **two different formats** using Craft's "Export" feature.

<img class="dropshadow" :src="$withBase('/images/address-field/export-options.png')" alt="Screenshot of options when exporting">

### When exporting "condensed" Addresses...

Each Address will show a compact string in a single column.

<img :src="$withBase('/images/address-field/export-condensed.png')" alt="Example of condensed export results">

### When exporting "expanded" Addresses...

Each Address will be split across multiple columns. Each subfield (and coordinates) will be separated into their own individual columns.

<img :src="$withBase('/images/address-field/export-expanded.png')" alt="Example of expanded export results">

:::tip Combining with other data
In order to combine Address data with other field data, create two separate exports:

 - Get the **Address data** using one of the "Addresses" exports.
 - Get the **field data** via a standard element export ("Raw data" or "Expanded").

After exporting both files separately, you will need to merge the data elsewhere with a separate tool.
:::
