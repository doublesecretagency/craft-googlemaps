# Geolocation Event

This event is triggered when a visitor geolocation is performed.

| Property       | Type     | Description                                              |
|----------------|:--------:|----------------------------------------------------------|
| `service`      | _string_ | Which geolocation service was used. (ie: "MaxMind")      |
| `ip`           | _string_ | The visitor's detected IP address.                       |
| `results`      | _array_  | Raw geolocation lookup results.                          |
| `fromCache`    | _bool_   | Whether results were grabbed from cache, or fetched new. |
| `cacheExpires` | _int_    | Timestamp of when the cache is due to expire.            |

:::tip SAME FOR ALL SERVICES
The same event will be triggered, regardless of which 3rd party service performs the geolocation.
:::
