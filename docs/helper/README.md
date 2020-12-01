# `googleMaps` in Twig & PHP

Whether you are working in Twig or PHP, the **`GoogleMaps` helper class** exists to make your life a little easier. It includes wrapper methods for some of the most common use-cases you will encounter.

### When using it in Twig

As long as this plugin is installed and enabled, the `googleMaps` object will be globally available in Twig. Nothing else needs to be done to instantiate it.

### When using it in PHP

You must be sure to `use` the helper class in order to reference it in your own plugin or module.

```php
use doublesecretagency\googlemaps\helpers\GoogleMaps;
```

Throughout these docs, you will see references to `GoogleMaps` in PHP. Although it's rarely stated, in each instance you _must_ make sure to `use` the Helper class.

:::warning When should I rely on the helper methods?
**As much as possible.** If there is a helper method which directly addresses the problem that you are trying to solve, it is preferable to use the helper method rather than the underlying service method (or whatever the helper method may be wrapping). The goal of this is to provide consistency and uniformity across and within projects.

However, if your use case is _not_ covered by this collection of helper methods, then feel free to rely directly on the underlying service methods. It's unrealistic to expect the helper methods to cover 100% of use-cases, so do what you must in order to get the job done.
:::
