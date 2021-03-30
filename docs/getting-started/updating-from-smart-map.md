# Updating from Smart Map

## Install the Google Maps plugin

To replace Smart Map, simply [install the Google Maps plugin](/getting-started/).

**That's it!** When the Google Maps plugin gets installed, it handles the entire migration automatically.

Once the migration has been completed, it will automatically uninstall the Smart Map plugin, since you no longer need it.

<!--
:::warning Update in All Environments
Before removing the `craft-smartmap` package from Composer, make sure you have updated the plugin in **all environments**! If possible, these should be handled as separate deployments.

Once all environments are safely running the new Google Maps plugin, _then_ it will be safe to remove this line from your `composer.json` file:

```js
    "doublesecretagency/craft-smartmap": "^3.x",
```
:::
-->

## Update your Twig code

Read the [complete instructions](/updating-from-smart-map/) for updating your corresponding Twig code.

## Transferring the license

There is no need to re-purchase a license if you are migrating from Smart Map. Simply track down your old Smart Map license, and paste it into the license key box for Google Maps.

### Where to find your old Smart Map key?

With any luck, it will still be available in the Craft control panel for you to copy & paste. If you can't immediately find your old Smart Map license, you may need to dig a little deeper.

Other places where you might find the old license:

1. The **old project config YAML files** which referenced Smart Map. Assuming you're using a git repo, you may need to go back a little to find it.
2. Any **old emails** from Pixel & Tonic or Double Secret Agency regarding your license. You would have received it from Pixel & Tonic if the license was purchased new on Craft 3, or from Double Secret Agency if the license was transferred from Craft 2.
