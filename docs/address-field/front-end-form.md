---
description: If your site has a front-end form with an Address field, follow these instructions. You can easily implement an Address field on a typical Craft form.
---

# Front-End Form

## Basic input fields

If your website features a [front-end form](https://craftcms.com/knowledge-base/entry-form) for users to manage content, it's easy to include a simplified version of an Address field in that form.

Each of the Address subfields can be handled using a standard `text` input. Or you could use something like a dropdown (for `state` or `country`) or hidden fields (for `lat` & `lng`).

The following sample snippet leans heavily on Craft's native [`input` function](https://craftcms.com/docs/3.x/dev/functions.html#input), which renders a normal `<input>` HTML tag.

## Sample Address Field

The Address field consists of many subfields. Each subfield is a normal HTML `input`.

:::warning myAddressField
Remember, `myAddressField` is just a placeholder for your actual Address field handle!
:::

```twig
{#
 # Visible Subfields
 # (eg: `street1`, `city`, etc)
 #}
<label>Street Address</label>
{{ input('text',
    'fields[myAddressField][street1]',
    entry.myAddressField.street1 ?? null,
    {id: 'myAddressField-street1'}
) }}
<label>Apartment or Suite</label>
{{ input('text',
    'fields[myAddressField][street2]',
    entry.myAddressField.street2 ?? null,
    {id: 'myAddressField-street2'}
) }}
<label>City</label>
{{ input('text',
    'fields[myAddressField][city]',
    entry.myAddressField.city ?? null,
    {id: 'myAddressField-city'}
) }}
<label>State</label>
{{ input('text',
    'fields[myAddressField][state]',
    entry.myAddressField.state ?? null,
    {id: 'myAddressField-state'}
) }}
<label>Zip Code</label>
{{ input('text',
    'fields[myAddressField][zip]',
    entry.myAddressField.zip ?? null,
    {id: 'myAddressField-zip'}
) }}
<label>Country</label>
{{ input('text',
    'fields[myAddressField][country]',
    entry.myAddressField.country ?? null,
    {id: 'myAddressField-country'}
) }}
<label>Latitude</label>
{{ input('text',
    'fields[myAddressField][lat]',
    entry.myAddressField.lat ?? null,
    {id: 'myAddressField-lat'}
) }}
<label>Longitude</label>
{{ input('text',
    'fields[myAddressField][lng]',
    entry.myAddressField.lng ?? null,
    {id: 'myAddressField-lng'}
) }}

{#
 # Hidden Subfields
 # (eg: `formatted`, `raw`, etc)
 #}
{{ input('hidden',
    'fields[myAddressField][formatted]',
    entry.myAddressField.formatted ?? null,
    {id: 'myAddressField-formatted'}
) }}
{{ input('hidden',
    'fields[myAddressField][raw]',
    entry.myAddressField.raw ?? null,
    {id: 'myAddressField-raw'}
) }}
```

Of course, it's possible to swap out these HTML form field types. They do not all need to be regular `text` inputs. Some common examples might be...

 - Using a **dropdown menu** for the `state` and/or `country` fields.
 - Using **hidden fields** to hold the `lat` and `lng` values.

:::tip Coordinates are Critical
Assuming you want to be able to show this location [on a map](/dynamic-maps/), or would like it to appear in the results of a [proximity search](/proximity-search/), then it is **critical** to include the `lat` and `lng` fields.

If you don't want the coordinates to be editable, you can use `hidden` inputs instead.
:::

## Using the Places API

Once you have the form fields in place, you can activate the [Google Places Autocomplete](https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete).

For your convenience, we've provided a script that you can **copy & paste** to a folder in your public directory. When this JavaScript file is available on the front-end of your site, you can then link to it and enable Google Places autocompletion on your custom form.

### 1. Configure the HTML form inputs

We'll assume that your HTML form looks a lot like the [sample above](#sample-address-field). It's important that the IDs of each `input` align with the JavaScript code provided.

### 2. Download the `address-field.js` file 

Download this file, and place it in a public front-end directory.

 - [**Latest version on GitHub**](https://github.com/doublesecretagency/craft-googlemaps/blob/v4/src/resources/js/address-field.js)

:::tip This file belongs to you now
Once you have copied the `address-field.js` file locally, you are free to make any further adjustments as you deem necessary.
:::

### 3. Add & adjust this Twig snippet

Add the following lines to your Twig template, then adjust them accordingly...

```twig
{% do googleMaps.loadAssets({'libraries': 'places'}) %}
{% js 'path/to/address-field.js' %}
{% js 'window.addressField.activateSubfield("myAddressField-street1");' %}
```

In order, these three lines will:

1. **Load the Google Places API.**
2. **Load the new JS file that you just added.** Be sure to update the path accordingly.
3. **Activate the first subfield `input`.** Update the snippet to reflect the actual ID of your input.

And you're done! If all went smoothly, your subfield will now use the Google Places autocompletion.

:::tip One subfield only
You should only activate a single input (preferably the first subfield, typically `street1`).
:::

---
---

## Geocoding via AJAX

If you'd rather not use the Places API, you can still perform an [address lookup](/geocoding/) via AJAX. This may allow for greater flexibility, but will require greater JavaScript customization on your end.

:::warning AJAX agnostic
The Google Maps plugin is unopinionated about which JavaScript framework or approach you use. How you choose to return the data from your AJAX call into the form is entirely up to you.
:::

Depending on your usage, you will most likely want to ping either the `/coords` endpoint (for just coordinates) or the `/one` endpoint (for a single complete Address). Under rare circumstances, you may want to ping `/all` for a _collection_ of Addresses, then most likely let the user decide which Address is correct.

:::tip Further Reading
For more information, see the docs regarding [Geocoding via AJAX...](/geocoding/via-ajax/)
:::
