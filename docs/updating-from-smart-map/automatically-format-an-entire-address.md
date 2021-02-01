# 🔧 Automatically format an entire address

<update-message/>

## Quickly render on a single line

Flattening an Address into a single-line string is just as easy as it used to be...

```twig
{# BOTH OLD & NEW - EXACT SAME SYNTAX! #}
{{ address }}
```

The internal formula has changed, however, so it may render a slightly different output now. Take a closer look at the [new formula](/models/address-model/#output-as-a-string) if you're curious.

## Multiple lines

It's now a bit more intuitive to render an Address across [multiple lines...](/models/address-model/#multiline-maxlines-3)

### One line

```twig
{# OLD #}
{{ address.format(true, true) }}

{# NEW #}
{{ address.multiline(1) }}
```

### Two lines

```twig
{# OLD #}
{{ address.format(true) }}

{# NEW #}
{{ address.multiline(2) }}
```

### Three lines

```twig
{# OLD #}
{{ address.format() }}

{# NEW #}
{{ address.multiline(3) }}
```

## NEW: `formatted` property

New in the Google Maps plugin, we are now keeping track of the [fully formatted address](/models/address-model/#formatted) retrieved during the original API lookup. This value is unfortunately not included in existing Smart Map data.

```twig
{# NEW #}
{{ address.formatted }}
```

:::tip New Documentation
See the complete new [Multiline vs. Formatted](/models/address-model/#multiline-vs-formatted) documentation.
:::

