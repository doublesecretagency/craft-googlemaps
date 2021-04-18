# Changelog

## 4.0.6 - 2021-04-18

### Added
- Added the [subfield filter fallback](https://plugins.doublesecretagency.com/google-maps/guides/filter-by-subfields/#subfield-filter-fallback) mechanism for proximity searches. ([#5](https://github.com/doublesecretagency/craft-googlemaps/issues/5))

### Changed
- Improved error messaging for when a map ID conflicts with the ID of another DOM element. ([#23](https://github.com/doublesecretagency/craft-googlemaps/issues/23#issuecomment-816841754))

## 4.0.5 - 2021-03-27

### Added
- Added [`fieldControlSize`](https://plugins.doublesecretagency.com/google-maps/getting-started/config/#fieldcontrolsize) setting to control the UI size for Address fields. ([#19](https://github.com/doublesecretagency/craft-googlemaps/issues/19))
- Added [`fieldParams`](https://plugins.doublesecretagency.com/google-maps/getting-started/config/#fieldparams) setting to adjust the API URL for Address fields. ([#18](https://github.com/doublesecretagency/craft-googlemaps/pull/18))

## 4.0.4 - 2021-03-24

### Added
- Added full support for Matrix, Neo, and Super Table fields. ([#1](https://github.com/doublesecretagency/craft-googlemaps/issues/1))

## 4.0.3 - 2021-03-14

### Changed
- Improved IP autodetection for visitor geolocation.

### Fixed
- Fixed normalization bug when handling an Address Model. ([#6](https://github.com/doublesecretagency/craft-googlemaps/issues/6))
- Be more defensive against unavailable coordinates.

## 4.0.2 - 2021-02-28

### Added
- Added [`api`](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/twig-php-methods/#tag-options) option to Twig/PHP `tag` method.
- Added [`assets`](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/twig-php-methods/#tag-options) option to Twig/PHP `tag` method.
- Added [`callback`](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/twig-php-methods/#tag-options) option to Twig/PHP `tag` method.

## 4.0.1 - 2021-02-26

### Added
- Added [`getInfoWindow`](https://plugins.doublesecretagency.com/google-maps/dynamic-maps/javascript-methods/#getinfowindow-infowindowid) JavaScript method.

### Fixed
- Fixed overlap between the field handle div and the map's toggle text/icon. ([#7](https://github.com/doublesecretagency/craft-googlemaps/issues/7))

## 4.0.0 - 2021-02-22

### Added
- Everything.

**This is the direct successor to the Smart Map plugin.** Since the final version of Smart Map ended at 3.x, Google Maps begins with version 4.0. To replace Smart Map, simply install the Google Maps plugin. Your data will be migrated automatically.

For more information, please see the [complete documentation...](https://plugins.doublesecretagency.com/google-maps/)
