# 🔧 Render a map in Twig

<update-message/>

Generally speaking, the concepts of creating a map have remaining largely the same. The code required to generate a map is very similar to how it was in Smart Map. In the new Google Maps plugin, however, the resulting maps are far more flexible.

### Dynamic Maps

In the case of **dynamic maps**, you are now creating a [Dynamic Map Model](/models/dynamic-map-model/). You can manipulate that model as much as you'd like before eventually rendering it on the page (with the `.tag()` parameter).

#### Render a dynamic map `<div>` tag.

```twig
{# OLD #}
{{ craft.smartMap.map(locations, options) }}

{# NEW #}
{{ googleMaps.map(locations, options).tag() }}
```

:::warning Please re-evaluate your options! 
Several of the individual `options` have changed. You are highly encouraged to re-assess each of your existing options, to determine whether each option is still necessary, and/or whether it should now be nested within the `mapOptions` option.

See the complete list of [dynamic map options...](/dynamic-maps/map-management/#dynamic-map-options)
:::

:::tip New Documentation
See the complete new [Dynamic Maps](/dynamic-maps/) documentation.
:::

---
---

### Static Maps

In the case of **static maps**, you are now creating a [Static Map Model](/models/static-map-model/). You can manipulate that model as much as you'd like before eventually rendering it on the page. Append the `tag()` parameter to render a complete `<img>` tag, or `.src()` parameter to only get the `src` attribute.

#### Render a static map `<img>` tag.

```twig
{# OLD #}
{{ craft.smartMap.img(locations, options) }}

{# NEW #}
{{ googleMaps.img(locations, options).tag() }}
```

#### Render a static map `src` attribute value.

```twig
{# OLD #}
{{ craft.smartMap.imgSrc(locations, options) }}

{# NEW #}
{{ googleMaps.img(locations, options).src() }}
```

:::warning Please re-evaluate your options!
Several of the individual `options` have changed. You are highly encouraged to re-assess each of your existing options, to determine whether each option is still necessary.

See the complete list of [static map options...](/models/static-map-model/#static-map-options)
:::

:::tip New Documentation
See the complete new [Static Maps](/static-maps/) documentation.
:::
