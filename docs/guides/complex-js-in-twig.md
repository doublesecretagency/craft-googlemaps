# Complex JS in Twig

When working directly with JavaScript, you have full access to the `google.maps` JS object.

**Example of loading a custom marker icon via JavaScript:**

```js
var map = new google.maps.Map(document.getElementById('gm-map-1'));
var marker = new google.maps.Marker({
    icon: {
        url: 'path/to/icon.png',
        scaledSize: new google.maps.Size(32,32)
    }
});
```

However, that's tough to translate to Twig because of the `google.maps` object. It's not really possible to translate a JavaScript object directly into Twig. To work around this, simply pass any `google.maps` objects as a **string** when referencing them in Twig.

**Example of loading a custom marker icon via Twig:**

```twig
{{ googleMaps.dynamic(locations, {
    markerOptions: {
        icon: {
            url: 'path/to/icon.png',
            scaledSize: 'new google.maps.Size(32,32)'
        }
    }
}) }}
```

::: warning ENCLOSE THE ENTIRE DECLARATION
Note that the _entire JS object declaration_ is being wrapped in a string. The string as a whole will be parsed as literal JavaScript during runtime.
:::
