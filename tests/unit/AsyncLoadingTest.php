<?php
namespace doublesecretagency\googlemaps\tests\unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for loading the Maps API asynchronously (issue #117, and the #133 race).
 *
 * With `loading=async`, the whole Maps API can arrive after the window `load`
 * event (measured: Firefox every time, WebKit often), so `init` on `load` hit
 * `google.maps.Map is not a constructor` and async was reverted in 5.1.5. Now
 * async is an opt-in setting (`loadApiAsync`), `googleMaps.ready()` waits for
 * the real API, `init` defers only when it has to, and maps requested early get
 * a stand-in which records each call and replays it once the map exists.
 *
 * Everything that matters here is browser timing, which no PHPUnit test can
 * see. These are structural guards on the pieces whose removal would quietly
 * bring the race (or a regression for sites that never opted in) back. The live
 * behavior was verified with Playwright in Firefox, WebKit and Chromium, with
 * the setting on and off: `.claude/plans/117-dry-run/` in the sandbox.
 */
class AsyncLoadingTest extends TestCase
{
    private string $googlemaps;
    private string $dynamicmap;
    private string $helper;
    private string $apiHelper;

    protected function setUp(): void
    {
        $root = dirname(__DIR__, 2);
        $this->googlemaps = file_get_contents("{$root}/src/resources/js/googlemaps.js");
        $this->dynamicmap = file_get_contents("{$root}/src/resources/js/dynamicmap.js");
        $this->helper = file_get_contents("{$root}/src/helpers/GoogleMaps.php");
        $this->apiHelper = file_get_contents("{$root}/src/helpers/ApiHelper.php");
    }

    /**
     * Slice one `name: function` member out of googlemaps.js.
     */
    private function _member(string $name): string
    {
        $start = strpos($this->googlemaps, "    {$name}: function");
        $this->assertNotFalse($start, "Missing googleMaps.{$name}().");
        $end = strpos($this->googlemaps, "\n    },\n", $start);
        return substr($this->googlemaps, $start, $end - $start);
    }

    // ========================================================================= //
    // PHP: the URL
    // ========================================================================= //

    public function testAsyncIsOnlyAddedWhenTheSettingIsOn(): void
    {
        // Off by default in 5.x, so nobody's maps change on upgrade
        $this->assertMatchesRegularExpression(
            "/if \(\\\$settings->loadApiAsync \?\? false\) \{\s*\n\s*\\\$params = array_merge\(\['loading' => 'async'\], \\\$params\);/",
            $this->helper
        );
        $this->assertStringNotContainsString('Temporarily disabled async loading', $this->helper);
    }

    public function testUserParamsStillWinOverTheSetting(): void
    {
        // `params: {loading: false}` must be able to opt a single page out
        $this->assertStringContainsString("array_merge(['loading' => 'async'], \$params)", $this->helper);
        $this->assertStringNotContainsString("array_merge(\$params, ['loading' => 'async'])", $this->helper);
    }

    public function testDisabledParamsAreLeftOutOfTheUrl(): void
    {
        $this->assertStringContainsString('array_filter($params, static fn($value) => false !== $value)', $this->apiHelper);
    }

    // ========================================================================= //
    // JS: waiting for the API
    // ========================================================================= //

    public function testReadyWaitsForTheWholeApiAndGivesUp(): void
    {
        $wait = $this->_member('_waitForApi');

        $this->assertStringContainsString("maps.importLibrary('maps'), maps.importLibrary('marker')", $wait);
        $this->assertStringContainsString('if (maps.Map) {', $wait);
        $this->assertStringContainsString('reject(', $wait);
        $this->assertStringContainsString('_readyTimeout: 15000,', $this->googlemaps);
    }

    public function testReadyIsCachedAndAcceptsExtraLibraries(): void
    {
        $ready = $this->_member('ready');

        $this->assertStringContainsString('if (!this._ready) {', $ready);
        $this->assertStringContainsString('google.maps.importLibrary(library)', $ready);
    }

    public function testInitStaysSynchronousWhenTheApiIsReady(): void
    {
        // Deferring every init would break `init('x'); getMap('x')` on sites that never opted in
        $init = $this->_member('init');

        $this->assertMatchesRegularExpression('/^\s*\/\/ If the API is ready, initialize immediately\s*\n\s*if \(this\._isApiReady\(\)\) \{\s*\n\s*this\._init\(mapId, callback\);/m', $init);
        $this->assertNotFalse(strpos($init, 'this.ready()'));
        $this->assertLessThan(strpos($init, 'this.ready()'), strpos($init, 'this._isApiReady()'));
    }

    public function testDeferredInitDoesNotSwallowErrors(): void
    {
        // Only the timeout is caught; a map that throws must still surface (e.g. #157's KmlLayer)
        $init = $this->_member('init');

        $this->assertStringContainsString('this.ready().then(() => this._init(mapId, callback), () => {});', $init);
        $this->assertStringNotContainsString('.catch(', $this->googlemaps);
    }

    public function testDeferredInitStandsInForEachMap(): void
    {
        // So `getMap('one')` on load still works for a Twig map that is waiting
        $init = $this->_member('init');

        $this->assertStringContainsString("this._standIn(null, {'id': container.id}, false);", $init);
        $this->assertStringContainsString('this._replay(map.id, dynamicMap);', $this->_member('_init'));
    }

    // ========================================================================= //
    // JS: the stand-in
    // ========================================================================= //

    public function testMapReturnsAStandInUntilTheApiIsReady(): void
    {
        $map = $this->_member('map');

        $this->assertMatchesRegularExpression('/if \(!this\._isApiReady\(\)\) \{\s*\n\s*return this\._standIn\(locations, options\);/', $map);
    }

    public function testTheStandInAnswersIdentityImmediately(): void
    {
        // `const mapDiv = map.tag();` is documented, so the element must exist now
        $standIn = $this->_member('_standIn');

        $this->assertStringContainsString("if ('id' === property) {", $standIn);
        $this->assertStringContainsString("if ('div' === property) {", $standIn);
        $this->assertStringContainsString("if ('tag' === property) {", $standIn);
        $this->assertStringContainsString("Object.assign(options, {'id': id, '_div': div})", $standIn);
    }

    public function testTheStandInIsNotThenable(): void
    {
        // Returning a function for `then` would make `await map` hang forever
        $standIn = $this->_member('_standIn');

        $this->assertStringContainsString('// Anything else doesn\'t exist yet', $standIn);
        $this->assertStringNotContainsString("'then'", $standIn);
    }

    public function testEveryPublicMapMethodIsEitherChainableOrAGetter(): void
    {
        // A new DynamicMap method must be added to one list, or the stand-in silently drops it
        preg_match_all('/^    this\.([a-zA-Z]\w*) = function/m', $this->dynamicmap, $m);
        $public = array_values(array_diff($m[1], ['tag']));

        preg_match('/_chainable: \[(.*?)\]/s', $this->googlemaps, $chainable);
        preg_match('/_getters: \[(.*?)\]/s', $this->googlemaps, $getters);
        preg_match_all("/'(\w+)'/", $chainable[1] . $getters[1], $listed);

        sort($public);
        $listed = $listed[1];
        sort($listed);

        $this->assertSame($public, $listed);
    }

    public function testTheRealMapAdoptsThePreparedContainer(): void
    {
        // A detached container appended after the API arrives would otherwise be left blank
        $this->assertStringContainsString('this.div = options._div || document.getElementById(options.id);', $this->dynamicmap);
    }

    public function testCreatingAMapTooEarlyExplainsItself(): void
    {
        $this->assertStringContainsString('if (!window.google?.maps?.Map) {', $this->dynamicmap);
        $this->assertStringContainsString('Wrap JS-only maps in googleMaps.ready()', $this->dynamicmap);
    }

    // ========================================================================= //
    // Minified twins and examples
    // ========================================================================= //

    public function testMinifiedTwinsCarryTheChange(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/resources/js';

        $min = file_get_contents("{$dir}/googlemaps.min.js");
        foreach (['_standIn', '_waitForApi', '_isApiReady', '_replay', 'importLibrary', '15e3'] as $token) {
            $this->assertStringContainsString($token, $min, "googlemaps.min.js is missing `{$token}`. Regenerate it.");
        }

        $min = file_get_contents("{$dir}/dynamicmap.min.js");
        foreach (['options._div', 'Wrap JS-only maps in googleMaps.ready()'] as $token) {
            $this->assertStringContainsString($token, $min, "dynamicmap.min.js is missing `{$token}`. Regenerate it.");
        }
    }

    public function testTheFrontEndAddressExampleWaitsForTheApi(): void
    {
        // `importLibrary` doesn't exist yet at `load` under async (Firefox, every time)
        $example = file_get_contents(dirname(__DIR__, 2) . '/docs/examples/js/address-field.js');

        $wait = strpos($example, 'await window.googleMaps.ready();');
        $import = strpos($example, "google.maps.importLibrary('places')");

        // A missing line is `false`, which PHP would happily rank before any position
        $this->assertNotFalse($wait, 'The example no longer waits for googleMaps.ready().');
        $this->assertNotFalse($import);
        $this->assertLessThan($import, $wait);
    }
}
