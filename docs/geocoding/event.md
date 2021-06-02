---
description: To perform an action after each address lookup, tap into the `EVENT_AFTER_GEOCODING`. Add this snippet to your module or plugin, and customize as needed.
---

# Geocoding Event

This event is triggered when a [geocoding address lookup](/geocoding/) is performed.

## Properties

| Property  | Type    | Description
|:----------|:-------:|:------------
| `target`  | _array_ | The original target used to perform the lookup.
| `results` | _array_ | The complete results returned by the lookup.

## Example

```php
use doublesecretagency\googlemaps\events\GeocodingEvent;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use yii\base\Event;

Event::on(
    GoogleMapsPlugin::class,
    GoogleMapsPlugin::EVENT_AFTER_GEOCODING,
    function (GeocodingEvent $event) {
    
        // Target used to generate geocoding query
        $event->target;
    
        // Results of geocoding address lookup
        $event->results;

    }
);
```
