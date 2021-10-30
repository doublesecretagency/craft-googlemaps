---
description:
---

# Troubleshoot Dynamic Maps

## The map isn't visible

If you see the words "Loading Map", _skip to the next answer._

If you **don't** see the words "Loading Map":

- Is the map height being set properly?
- Is the map being hidden due to CSS?

Check your DOM to see if the map container has been generated. If it has, then you're probably facing a simple CSS issue. By default, the height of a div will be zero... So even though the container is there, it isn't visible.

**Ways to fix it:**
 - Add a height value to your map options.
 - Set the height of `.gm-map` in your CSS.

:::tip More Info
Check out the guide about [Setting the Map Height...](/guides/setting-map-height/)
:::

## It says "Loading Map" instead of showing the map

Sounds like something went wrong with the JavaScript. Open up your browser's console and start troubleshooting.

- Did some unrelated JS fail, preventing the plugin from operating properly?
- Was the `maps.googleapis.com/maps/api/js` file referenced properly?
- Were the `googlemaps.js` and `dynamicmap.js` files loaded properly?

If the assets aren't being loaded properly, _see the next answer._

## Assets aren't being loaded properly

Caching can often lead to issues with missing assets. Be sure that the JS files you need are all being referenced properly.

It's possible to have a more granular control over how and when the necessary assets get loaded. They can be loaded manually or automatically, or the automatic loading can be suppressed.

:::tip More Info
Check out the guide about [Required JS Assets...](/guides/required-js-assets/)
:::
