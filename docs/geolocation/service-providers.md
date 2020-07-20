# Service Providers



It's possible to use **IP address detection and geolocation** in an attempt to pinpoint the location of your users. The plugin will determine each user's IP address, then use a 3rd-party geolocation service (of your choosing) to perform the geolocation lookup.

:::warning PRECISION NOT GUARANTEED
Keep in mind, the accuracy of IP-based geolocation is highly variable... it's very normal for a user's IP geolocation to report the location of their local _internet service provider_, instead of the user's exact location. Do not be suprised if a user's reported location is several miles/kilometers from their actual location.
:::

There are several companies which provide geolocation services. You can set which service your website is using by visiting [Settings > Google Maps](/settings/#visitor-geolocation) in your control panel.

<img class="dropshadow" :src="$withBase('/images/settings/visitor-geolocation.png')" alt="Screenshot of geolocation service provider options">

## None

If you are not using geolocation, simply select "None". This will be the default value when the plugin is first installed.

## ipstack

The quickest and easiest way to add visitor geolocation to your site. [ipstack](https://ipstack.com/) provides solid and consistent geolocation results. This is the **recommended** solution, because it is free and easy to use.

## MaxMind

Another option is to use the [MaxMind](https://www.maxmind.com/) geolocation service. It's a bit more complicated to set up, with a recurring monthly cost associated. MaxMind is a slightly older, enterprise-level solution.

<hr>

:::tip
If you choose to enable visitor geolocation, both **ipstack** and **MaxMind** are equally solid options. Both services return reliable, high-quality data.
:::
