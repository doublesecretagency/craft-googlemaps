# Getting Started


:::warning BETA Installation Instructions
**For users of the beta plugin,** please follow these instructions:

1. Create a folder in your project root called `beta`.
2. Unzip `googlemaps-beta.*.zip` into the new `beta` folder.

Your directory structure should now look like this:

```
    yourproject/beta/craft-googlemaps
``` 

3. Add this to your `composer.json` file:

```js
    "repositories": [
        {
            "type": "path",
            "url": "beta/*",
            "options": {
                "symlink": true
            }
        }
    ],
```

4. Follow the [Installation via Console Commands...](#installation-via-console-commands)
:::


## Installation via Plugin Store

To install the Google Maps plugin via the plugin store, follow these steps:

1. In your site's control panel, visit the Plugin Store page. If you do not see a link to the Plugin Store, be sure you are working in an environment which [allows admin changes](https://craftcms.com/docs/3.x/config/config-settings.html#allowadminchanges).

2. Search for "Google Maps".

3. Install the plugin titled **Google Maps**.

<img class="dropshadow" :src="$withBase('/images/getting-started/plugin-store.png')" alt="How the Google Maps plugin appears in the Plugin Store" style="max-width:345px; margin-bottom:10px;">

## Installation via Console Commands

To install the Google Maps plugin via the console, follow these steps:

1. Open your terminal and go to your Craft project:

```sh
cd /path/to/project
```

2. Then tell Composer to load the plugin:

```sh
composer require doublesecretagency/craft-googlemaps @dev
```

3. Then tell Craft to install the plugin:

```sh
./craft plugin/install google-maps
```

:::warning Finish installing via Console or Settings page
Alternatively, you can visit the **Settings > Plugins** page to complete the installation.

If installed via the control panel, you'll be automatically redirected to configure the plugin.
:::

## Troubleshooting

While constructing your maps, we highly recommend enabling the [`devMode` config setting](https://craftcms.com/docs/3.x/config/config-settings.html#devmode).

With `devMode` enabled, you will see a much more detailed output in the JavaScript console each time a map is rendered...

<img class="dropshadow" :src="$withBase('/images/getting-started/console.png')" alt="Example of console output when a map is rendered" style="max-width:772px">
