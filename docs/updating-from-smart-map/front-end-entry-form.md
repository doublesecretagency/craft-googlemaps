# 🔧 Front-End Entry Form

<update-message/>

Technically, the syntax did not change at all. But since Craft itself has evolved, it's now possible to do something more like this...

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

:::tip New Documentation
See the complete new [Front-End Form](/address-field/front-end-form/) documentation.
:::
