# Creating a dynamic map in PHP

```php
// Get map with basic configuration (no markers)
$map = GoogleMaps::map($mapOptions);

// Append markers using default `markerOptions` values
$map->markers($locations);

// Append markers using specific `markerOptions` override values
$map->markers($locations, $markerOptions);
```

```php
// Set all locations when you create a new map
$map = GoogleMaps::map($mapOptions, $locations);
```
