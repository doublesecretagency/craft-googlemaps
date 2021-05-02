---
description:
---

# Models

## Geocoding & Proximity Searching

| Model                                 | Overview
|:--------------------------------------|:---------
| [Lookup Model](/models/lookup-model/) | Core of a geocoding address lookup.

To perform [geocoding](/geocoding/), a **Lookup Model** must be created. This often occurs internally when triggering a [proximity search](/proximity-search/). The model is responsible for pinging the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/start) based on the specified `target` value.


## Addresses & Visitor Geolocation

| Model                                     | Overview
|:------------------------------------------|:---------
| [Address Model](/models/address-model/)   | Used in many places, including [Address fields](/address-field/).
| [Location Model](/models/location-model/) | Parent model of the **Address Model** and **Visitor Model**.
| [Visitor Model](/models/visitor-model/)   | Returned as results of a [visitor geolocation](/geolocation/) lookup.

You will frequently encounter an **Address Model**, which is an extension of the **Location Model**. When working with an Address, it's possible to use the properties and methods of both models.

Similarly, the **Visitor Model** is also an extension of the **Location Model**. You can access the properties and methods of both when fetching geolocation results.

## Dynamic & Static Maps

| Model                                           | Overview
|:------------------------------------------------|:---------
| [Dynamic Map Model](/models/dynamic-map-model/) | Handles creation of [dynamic maps](/dynamic-maps/).
| [Static Map Model](/models/static-map-model/)   | Handles creation of [static maps](/static-maps/).

Both the **Dynamic Map Model** and **Static Map Model** are powerful [chainable objects](/dynamic-maps/chaining/) which can be configured to display each map as desired.

## Geolocation Services

<div class="custom-block warning">
    <p>⚠️&nbsp; You will almost never need to call these models directly.</p>
</div>

| Model                                   | Overview
|:----------------------------------------|:---------
| [Ipstack Model](/models/ipstack-model/) | Connects to the [ipstack](/geolocation/service-providers/#ipstack) geolocation service.
| [Maxmind Model](/models/maxmind-model/) | Connects to the [MaxMind](/geolocation/service-providers/#maxmind) geolocation service.

 The **Ipstack Model** and **Maxmind Model** are responsible for handling [visitor geolocation](/geolocation/). Only one of these models will be used, depending on which [geolocation service](/geolocation/service-providers/) you have selected.

## Plugin Settings

<div class="custom-block warning">
    <p>⚠️&nbsp; You will almost never need to call this model directly.</p>
</div>

| Model                                     | Overview
|:------------------------------------------|:---------
| [Settings Model](/models/settings-model/) | Handles internal plugin settings.

The **Settings Model** handles the internal settings for the entire Google Maps plugin. It is a standard component of many Craft plugins.

---
---
---

# Pseudo-Models

Not quite a Model, **Coordinates** defines a standard format for `coords` values used in various places around the plugin. It's also directly compatible with many facets of the Google Maps JavaScript API.

| Pseudo-Model                        | Overview
|:------------------------------------|:---------
| [Coordinates](/models/coordinates/) | Common configuration of latitude & longitude.
