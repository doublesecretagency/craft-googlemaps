# Updating from Smart Map

## Replacing the plugin

To replace Smart Map, simply [install the Google Maps plugin](/getting-started/).

**That's it!** When the Google Maps plugin is installed, it handles the entire migration automatically.

Once the migration has been completed, it will automatically uninstall the Smart Map plugin, since you no longer need it.

It is now safe to remove this line from your `composer.json` file:

```js
"doublesecretagency/craft-smartmap": "^3.x",
```

## Updating your Twig code

Some of your Twig code may need to be updated as well. While there are many similarities to the original Smart Map examples, several components will require adjustments.

:::tip Examples for Updating Twig
For complete instructions, read the [guide to updating from Smart Map to Google Maps](/updating-from-smart-map/).
:::
