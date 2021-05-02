---
description: Follow these instructions to set up the Google Maps plugin for Craft CMS. This simple step-by-step guide shows how to get started.
---

# Getting Started

## Installation via Plugin Store

To install the Google Maps plugin via the plugin store, follow these steps:

1. In your site's control panel, visit the Plugin Store page. If you do not see a link to the Plugin Store, be sure you are working in an environment which [allows admin changes](https://craftcms.com/docs/3.x/config/config-settings.html#allowadminchanges).

2. Search for "Google Maps".

3. Install the plugin titled **Google Maps**.

<div style="
    display: flex;
    padding: 20px 23px 2px;
    border: 1px solid #e3e5e8;
    border-radius: 5px;
    box-sizing: border-box;
    position: relative;
    width: 360px;
    margin: 0 10px;
    font-size: 14px; margin-bottom:16px
">
    <div style="margin-right:20px">
        <img :src="$withBase('/images/icon.svg')" width="70" alt="">
    </div>
    <div>
        <strong style="font-size:17px">Google Maps</strong>
        <div style="font-size:15px; margin-top:9px;">Maps in minutes. Powered by the Google Maps API.</div>
        <p style="color:#8f98a3 !important; font-weight:normal;">$99</p>
    </div>
</div>

## Installation via Console Commands

To install the Google Maps plugin via the console, follow these steps:

1. Open your terminal and go to your Craft project:

```sh
cd /path/to/project
```

2. Then tell Composer to load the plugin:

```sh
composer require doublesecretagency/craft-googlemaps
```

3. Then tell Craft to install the plugin:

```sh
./craft plugin/install google-maps
```

:::warning Finish installing via Console or Settings page
Alternatively, you can visit the **Settings > Plugins** page to complete the installation.

If installed via the control panel, you'll be automatically redirected to configure the plugin.
:::
