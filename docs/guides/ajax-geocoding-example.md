---
description:
---

# AJAX Geocoding Example

When working with address data on the front-end, you may find yourself needing to perform a geocoding lookup [via AJAX](/geocoding/via-ajax/). Here is a relatively straightforward example of how it works...

## Step 1: The Form

The first ingredient is your **normal HTML form**. If you're building an [entry form](/address-field/front-end-form/), it's very likely to contain some Twig tags as well.

:::code
```html
<div>
    <!-- A text input and button to trigger the lookup -->
    <input type="text" id="search" placeholder="Enter a location...">
    <input type="button" value="Perform Address Lookup" onclick="performLookup()">
</div>

<div>
    <!-- Fields to be populated by the lookup results -->
    <input type="text" id="lat" name="fields[myAddressField][lat]" placeholder="Latitude">
    <input type="text" id="lng" name="fields[myAddressField][lng]" placeholder="Longitude">
</div>
```
:::

## Step 2: The JavaScript

### CSRF token

First, make sure Craft is passing the internal CSRF token through to JavaScript. Note that this JS snippet is being included `at head`. This allows you to securely submit your AJAX requests.

:::code
```twig
{% js at head %}
    // Set CSRF token information from Craft
    window.csrfTokenName = '{{ craft.app.config.general.csrfTokenName }}';
    window.csrfTokenValue = '{{ craft.app.request.csrfToken }}';
{% endjs %}
```
:::

### Lookup Function

Finally, you'll need something which ties it all together. The JavaScript function below is responsible for (1) getting the user's lookup query, (2) triggering the AJAX request, then (3) populating the coordinates fields with their respective data. See the code comments below for additional details.

:::warning Framework Agnostic
These geocoding endpoints are **framework agnostic**. You can use any framework you'd like to submit the AJAX request. For this particular example, we are using jQuery.
:::

:::code
```js
// Basic JavaScript function to handle the lookup
function performLookup() {

    // Specify which endpoint to ping
    var endpoint = '/actions/google-maps/lookup/one';

    // Initialize data for the AJAX call
    var data = {};

    // Append CSRF Token to outgoing data
    data[window.csrfTokenName] = window.csrfTokenValue;

    // Specify target for lookup (in this case, as a string)
    data['target'] = $('#search').val();

    // Alternatively, you can use a complex array of parameters
    // data['target'] = {
    //     'address': $('#search').val(),
    //     'components': {
    //         'country': 'US',
    //         'administrative_area': 'California'
    //     }
    // };

    // AJAX call using jQuery
    $.post(endpoint, data, function (response) {

        // If unsuccessful, emit console warning and bail
        if (!response.success) {
            console.warn(`[GM] ${response.error}`);
            return;
        }

        /**
         * The format of `response.results` is determined by each endpoint:
         *  /all - Returns an array of Address Models
         *  /one - Returns a single Address Model
         *  /coords - Returns a set of coordinates
         */
        console.log(response.results);

        // In this case, we retrieved a single Address
        var address = response.results;

        // Set the input values of the coordinates fields
        $('#lat').val(address.lat);
        $('#lng').val(address.lng);

        // If you have other subfields, you could set those as well
        // $('#street1').val(address.street1);
        // $('#street2').val(address.street2);
        // $('#city').val(address.city);
        // $('#state').val(address.state);
        // $('#zip').val(address.zip);
        // $('#neighborhood').val(address.neighborhood);
        // $('#county').val(address.county);
        // $('#country').val(address.country);
        // $('#countryCode').val(address.countryCode);

    });

}
```
:::

## Important Notes

Based on the JavaScript snippet above, **you may have noticed the following things:**

- There are multiple ways to [specify the `target`](/geocoding/target/) of your lookup.
- The format of your `results` will vary based on [which endpoint](/geocoding/via-ajax/) you are using.
- You can use this technique to populate an entire Address field on a [front-end form](/address-field/front-end-form/).
