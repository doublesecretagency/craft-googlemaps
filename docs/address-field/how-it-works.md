---
description: Let's break down what makes the Address field so cool. We've got several moving parts tied together, to bring you the most intuitive user experience.
---

# How It Works

If you saw the [animated GIF](/address-field/), then you've already had a glimpse of how dynamic the Address field is.

There are a few things going on simultaneously. Let's take a moment to break it down and point out each aspect of the field...

<img :src="$withBase('/images/address-field/annotated.png')" alt="Annotated screenshot of Address field components">

---
---


## Search as you type

When searching for a specific address, the Address field will perform an automatic lookup as you type. It quickly narrows down your search results to help you find the best possible match. Once you've identified the proper match, simply select it from the list.

Selecting an address will automatically populate each of the relevant subfields with that location's data. It will automatically set the values of both the **Latitude** and **Longitude** (even if they are hidden), as well as the current **Zoom** value.

:::warning First field only
Only the **first** subfield will conduct a lookup. This is often the "Name" or "Street Address", but the autocomplete mechanism will be bound to whichever subfield appears first.
:::

## Show or hide the map

It's possible to keep the map either open or closed. By default, the map will start in the closed position. When you conduct a search, the map will automatically open to reveal the newly found location.

You will find all of this to be fully configurable within the [field settings](/address-field/settings/).

## Dynamic coordinates subfields

The **Latitude** and **Longitude** fields will be automatically updated any time you select a new location. They are tied directly to the marker position, so moving the marker or conducting a search will immediately affect these subfield values. Even when the coordinates subfields are hidden, their respective values will be populated and stored normally.

The **Zoom** subfield only changes when you zoom in or out on the map.

## Drag & drop marker

If you want to adjust the marker positioning to a more precise location, simply click to drag & drop the marker itself! The coordinates subfields will be automatically updated to account for the new position.

:::tip Fully Customizable
Take a look at the [field settings](/address-field/settings/) page to see the many ways this field can be customized.
:::
