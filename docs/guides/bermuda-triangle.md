# Bermuda Triangle

In other words, _"You are lost."_

<img :src="$withBase('/images/guides/bermuda-triangle.png')" alt="Screenshot of a Google Map displaying the Bermuda Triangle">

## Why does my map just say "Bermuda" in the middle of the ocean?

The point you were trying to plot on a map does not exist. You have somehow found yourself trapped in the [Bermuda Triangle](https://en.wikipedia.org/wiki/Bermuda_Triangle).

We use this fallback location as our **default coordinates**, in case we are unable to compile a set of coordinates via another method. If you have stumbled across this oddity, you'll need to dig through your code to figure out where the disconnect is.

## Fallback Coordinates

```json
{
    "lat": 32.3113966,
    "lng": -64.7527469
}
```
