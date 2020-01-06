# Geocoding (Address Lookups)

You will often need to perform a geocoding lookup to get the full address (including coordinates) based on a partial address or postal code. Indeed, the Google Maps plugin itself uses these lookup methods frequently under the hood.

![](/images/geocoding/flow-chart.png)

If you want to get the complete set of geocoding results from Google, you would use the [`all()`](/services/lookup-services/#all) method. The results will be sorted in order of what Google considers to be the best match.

Generally (but not always), it's safe to assume that Google hunch is correct, and the _first_ result is likely to be the correct match. In this case, you may feel comfortable using the [`one()`](/services/lookup-services/#one) method.

And if all you really need are the _coordinates_ of the _best possible match_, then it's probably safe to use the [`coords()`](/services/lookup-services/#coords) method.

## Lookup Services

If you want to see how it all operates under the hood, take a closer look at the [Lookup Services](/services/lookup-services/).
