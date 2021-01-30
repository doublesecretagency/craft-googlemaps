# Updating from Smart Map

## Replacing the plugin

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

## Updating your Twig code

Some of your Twig code may need to be updated as well. While there are many similarities to the original Smart Map examples, several components will require adjustments.

:::tip Examples for Updating Twig
For complete instructions, read the [guide to updating from Smart Map to Google Maps](/updating-from-smart-map/).
:::
