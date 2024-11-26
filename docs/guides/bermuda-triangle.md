---
description: In other words, "You are lost." The point you were trying to plot on a map does not exist. You have somehow found yourself trapped in the Bermuda Triangle.
meta:
  - property: og:type
    content: website
  - property: og:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
  - property: og:title
    content: Bermuda Triangle | Google Maps plugin for Craft CMS
  - property: og:description
    content: In other words, "You are lost." The point you were trying to plot on a map does not exist. You have somehow found yourself trapped in the Bermuda Triangle.
  - property: og:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/bermuda-triangle.png
  - property: twitter:card
    content: summary_large_image
  - property: twitter:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
  - property: twitter:title
    content: Bermuda Triangle | Google Maps plugin for Craft CMS
  - property: twitter:description
    content: In other words, "You are lost." The point you were trying to plot on a map does not exist. You have somehow found yourself trapped in the Bermuda Triangle.
  - property: twitter:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/bermuda-triangle.png
---

# Bermuda Triangle

In other words, _"You are lost."_

<img class="dropshadow" :src="$withBase('/images/guides/bermuda-triangle.png')" alt="Screenshot of a Google Map displaying the Bermuda Triangle">

## Why does my map just say "Bermuda" in the middle of the ocean?

The point you were trying to plot on a map does not exist. You have somehow found yourself trapped in the [Bermuda Triangle](https://en.wikipedia.org/wiki/Bermuda_Triangle).

We use this fallback location as our **default coordinates**, in case we are unable to compile a set of coordinates via another method. If you have stumbled across this oddity, you'll need to dig through your code to figure out where the disconnect is.

## Fallback Coordinates

:::code
```js JSON
{
    "lat": 32.3113966,
    "lng": -64.7527469
}
```
:::
