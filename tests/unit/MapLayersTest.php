<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\models\DynamicMap;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the map layers which replaced `google.maps.KmlLayer` (issue #157).
 *
 * Google removed `KmlLayer` from Maps JS 3.66, and constructing one throws, which
 * took the whole map down. `kml()` now fetches the file in the browser, converts
 * it with the bundled togeojson library, and renders it through `google.maps.Data`.
 * The same engine powers `geoJson()` and `gpx()`, and `groundOverlay()`,
 * `dataset()` and `boundaries()` were added alongside.
 *
 * The PHP half is pure DNA, and `DynamicMap` builds without Craft, so the
 * segment shapes are asserted on real output. The JavaScript half is browser
 * behavior no PHPUnit test can see; those tests are structural guards on the
 * pieces whose removal would quietly break it. The live behavior was verified
 * with Playwright in Firefox, WebKit and Chromium (`.claude/plans/157-dry-run/`
 * in the sandbox).
 */
class MapLayersTest extends TestCase
{
    private string $dynamicmap;
    private string $googlemaps;
    private string $jsDir;

    protected function setUp(): void
    {
        $this->jsDir = dirname(__DIR__, 2) . '/src/resources/js';
        $this->dynamicmap = file_get_contents("{$this->jsDir}/dynamicmap.js");
        $this->googlemaps = file_get_contents("{$this->jsDir}/googlemaps.js");
    }

    /**
     * Get every DNA segment after the opening `map` segment.
     */
    private function _segments(DynamicMap $map): array
    {
        $dna = $map->getDna();
        $this->assertSame('map', $dna[0]['type']);
        return array_slice($dna, 1);
    }

    // ========================================================================= //
    // PHP: DNA segment shapes
    // ========================================================================= //

    public function testKmlSegmentKeepsItsShape(): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->kml('a.kml', ['id' => 'k', 'kmlLayerOptions' => ['preserveViewport' => true]]);

        $this->assertSame([[
            'type' => 'kml',
            'url' => 'a.kml',
            'options' => ['id' => 'k', 'kmlLayerOptions' => ['preserveViewport' => true]],
        ]], $this->_segments($map));
    }

    public function testGeoJsonAcceptsAUrl(): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->geoJson('/shapes.geojson', ['id' => 'g']);

        $this->assertSame([[
            'type' => 'geoJson',
            'source' => '/shapes.geojson',
            'options' => ['id' => 'g'],
        ]], $this->_segments($map));
    }

    public function testGeoJsonAcceptsInlineData(): void
    {
        // Twig users pass a field value straight through, so the array must survive intact
        $data = ['type' => 'FeatureCollection', 'features' => [['type' => 'Feature', 'geometry' => ['type' => 'Point', 'coordinates' => [1, 2]], 'properties' => []]]];

        $map = (new DynamicMap([], ['id' => 'm']))->geoJson($data);

        $this->assertSame($data, $this->_segments($map)[0]['source']);
    }

    public function testGpxSegment(): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->gpx('/track.gpx', ['id' => 't']);

        $this->assertSame([[
            'type' => 'gpx',
            'url' => '/track.gpx',
            'options' => ['id' => 't'],
        ]], $this->_segments($map));
    }

    public function testGroundOverlaySegment(): void
    {
        $bounds = ['north' => 1, 'south' => 0, 'east' => 1, 'west' => 0];

        $map = (new DynamicMap([], ['id' => 'm']))->groundOverlay('/plan.png', $bounds, ['opacity' => 0.5]);

        $this->assertSame([[
            'type' => 'groundOverlay',
            'url' => '/plan.png',
            'bounds' => $bounds,
            'options' => ['opacity' => 0.5],
        ]], $this->_segments($map));
    }

    public function testDatasetSegment(): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->dataset('3274c12c-7b68-4897-a32c-dc4e2450fff1', ['id' => 'd']);

        $this->assertSame([[
            'type' => 'dataset',
            'datasetId' => '3274c12c-7b68-4897-a32c-dc4e2450fff1',
            'options' => ['id' => 'd'],
        ]], $this->_segments($map));
    }

    public function testBoundariesSegment(): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->boundaries('LOCALITY', ['ChIJE9on3F3HwoAR9AhGJW_fL-I'], ['id' => 'la']);

        $this->assertSame([[
            'type' => 'boundaries',
            'featureType' => 'LOCALITY',
            'placeIds' => ['ChIJE9on3F3HwoAR9AhGJW_fL-I'],
            'options' => ['id' => 'la'],
        ]], $this->_segments($map));
    }

    /**
     * @dataProvider emptyInputProvider
     */
    public function testEmptyInputAddsNoSegment(callable $call): void
    {
        $map = new DynamicMap([], ['id' => 'm']);

        $this->assertSame($map, $call($map), 'An empty call must still return the map for chaining.');
        $this->assertSame([], $this->_segments($map));
    }

    public static function emptyInputProvider(): array
    {
        return [
            'kml' => [static fn(DynamicMap $m) => $m->kml('')],
            'geoJson string' => [static fn(DynamicMap $m) => $m->geoJson('')],
            'geoJson array' => [static fn(DynamicMap $m) => $m->geoJson([])],
            'gpx' => [static fn(DynamicMap $m) => $m->gpx('')],
            'groundOverlay url' => [static fn(DynamicMap $m) => $m->groundOverlay('', ['north' => 1])],
            'groundOverlay bounds' => [static fn(DynamicMap $m) => $m->groundOverlay('/a.png', [])],
            'dataset' => [static fn(DynamicMap $m) => $m->dataset('')],
            'boundaries type' => [static fn(DynamicMap $m) => $m->boundaries('', 'ChIJ')],
            'boundaries ids' => [static fn(DynamicMap $m) => $m->boundaries('LOCALITY', [])],
        ];
    }

    /**
     * @dataProvider mutatorProvider
     */
    public function testHideAndShowSegments(string $method, string $key): void
    {
        $map = (new DynamicMap([], ['id' => 'm']))->{$method}(['a', 'b']);

        $this->assertSame([['type' => $method, $key => ['a', 'b']]], $this->_segments($map));
    }

    /**
     * Each mutator, and the DNA key googlemaps.js reads it back from.
     */
    public static function mutatorProvider(): array
    {
        $pairs = [
            ['hideKml', 'kmlId'], ['showKml', 'kmlId'],
            ['hideGeoJson', 'geoJsonId'], ['showGeoJson', 'geoJsonId'],
            ['hideGpx', 'gpxId'], ['showGpx', 'gpxId'],
            ['hideGroundOverlay', 'overlayId'], ['showGroundOverlay', 'overlayId'],
            ['hideDataset', 'datasetId'], ['showDataset', 'datasetId'],
            ['hideBoundaries', 'boundariesId'], ['showBoundaries', 'boundariesId'],
        ];
        return array_combine(array_column($pairs, 0), $pairs);
    }

    /**
     * @dataProvider mutatorProvider
     */
    public function testJavaScriptReadsTheSameMutatorKey(string $method, string $key): void
    {
        $this->assertStringContainsString("map.{$method}(block.{$key});", $this->googlemaps);
    }

    /**
     * @dataProvider layerProvider
     */
    public function testJavaScriptUnpacksEachLayer(string $call): void
    {
        $this->assertStringContainsString($call, $this->googlemaps);
    }

    public static function layerProvider(): array
    {
        $calls = [
            'map.kml(block.url, block.options);',
            'map.geoJson(block.source, block.options);',
            'map.gpx(block.url, block.options);',
            'map.groundOverlay(block.url, block.bounds, block.options);',
            'map.dataset(block.datasetId, block.options);',
            'map.boundaries(block.featureType, block.placeIds, block.options);',
        ];
        return array_combine($calls, array_map(static fn(string $c): array => [$c], $calls));
    }

    // ========================================================================= //
    // JavaScript: the KmlLayer is gone for good
    // ========================================================================= //

    public function testKmlLayerIsNeverUsed(): void
    {
        // Constructing it throws on Maps JS 3.66+, taking the whole map down
        foreach (['dynamicmap.js', 'dynamicmap.min.js', 'googlemaps.js', 'googlemaps.min.js'] as $file) {
            $this->assertStringNotContainsString('KmlLayer', file_get_contents("{$this->jsDir}/{$file}"), "{$file} still references KmlLayer.");
        }
    }

    public function testBothOptionShapesReachTheEngine(): void
    {
        // The docs describe nested `kmlLayerOptions`, but top-level options always worked and sites use them
        $this->assertStringContainsString('options = Object.assign({}, options, options.kmlLayerOptions || {});', $this->dynamicmap);
    }

    public function testTheLayerExistsBeforeTheFileLoads(): void
    {
        // A `hideKml()` later in the same DNA chain must find a real layer, not a missing one
        $engine = $this->_slice('this._createDataLayer = function', 'this._dataLayerStyle = function');

        $created = strpos($engine, 'new google.maps.Data(');
        $loaded = strpos($engine, 'load().then(');
        $this->assertNotFalse($created);
        $this->assertNotFalse($loaded);
        $this->assertLessThan($loaded, $created);
        $this->assertStringContainsString('this[config.store][layerId] = data;', $engine);
    }

    public function testAutoFitWaitsForTheFirstRender(): void
    {
        // Fitting before the first render gets snapped back by the initial zoom and center (measured: 3 of 12 fitted)
        $fit = $this->_slice('this._fitLayer = function', "\n    };\n");

        $this->assertStringContainsString('if (this._map.getBounds()) {', $fit);
        $this->assertStringContainsString("google.maps.event.addListenerOnce(this._map, 'idle', fit);", $fit);
    }

    public function testFeaturesWithoutShapesAreSkipped(): void
    {
        // A KML <NetworkLink> becomes a feature with no geometry, which crashed the fit
        $this->assertStringContainsString("{'skipNullGeometry': true}", $this->dynamicmap);
        $this->assertStringContainsString('if (geometry) {', $this->_slice('this._extendBounds = function', 'this._fitLayer = function'));
    }

    public function testFitIncludesVisibleLayers(): void
    {
        $bounds = $this->_slice('this._determineBounds = function', 'this._extendBounds = function');

        $this->assertStringContainsString("['kml', 'geoJson', 'gpx', 'groundOverlay'].forEach(", $bounds);
        $this->assertStringContainsString('if (!layer || !layer.getMap()) {', $this->_slice('this._extendBounds = function', 'this._fitLayer = function'));
    }

    public function testLayerDescriptionsAreSanitized(): void
    {
        // A KML description is HTML from whoever made the file, possibly another site
        $popup = $this->_slice('this._openLayerInfoWindow = function', 'this._sanitizeHtml = function');

        $this->assertStringContainsString('heading.textContent = name;', $popup);
        $this->assertStringContainsString('body.innerHTML = this._sanitizeHtml(', $popup);
        $this->assertStringNotContainsString('setContent(`', $popup);
    }

    public function testBlockedRequestsMentionCors(): void
    {
        $this->assertStringContainsString('error.blocked = true;', $this->dynamicmap);
        $this->assertStringContainsString('cross-origin requests (CORS)', $this->dynamicmap);
    }

    public function testMapIdCheckWaitsForGoogle(): void
    {
        // At creation, Google reports data-driven styling as available even without a Map ID
        $check = $this->_slice('this._checkDataDrivenStyling = function', 'this._openLayerInfoWindow = function');

        $this->assertStringContainsString("addListenerOnce(map, 'mapcapabilities_changed', check);", $check);
        $this->assertStringContainsString('if (!this._mapId) {', $check);
    }

    public function testHiddenDatasetsLoseTheirStyle(): void
    {
        // Feature layers can't be detached; a null style is what hides them (verified in three engines)
        $this->assertStringContainsString('this._datasets[layerId].style = (entry.visible ? style : null);', $this->dynamicmap);
    }

    public function testCenterSurvivesTheGreyBoxCheck(): void
    {
        // Once `.center()` runs, getCenter() returns a LatLng, whose `lat` is a method, not a value.
        // `center.lat ?? center.lat()` took the method itself, so every such map landed on 0,0 (since 5.1.0).
        $guard = $this->_slice('this._preventGreyBox = function', "\n    };\n");

        $this->assertStringContainsString("('function' === typeof center.lat ? center.lat() : center.lat)", $guard);
        $this->assertStringContainsString("('function' === typeof center.lng ? center.lng() : center.lng)", $guard);
        $this->assertStringNotContainsString('center.lat ?? center.lat()', $guard);
    }

    // ========================================================================= //
    // Bundled libraries
    // ========================================================================= //

    /**
     * @dataProvider libraryProvider
     */
    public function testLibraryIsBundledAndPinned(string $file, string $global): void
    {
        $path = "{$this->jsDir}/lib/{$file}";

        $this->assertFileExists($path);
        $this->assertMatchesRegularExpression('/-\d+\.\d+\.\d+\./', $file, 'Bundled libraries carry their version in the file name.');
        $this->assertMatchesRegularExpression('/\'file\': \'' . preg_quote($file, '/') . '\',\s+\'global\': \'' . $global . '\'/', $this->googlemaps);
        $this->assertStringContainsString($global, file_get_contents($path));
    }

    public static function libraryProvider(): array
    {
        return [
            'togeojson' => ['togeojson-7.1.2.umd.js', 'toGeoJSON'],
            'jszip' => ['jszip-3.10.2.min.js', 'JSZip'],
        ];
    }

    public function testBundledLibrariesKeepTheirLicense(): void
    {
        $this->assertStringContainsString('BSD-2-Clause', file_get_contents("{$this->jsDir}/lib/togeojson-7.1.2.umd.js"));
        $this->assertStringContainsString('MIT license', file_get_contents("{$this->jsDir}/lib/jszip-3.10.2.min.js"));
    }

    public function testNoLibraryComesFromACdn(): void
    {
        // Sites with a strict Content Security Policy block third-party scripts
        foreach (['dynamicmap.js', 'googlemaps.js'] as $file) {
            $source = file_get_contents("{$this->jsDir}/{$file}");
            $this->assertStringNotContainsString('togeojson@', $source);
            $this->assertStringNotContainsString('jszip@', $source);
        }
    }

    public function testLibrariesLoadFromBesideThePluginScript(): void
    {
        $this->assertStringContainsString("new URL('lib/', document.currentScript.src)", $this->googlemaps);
        // Fallback for when currentScript is unavailable
        $this->assertStringContainsString('script[src*="/googlemaps.min.js"]', $this->googlemaps);
    }

    public function testMinifiedTwinsCarryTheEngine(): void
    {
        $min = file_get_contents("{$this->jsDir}/dynamicmap.min.js");
        foreach (['skipNullGeometry', 'mapcapabilities_changed', 'cross-origin requests (CORS)', 'groundoverlay'] as $token) {
            $this->assertStringContainsString($token, $min, "dynamicmap.min.js is missing `{$token}`. Regenerate it.");
        }

        $min = file_get_contents("{$this->jsDir}/googlemaps.min.js");
        foreach (['togeojson-7.1.2.umd.js', 'jszip-3.10.2.min.js', '_loadLibrary'] as $token) {
            $this->assertStringContainsString($token, $min, "googlemaps.min.js is missing `{$token}`. Regenerate it.");
        }
    }

    // ========================================================================= //

    /**
     * Slice dynamicmap.js from one marker to the next.
     */
    private function _slice(string $from, string $to): string
    {
        $start = strpos($this->dynamicmap, $from);
        $this->assertNotFalse($start, "Missing `{$from}` in dynamicmap.js.");
        $end = strpos($this->dynamicmap, $to, $start + strlen($from));
        $this->assertNotFalse($end, "Missing `{$to}` after `{$from}` in dynamicmap.js.");
        return substr($this->dynamicmap, $start, $end - $start);
    }
}
