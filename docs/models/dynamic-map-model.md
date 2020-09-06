# Dynamic Map Model

The properties and methods of the Visitor Model are identical whether you are accessing them via Twig or PHP.

In all examples, `map` will be a Map Model.

## Public Methods

### `map(locations = [], options = [])`

:::code
```twig
{% set map = googleMaps.map(locations) %}
```
```php
$map = GoogleMaps::map($locations);
```
:::

#### Arguments

 - `$parameters` (_array_) - An optional configuration array.

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) containing the approximate location of a visitor.



```php

GoogleMaps::map(locations, options).markers(locations, options)

```
