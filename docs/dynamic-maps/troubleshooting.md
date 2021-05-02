---
description: To make troubleshooting a bit easier, enable devMode. Then watch your browser console for the full play-by-play log, each time a dynamic map is created.
---

# Troubleshooting

## Logging to the browser console

When a dynamic map is rendered on the page, the progress may be logged in the browser console...

<img class="dropshadow" :src="$withBase('/images/getting-started/console.png')" alt="Example of console output when a map is rendered" style="max-width:772px">

It's worth noting that this behavior only occurs when both of the following are true:

 - Craft's [`devMode`](https://craftcms.com/docs/3.x/config/config-settings.html#devmode) is **enabled**.
 - The plugin's [`enableJsLogging`](/getting-started/config/#enablejslogging) is **enabled**.

:::warning Enable devMode
With `devMode` enabled, you will see a much more detailed output in the JavaScript console each time a map is rendered. If you haven't already, we highly recommend enabling the [`devMode` config setting](https://craftcms.com/docs/3.x/config/config-settings.html#devmode) while building your maps.
:::

:::tip Disable logging entirely
If you don't want the logging to be visible at all (even with `devMode` enabled), you can use the [`enableJsLogging` config setting](/getting-started/config/#enablejslogging) to disable console logging entirely.
:::
