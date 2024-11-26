---
description:
---

# Importing Addresses

To import a collection of Addresses, use the free [Feed Me](https://plugins.craftcms.com/feed-me) plugin.

## Feed Me

When configuring the feed, simply assign each column to its respective [Address](/address-field/) subfield...

<img class="dropshadow" :src="$withBase('/images/guides/feed-me.png')" alt="Screenshot of Feed Me import" style="max-width:586px; margin-top:16px;">

## Bulk Geocoding

At the moment, this plugin does not support automatic [geocoding](/geocoding/) during the import process. If you are importing address data that **does not already contain valid coordinates**, you may want to generate the coordinates before importing your data.

:::warning Bulk Geocode First
Before running the import, make sure you already have the latitude & longitude in your CSV. Otherwise, it will likely be a minor pain to generate each set of Address coordinates later.
:::

## Geocoding from a CSV file

If conducting the import via a CSV file, we recommend running the data through an external bulk lookup service. Here are a few examples...

| Service | Analysis
|:--------|:---------
| [SmartyStreets](https://smartystreets.com) | Really nice data and easy to use, but expensive.
| [GPS Visualizer](https://www.gpsvisualizer.com/geocoder/) | Not as nice, but it’s free.
| [Geocodio](https://www.geocod.io) | Nice and cheap.

Once you've generated coordinates for each address in the CSV file, you can then import the complete set of data into Craft using Feed Me.

## Geocoding from a Google Sheet

If you're running the import from Google Sheets, it's possible to do a bulk geocoding of that data before running the import.

### Before Geocoding

<img class="dropshadow" :src="$withBase('/images/guides/import-sheets-1.png')" alt="" style="margin-top:12px">

### After Geocoding

<img class="dropshadow" :src="$withBase('/images/guides/import-sheets-2.png')" alt="" style="margin-top:12px; margin-bottom:22px;">

---
---

### Instructions

1. Open your Google Sheet, and click the **"Tools"** menu option.

2. Select **"Script editor"** from the dropdown menu to open the editor in a separate tab.

<img class="dropshadow" :src="$withBase('/images/guides/import-sheets-3.png')" alt="" style="margin-bottom:8px">

3. Copy the sample code provided (see below), and paste it into the script editor. **Read through the code snippet, and make any necessary changes to align with your unique spreadsheet.**

<img class="dropshadow" :src="$withBase('/images/guides/import-sheets-4.png')" alt="" style="margin-top:8px">

4. Click the **"Save"** button.

5. Click the **"Run"** button.

6. Watch the results of your script in the **"Execution Log"**.

### Sample Code

Copy & paste this code into your script editor. **Make all necessary changes for full compatibility.**

```js
function geocode() {
    
  // Get the current Google Sheet
  const sheet = SpreadsheetApp.getActiveSheet();
   
  // Get the range and cell values
  const range = sheet.getDataRange();
  const cells = range.getValues();

  // Initialize columns (starting with labels)
  const latitudes  = [['Latitude']];
  const longitudes = [['Longitude']];
  const formatted  = [['Formatted']];
  const raw        = [['Raw']];
  
  // Loop over each row
  for (let i = 1; i < cells.length; i++) {

    // Blank coords by default
    let lat = lng = f = r = '';
    
    /**
     * You may need to adjust the column numbers below.
     * Here's how they line up in this example...
     * - 1 = Street Name
     * - 3 = City
     * - 5 = Zip Code
     */

    // If a street address exists
    if (cells[i][1]) {
      
      // Compile address string
      const address = `${cells[i][1]}, ${cells[i][3]}, ${cells[i][5]}`;

      // Log the target address
      console.log(address);

      // Perform geocode lookup
      const geocoder = Maps.newGeocoder().geocode(address);
      const res = geocoder.results[0];
  
      // If results were found, get coords
      if (res) {
        lat = res.geometry.location.lat;
        lng = res.geometry.location.lng;
        f   = res.formatted_address;
        r   = JSON.stringify(res);
      }

      // Slow it down so Google doesn't get mad
      if (5 == i) {
        Utilities.sleep(500);
      }

    }
   
    // Append to columns data
    latitudes.push([lat]);
    longitudes.push([lng]);
    formatted.push([f]);
    raw.push([r]);
  }

  /**
   * You may need to adjust the column letters below.
   * Be sure to assign empty columns for the results,
   * so that no existing data will be overwritten.
   */
   
  // Write data to each column
  sheet.getRange('H1').offset(0, 0, latitudes.length).setValues(latitudes);
  sheet.getRange('I1').offset(0, 0, longitudes.length).setValues(longitudes);
  sheet.getRange('J1').offset(0, 0, formatted.length).setValues(formatted);
  sheet.getRange('K1').offset(0, 0, raw.length).setValues(raw);
  
}
```

Once you've generated coordinates for each address in the Google Sheet, you can then import the complete set of data into Craft using Feed Me.
