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
