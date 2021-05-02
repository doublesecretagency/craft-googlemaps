---
description:
---

# Service Providers

It's possible to use **IP address detection and geolocation** in an attempt to pinpoint the location of your users. The system will determine the IP address of each user, and run it through a 3rd-party geolocation service to perform the geolocation lookup.

:::warning Precision Not Guaranteed
Keep in mind, the accuracy of IP-based geolocation is highly variable... it's very normal for a user's IP geolocation to report the location of their local _internet service provider_, instead of the user's exact location. Don't be suprised if a user's reported location is several miles/kilometers from their actual location.
:::

There are multiple companies which can provide geolocation services. You can set which service your website is using by visiting [Settings > Google Maps](/getting-started/settings/#visitor-geolocation) in your control panel.

<img class="dropshadow" :src="$withBase('/images/settings/visitor-geolocation.png')" alt="Screenshot of geolocation service provider options">

## None

If you are not using geolocation, simply select "None". This will be the default value when the plugin is first installed.

## ipstack

The quickest and easiest way to add visitor geolocation to your site. [ipstack](https://ipstack.com/) provides solid and consistent geolocation results. This is the **recommended** solution, because it is free and easy to use.

## MaxMind

Another option is to use the [MaxMind](https://www.maxmind.com/) geolocation service. It's a bit more complicated to set up, with a recurring monthly cost associated. MaxMind is a slightly older, enterprise-level solution.

<hr>

:::tip Equal Quality Data
If you choose to enable visitor geolocation, both **ipstack** and **MaxMind** are equally solid options. Both services return reliable, high-quality data.

Our preference for ipstack is only because it is free and has a simpler user interface.
:::
