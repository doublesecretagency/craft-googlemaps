---
description:
---

# Visitor Geolocation

## Third-Party Services

If you'd like to use the visitor geolocation feature, you will also need to subscribe to a third-party service. See more about the available [geolocation service providers...](/geolocation/service-providers/)

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

## Error Handling

In rare instances when the geolocation fails entirely (ie: the third-party service is unreachable), you can check for errors by inspecting the `error` property of the returned [Visitor Model](/models/visitor-model/).

How you choose to handle a geolocation error is entirely up to you.

:::code
```twig
{% set visitor = googleMaps.getVisitor() %}

{% if visitor.error %}
    <p>There was an error performing the visitor geolocation: {{ visitor.error }}</p>
{% endif %}
```
```php
$visitor = GoogleMaps::getVisitor();

if ($visitor->error) {
    // Do something based on the error
}
```
:::
