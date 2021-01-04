# Geolocation Event

This event is triggered when a [visitor geolocation](/geolocation/) is performed.

## Properties

| Property  | Type     | Description
|:----------|:--------:|:------------
| `service` | _string_ | Which geolocation service was used (`ipstack` or `MaxMind`).
| `ip`      | _string_ | The visitor's IP address (automatically detected by default).
| `visitor` | [Visitor](/models/visitor-model/) | Complete geolocation lookup results.

## Example

```php
use doublesecretagency\googlemaps\events\GeolocationEvent;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use yii\base\Event;

Event::on(
    GoogleMapsPlugin::class,
    GoogleMapsPlugin::EVENT_AFTER_GEOLOCATION,
    function (GeolocationEvent $event) {
    
        // Service used to detect the location
        $event->service;
    
        // Visitor's IP address
        $event->ip;
    
        // The resulting Visitor Model
        $event->visitor;

    }
);
```
