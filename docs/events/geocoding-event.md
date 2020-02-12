# Geocoding Event

This event is triggered when an address lookup is performed.

| Property       | Type     | Description                                              |
|----------------|:--------:|----------------------------------------------------------|
| `results`      | _array_  | Raw geolocation lookup results.                          |
| `fromCache`    | _bool_   | Whether results were grabbed from cache, or fetched new. |
| `cacheExpires` | _int_    | Timestamp of when the cache is due to expire.            |

:::tip ADJUST THE SEARCH RESULTS
This event allows the search results to be further manipulated via a custom module or 3rd party plugin.
:::
