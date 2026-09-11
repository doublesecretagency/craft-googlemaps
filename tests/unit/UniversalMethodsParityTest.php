<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\models\DynamicMap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Parity tests between the PHP and JavaScript halves of a dynamic map.
 *
 * A dynamic map is never rendered server-side. Each chained PHP call appends a
 * segment to a private `_dna` array, `tag()` JSON-encodes that into a
 * `data-dna` attribute, and `googlemaps.js` unpacks it on load and replays
 * each segment against a JavaScript `DynamicMap` object.
 *
 * So the two method lists are a wire protocol. Adding or renaming a chainable
 * method on one side without the other produces a DNA segment the JavaScript
 * cannot replay, and the failure is silent: the map renders, and one
 * instruction quietly does nothing.
 *
 * The docs call these the "universal methods", which is the user-facing
 * promise these tests hold the code to.
 */
class UniversalMethodsParityTest extends TestCase
{
    private string $jsSource;

    protected function setUp(): void
    {
        $path = dirname(__DIR__, 2) . '/src/resources/js/dynamicmap.js';
        $this->assertTrue(file_exists($path), "dynamicmap.js should exist at: $path");
        $this->jsSource = file_get_contents($path);
    }

    /**
     * Public chainable methods on the PHP model.
     *
     * `getDna()` is excluded: it is an accessor for the compiled instruction
     * list rather than an instruction, and has no JavaScript twin by design.
     */
    private function _phpMethods(): array
    {
        $methods = [];
        foreach ((new ReflectionClass(DynamicMap::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Only methods declared here. Everything inherited from
            // craft\base\Model and Yii is framework surface, not the protocol.
            if (DynamicMap::class !== $method->getDeclaringClass()->getName()) {
                continue;
            }
            $name = $method->getName();
            if (str_starts_with($name, '__') || 'getDna' === $name) {
                continue;
            }
            $methods[] = $name;
        }
        sort($methods);
        return $methods;
    }

    /**
     * Public methods on the JavaScript object.
     */
    private function _jsMethods(): array
    {
        preg_match_all('/this\.([a-zA-Z]+) = function/', $this->jsSource, $m);
        $methods = array_unique($m[1]);
        sort($methods);
        return $methods;
    }

    // ========================================================================= //
    // The lists were found at all
    // ========================================================================= //

    public function testBothSidesExposeMethods(): void
    {
        // Guards every comparison below against passing vacuously.
        $this->assertNotEmpty($this->_phpMethods());
        $this->assertNotEmpty($this->_jsMethods());
    }

    // ========================================================================= //
    // Parity
    // ========================================================================= //

    public function testEveryPhpMethodHasAJavaScriptTwin(): void
    {
        $missing = array_diff($this->_phpMethods(), $this->_jsMethods());

        $this->assertSame([], array_values($missing), sprintf(
            'DynamicMap methods with no counterpart in dynamicmap.js: %s. '
            . 'A DNA segment naming one of these cannot be replayed, and the map '
            . 'renders with that instruction silently skipped.',
            implode(', ', $missing)
        ));
    }

    /**
     * The reverse is deliberately NOT symmetric.
     *
     * JavaScript legitimately exposes read-only accessors that have no PHP
     * twin, because there is nothing to read at compile time. They are listed
     * explicitly so a genuinely new JS-only method fails this test and has to
     * be justified rather than drifting in.
     */
    public function testJavaScriptOnlyMethodsAreTheKnownAccessors(): void
    {
        $jsOnly = array_values(array_diff($this->_jsMethods(), $this->_phpMethods()));

        $expected = [
            'getBounds',
            'getCenter',
            'getCircle',
            'getInfoWindow',
            'getKml',
            'getMarker',
            'getMarkerClusterer',
            'getZoom',
        ];

        sort($expected);

        $this->assertSame($expected, $jsOnly, sprintf(
            'Unexpected JavaScript-only method(s). Either add a PHP counterpart, '
            . 'or add it to the accessor list in this test with a reason. Found: %s',
            implode(', ', $jsOnly)
        ));
    }

    /**
     * @dataProvider chainableMethodProvider
     */
    public function testChainableMethodExistsOnBothSides(string $method): void
    {
        $this->assertContains($method, $this->_phpMethods(), "PHP is missing {$method}().");
        $this->assertContains($method, $this->_jsMethods(), "JavaScript is missing {$method}().");
    }

    /**
     * The documented universal methods, spelled out rather than derived.
     *
     * Deriving this list from either side would make the test agree with
     * whatever that side currently says, which is exactly what a parity test
     * must not do.
     */
    public static function chainableMethodProvider(): array
    {
        $methods = [
            'markers', 'circles', 'kml', 'styles', 'zoom', 'center', 'fit',
            'refresh', 'panToMarker', 'setMarkerIcon', 'hideMarker', 'showMarker',
            'openInfoWindow', 'closeInfoWindow', 'hideCircle', 'showCircle',
            'hideKml', 'showKml', 'tag',
        ];
        return array_combine(
            $methods,
            array_map(static fn(string $m): array => [$m], $methods)
        );
    }

    // ========================================================================= //
    // The DNA contract itself
    // ========================================================================= //

    public function testGetDnaIsPublic(): void
    {
        $this->assertTrue((new ReflectionClass(DynamicMap::class))->getMethod('getDna')->isPublic());
    }

    public function testTagRendersTheDnaIntoADataAttribute(): void
    {
        $php = file_get_contents(dirname(__DIR__, 2) . '/src/models/DynamicMap.php');

        $this->assertStringContainsString("'data-dna' => Json::encode(\$this->_dna)", $php);
    }

    public function testJavaScriptReadsTheSameDataAttribute(): void
    {
        $this->assertStringContainsString('map.dataset.dna', file_get_contents(
            dirname(__DIR__, 2) . '/src/resources/js/googlemaps.js'
        ));
    }

    public function testTagRequiresTheChainToStartWithAMapSegment(): void
    {
        $php = file_get_contents(dirname(__DIR__, 2) . '/src/models/DynamicMap.php');

        $this->assertStringContainsString(
            "if ('map' !== (\$this->_dna[0]['type'] ?? false))",
            $php,
            'Without this guard a chain missing its map() segment produces DNA '
            . 'the JavaScript cannot initialize.'
        );
    }

    public function testMarkerClickCallbackIsFlattenedBeforeExport(): void
    {
        // A markerClick callback is a JavaScript string. Exporting it through
        // JSON into a data attribute would inject it into the page.
        $php = file_get_contents(dirname(__DIR__, 2) . '/src/models/DynamicMap.php');

        $this->assertStringContainsString("\$item['options']['markerClick'] = true;", $php);
    }

    // ========================================================================= //
    // Minified twins, which are loaded when minifyJsFiles is on
    // ========================================================================= //

    /**
     * @dataProvider frontEndScriptProvider
     */
    public function testMinifiedTwinExistsAndIsNotEmpty(string $file): void
    {
        $min = dirname(__DIR__, 2) . "/src/resources/js/{$file}.min.js";

        $this->assertTrue(file_exists($min), "Missing minified twin: {$file}.min.js");
        $this->assertGreaterThan(0, filesize($min), "{$file}.min.js is empty.");
    }

    /**
     * 🛑 No build task produces these. They are regenerated by hand, so a
     * change to the source without a matching regeneration ships stale code to
     * anyone running `minifyJsFiles => true`.
     *
     * Every public method name survives minification, since they are object
     * properties rather than local bindings, so the two lists can be compared
     * directly. That is a real check where an mtime comparison would only be a
     * proxy that any `cp` or checkout can flip.
     *
     * @dataProvider frontEndScriptProvider
     */
    public function testMinifiedTwinExposesTheSameMethodsAsItsSource(string $file): void
    {
        $dir = dirname(__DIR__, 2) . '/src/resources/js';

        $sourceMethods = $this->_methodNames(file_get_contents("{$dir}/{$file}.js"));
        $minMethods = $this->_methodNames(file_get_contents("{$dir}/{$file}.min.js"));

        // Only meaningful for the file that declares methods this way.
        if ([] === $sourceMethods) {
            $this->assertSame([], $minMethods);
            return;
        }

        $missing = array_diff($sourceMethods, $minMethods);

        $this->assertSame([], array_values($missing), sprintf(
            '%s.min.js is missing method(s) present in %s.js: %s. Regenerate it with '
            . 'terser, or the minified path serves stale code. No build task does this '
            . 'for you.',
            $file,
            $file,
            implode(', ', $missing)
        ));
    }

    private function _methodNames(string $source): array
    {
        preg_match_all('/this\.([a-zA-Z]+) ?= ?function/', $source, $m);
        $names = array_unique($m[1]);
        sort($names);
        return $names;
    }

    public static function frontEndScriptProvider(): array
    {
        return [
            'googlemaps' => ['googlemaps'],
            'dynamicmap' => ['dynamicmap'],
        ];
    }
}
