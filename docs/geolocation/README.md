# Visitor Geolocation

## Basic Usage

If you want to know where your visitors are coming from, you can perform a geolocation lookup based on each user's IP address...

:::code
```twig
{% set visitor = googleMaps.getVisitor() %}
```
```php
$visitor = GoogleMaps::getVisitor();
```
:::

It will return the geolocation results as a [Visitor Model](/models/visitor-model/). For the full details regarding the `getVisitor` method, see the [advanced geolocation](/geolocation/how-to-use/#advanced) documentation.

:::warning Accuracy
This method relies on deducing the location based on a user's **IP address**. Please be aware, this will rarely be 100% accurate. Generally speaking, you will end up with results that are within a few miles of a visitor's actual location.
:::

<!--
A more precise method of visitor geolocation can be done using the HTML 5 geolocation feature. However, this will prompt the user to give your site permission to know their location, and it's possible (and common) for them to decline.
-->

## Third-Party Services

If you'd like to use the visitor geolocation feature, you will also need to subscribe to a third-party service. See more about the available [geolocation service providers...](/geolocation/service-providers/)
