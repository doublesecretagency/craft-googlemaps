# Geocoding Event

This event is triggered when a [geocoding address lookup](/geocoding/) is performed.

## Properties

| Property     | Type    | Description
|:-------------|:-------:|:------------
| `parameters` | _array_ | The original parameters used to perform the lookup.
| `results`    | _array_ | The complete results returned by the lookup.

## Example

```php
use doublesecretagency\googlemaps\events\GeocodingEvent;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use yii\base\Event;

Event::on(
    GoogleMapsPlugin::class,
    GoogleMapsPlugin::EVENT_AFTER_GEOCODING,
    function (GeocodingEvent $event) {
    
        // Parameters used to generate geocoding query
        $event->parameters;
    
        // Results of geocoding address lookup
        $event->results;

    }
);
```
