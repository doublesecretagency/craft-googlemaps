# Front-End Form

## Adding an Address field

If you'd like to use an Address field in a standard [front-end form](https://docs.craftcms.com/v3/dev/examples/entry-form.html), it's relatively straightforward...

```html
<label>Street Address</label>
<input type="text" name="fields[myAddressField][street1]">

<label>Apartment or Suite</label>
<input type="text" name="fields[myAddressField][street2]">

<label>City</label>
<input type="text" name="fields[myAddressField][city]">

<label>State</label>
<input type="text" name="fields[myAddressField][state]">

<label>Zip Code</label>
<input type="text" name="fields[myAddressField][zip]">

<label>Country</label>
<input type="text" name="fields[myAddressField][country]">

<label>Latitude</label>
<input type="text" name="fields[myAddressField][lat]">

<label>Longitude</label>
<input type="text" name="fields[myAddressField][lng]">
```

You can of course substitute other HTML field types to hold the subfield values. For instance, you could turn `[lat]` & `[lng]` into hidden fields, or use a select dropdown to chose a  `[state]` value.

## Prepopulating values in Twig

If you want to pre-populate an existing address in Twig, it would probably look something like this...

```html
<label>City</label>
<input type="text" name="fields[myAddressField][city]" value="{{ entry.myAddressField.city }}">
```

## Dynamically updating via JavaScript

You may want to populate the front-end Address fields dynamically using something a bit more complex. Perhaps you are doing an [address lookup](/geocoding/) on the front-end, and want to automatically fill in your form fields. Fortunately, the data returned from your lookup will fit nicely into the form fields shown above.

[See more info about Geocoding via AJAX...](/geocoding/via-ajax/)

Most likely, you will want to call the `/one` endpoint via AJAX. This will give you exactly one Address with which to update the form. You could potentially ping `/all`, and then somehow prompt the user to select which Address they want to use. Or if the only thing you want to update dynamically is the coordinates, just use the `/coords` endpoint.

::: tip 
The Google Maps plugin is unopinionated about which JavaScript framework or approach you use. How you choose to return the data from your AJAX call into the form is entirely up to you.
:::
