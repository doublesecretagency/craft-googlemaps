---
description: If your site has a front-end form with an Address field, follow these instructions. You can easily implement in Address field on a typical Craft form.
---

# Front-End Form

## Input fields

If your website features a [front-end form](https://craftcms.com/knowledge-base/entry-form) for users to manage content, it's easy to include a simplified version of an Address field in that form.

Functionally, each of the Address subfields can be handled using a standard `text` input. Or alternatively, you could use something like a dropdown field (for State or Country) or hidden fields (for Latitude & Longitude).

Craft includes an [`input` Twig function](https://craftcms.com/docs/3.x/dev/functions.html#input) which renders a normal `<input>` HTML tag.

```twig
<label>Street Address</label>
{{ input('text',
    'fields[myAddressField][street1]',
    entry.myAddressField.street1 ?? null
) }}

<label>Apartment or Suite</label>
{{ input('text',
    'fields[myAddressField][street2]',
    entry.myAddressField.street2 ?? null
) }}

<label>City</label>
{{ input('text',
    'fields[myAddressField][city]',
    entry.myAddressField.city ?? null
) }}

<label>State</label>
{{ input('text',
    'fields[myAddressField][state]',
    entry.myAddressField.state ?? null
) }}

<label>Zip Code</label>
{{ input('text',
    'fields[myAddressField][zip]',
    entry.myAddressField.zip ?? null
) }}

<label>Country</label>
{{ input('text',
    'fields[myAddressField][country]',
    entry.myAddressField.country ?? null
) }}

<label>Latitude</label>
{{ input('text',
    'fields[myAddressField][lat]',
    entry.myAddressField.lat ?? null
) }}

<label>Longitude</label>
{{ input('text',
    'fields[myAddressField][lng]',
    entry.myAddressField.lng ?? null
) }}
```

:::warning myAddressField
Remember, `myAddressField` is just a placeholder for your actual Address field handle!
:::

Of course, it's possible to swap out these HTML form field types. They do not all need to be regular `text` inputs. Some common examples might be...

 - Using a **dropdown menu** for the `state` and/or `country` fields.
 - Using **hidden fields** to hold the `lat` and `lng` values.

Assuming you want to be able to show this location on a map, or would like it to appear in a proximity search, then it is _critical_ to include `lat` and `lng` fields. If want to prevent the coordinates from being editable, you can use `hidden` inputs instead.

## Geocoding via AJAX

:::warning AJAX agnostic
The Google Maps plugin is unopinionated about which JavaScript framework or approach you use. How you choose to return the data from your AJAX call into the form is entirely up to you.
:::

You may want to populate the front-end Address fields dynamically using something a bit more complex. Perhaps you are doing an [address lookup](/geocoding/) on the front-end, and want to automatically fill in your form fields. Fortunately, the data returned from your lookup will fit nicely into the form fields shown above.

Depending on your usage, you will most likely want to ping either the `/coords` endpoint (for just coordinates) or the `/one` endpoint (for a single complete Address). Under rare circumstances, you may want to ping `/all` for a _collection_ of Addresses, then most likely let the user decide which Address is correct.

:::tip Further Reading
For more information, see the docs regarding [Geocoding via AJAX...](/geocoding/via-ajax/)
:::
