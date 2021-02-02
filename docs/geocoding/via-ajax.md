# Geocoding via AJAX

If your users need to perform an address lookup without loading a new page, you would of course use AJAX to handle that. The Google Maps plugin is not opinionated, how you perform the AJAX call is entirely up to you.

**Here is a basic example using jQuery:**

```js
// Specify which endpoint to ping
var endpoint = '/actions/google-maps/lookup/all';

// Set data for the AJAX call
var data = {
    'target': 'Los Angeles'
};

// Get CSRF token information from Craft
var csrfTokenName = '{{ craft.app.config.general.csrfTokenName }}';
var csrfTokenValue = '{{ craft.app.request.csrfToken }}';

// Append CSRF Token to outgoing data
data[csrfTokenName] = csrfTokenValue;

// AJAX call using jQuery
$.post(endpoint, data, function(response) {

    // If call was successful
    if (response.success) {
        
        /**
         * The format of `response.results` is determined by each endpoint:
         *  /all - Returns an array of Address Models
         *  /one - Returns a single Address Model
         *  /coords - Returns a set of coordinates
         */
        
        console.log(response.results);

    }
    
});

```

The `response` of the AJAX call will come in the following format:

```json
{
    success: bool,
    error: null|string,
    results: mixed // see below
}
```

The format of the `results` will depend on which endpoint you are using. If no results are found, the value will be `null`.

<div style="margin-bottom:30px"></div>

## `/all`

Returns an array of [Address Models](/models/address-model/), or `null` if nothing is found.

```js
'/actions/google-maps/lookup/all'
```

[_See full details of `all()`_](/models/lookup-model/#all)

## `/one`

Returns a single [Address Model](/models/address-model/), or `null` if nothing is found.

```js
'/actions/google-maps/lookup/one'
```

[_See full details of `one()`_](/models/lookup-model/#one)

## `/coords`

Returns a single set of coordinates, or `null` if nothing is found.

```js
'/actions/google-maps/lookup/coords'
```

[_See full details of `coords()`_](/models/lookup-model/#coords)
