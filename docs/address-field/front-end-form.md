# Front-End Form

If your users can edit content via a [front-end form](https://craftcms.com/knowledge-base/entry-form), it's easy to add a simplified Address field.

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

:::tip DIY geocoding
It will be up to you to populate the `lat` and `lng` fields. We have plans to add an AJAX endpoint for this, but there is no ETA at this moment.
:::


<!-- TODO: Add controller endpoint to lookup coordinates -->

<!-- TODO: Support AJAX endpoints
## Dynamically updating via JavaScript

You may want to populate the front-end Address fields dynamically using something a bit more complex. Perhaps you are doing an [address lookup](/geocoding/) on the front-end, and want to automatically fill in your form fields. Fortunately, the data returned from your lookup will fit nicely into the form fields shown above.

[See more info about Geocoding via AJAX...](/geocoding/via-ajax/)

Most likely, you will want to call the `/one` endpoint via AJAX. This will give you exactly one Address with which to update the form. You could potentially ping `/all`, and then somehow prompt the user to select which Address they want to use. Or if the only thing you want to update dynamically is the coordinates, just use the `/coords` endpoint.

::: tip 
The Google Maps plugin is unopinionated about which JavaScript framework or approach you use. How you choose to return the data from your AJAX call into the form is entirely up to you.
:::
-->
