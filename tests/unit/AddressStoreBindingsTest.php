<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\enums\Defaults;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for the Address field's store-to-DOM bindings.
 *
 * This file exists because of issue #158. In 5.2.0 the Address field moved from
 * fully Vue-rendered to Twig-rendered-then-hydrated, and the generic binding
 * (`v-model="addressStore.data.address[subfield.handle]"`) was replaced with a
 * hand-written list of ten entries inside `connectDom()`. Six paths were left
 * out: name, neighborhood, county, placeId, formatted and raw.
 *
 * The consequences were silent. An input missing from that list still renders
 * and still posts, it just never receives anything the store computes, so a new
 * entry saved NULL and an existing entry re-posted its previous value. It
 * shipped in 5.2.0, 5.2.1 and 5.2.2, roughly seven months, before anyone
 * noticed.
 *
 * The tests below pin the two properties that make a recurrence impossible
 * rather than merely unlikely:
 *
 *   1. Subfield bindings are DERIVED from the handle list, not enumerated.
 *      A derived set cannot omit a member.
 *   2. The canonical JS handle list agrees with PHP's Defaults::SUBFIELDCONFIG,
 *      so the two sides cannot drift.
 *
 * Note the deliberate asymmetry in what is asserted. A positive assertion that
 * the six names appear somewhere in the file would pass against a hardcoded
 * list of sixteen, which is exactly the shape that rotted. So the derivation
 * itself is asserted, and the enumerated form is asserted ABSENT.
 */
class AddressStoreBindingsTest extends TestCase
{
    /** @var string Full source of the Pinia store. */
    private string $storeSource;

    /** @var string Just the `bindings` array literal. */
    private string $bindingsBlock;

    /** @var string Just the `addressSubfields` array literal. */
    private string $canonicalListBlock;

    protected function setUp(): void
    {
        $path = dirname(__DIR__, 2) . '/src/web/assets/src/vue/stores/AddressStore.js';
        $this->assertTrue(file_exists($path), "AddressStore.js should exist at: $path");
        $this->storeSource = file_get_contents($path);

        // Isolate the bindings array so assertions cannot be satisfied by
        // an unrelated mention of a handle elsewhere in a 1700-line file.
        //
        // Extraction is deliberately non-fatal. If setUp() threw on a missing
        // block, every test in this file would fail with the same structural
        // message and none would name the value that actually went unbound,
        // which is the diagnosis a future reader needs.
        $this->bindingsBlock = $this->_extractBlock('/const bindings = \[(.*?)\n        \];/s');
        $this->canonicalListBlock = $this->_extractBlock('/const addressSubfields = \[(.*?)\n\];/s');
    }

    /**
     * Pull one array literal out of the store source, or an empty string.
     */
    private function _extractBlock(string $pattern): string
    {
        return preg_match($pattern, $this->storeSource, $m) ? $m[1] : '';
    }

    // ========================================================================= //
    // The blocks this file reasons about
    // ========================================================================= //

    public function testBindingsArrayExists(): void
    {
        $this->assertNotSame('', $this->bindingsBlock,
            'Could not locate the `bindings` array in connectDom(). Every assertion '
            . 'about which paths are bound depends on finding it.');
    }

    public function testCanonicalHandleListExists(): void
    {
        $this->assertNotSame('', $this->canonicalListBlock,
            'Could not locate the `addressSubfields` array. Without it, subfield '
            . 'bindings cannot be derived and the 5.2.0 regression is reachable again.');
    }

    /**
     * Every subfield handle the plugin knows about, read from PHP.
     */
    private function _phpHandles(): array
    {
        return array_column(Defaults::SUBFIELDCONFIG, 'handle');
    }

    /**
     * Every handle listed in the JS canonical array.
     */
    private function _jsHandles(): array
    {
        preg_match_all("/'([A-Za-z0-9]+)'/", $this->canonicalListBlock, $m);
        return $m[1];
    }

    // ========================================================================= //
    // The two sides agree
    // ========================================================================= //

    public function testCanonicalJsListCoversEveryPhpSubfieldHandle(): void
    {
        $missing = array_diff($this->_phpHandles(), $this->_jsHandles());

        $this->assertSame([], array_values($missing), sprintf(
            'Subfield handles defined in Defaults::SUBFIELDCONFIG but absent from '
            . 'the `addressSubfields` list in AddressStore.js: %s. '
            . 'Add them there, or the canonical list drifts from PHP.',
            implode(', ', $missing)
        ));
    }

    public function testCanonicalJsListInventsNoHandlesPhpDoesNotHave(): void
    {
        $extra = array_diff($this->_jsHandles(), $this->_phpHandles());

        $this->assertSame([], array_values($extra), sprintf(
            'Handles listed in AddressStore.js but absent from Defaults::SUBFIELDCONFIG: %s.',
            implode(', ', $extra)
        ));
    }

    public function testNeitherHandleListIsEmpty(): void
    {
        // Guards the two comparisons above against passing vacuously, which is
        // what array_diff() against an empty array would do.
        $this->assertNotEmpty($this->_jsHandles(), 'The JS handle list is empty.');
        $this->assertNotEmpty($this->_phpHandles(), 'Defaults::SUBFIELDCONFIG is empty.');
    }

    // ========================================================================= //
    // Subfield bindings are derived, not enumerated
    // ========================================================================= //

    public function testSubfieldBindingsAreDerivedFromTheHandleList(): void
    {
        // One binding is built per handle, rather than one line per handle.
        $this->assertMatchesRegularExpression(
            '/const subfieldBindings = handles\.map\(handle => \(\{/',
            $this->storeSource,
            'Subfield bindings must be derived from the handle list. A literal list '
            . 'of bindings is the exact shape that dropped six paths in 5.2.0.'
        );
    }

    public function testDerivedBindingUsesTheHandleForBothSelectorAndPath(): void
    {
        $this->assertStringContainsString('selector: `input[name$="[${handle}]"]`', $this->storeSource);
        $this->assertStringContainsString('path: `address.${handle}`', $this->storeSource);
    }

    public function testHandleListUnionsTheCanonicalListWithLiveSubfieldConfig(): void
    {
        // The union is what lets a subfield added in PHP bind without a JS edit.
        $this->assertMatchesRegularExpression(
            '/const handles = \[\.\.\.new Set\(\[\s*\.\.\.addressSubfields,\s*'
            . '\.\.\.\(settings\.value\.subfieldConfig \?\? \[\]\)\.map\(sf => sf\.handle\)/',
            $this->storeSource,
            'The handle list must union the canonical list with the field\'s own '
            . 'subfieldConfig, so a new subfield binds even before the canonical list is updated.'
        );
    }

    public function testBindingsArraySpreadsTheDerivedSubfieldBindings(): void
    {
        $this->assertStringContainsString('...subfieldBindings', $this->bindingsBlock);
    }

    /**
     * The negative half, and the one that actually blocks a regression.
     *
     * Reverting to a literal list would keep every positive assertion above
     * green if the names happened to be present, so the enumerated form is
     * asserted absent.
     *
     * @dataProvider subfieldHandleProvider
     */
    public function testNoSubfieldIsBoundByAnEnumeratedLiteral(string $handle): void
    {
        $this->assertDoesNotMatchRegularExpression(
            "/path: 'address\.{$handle}'/",
            $this->bindingsBlock,
            "`address.{$handle}` is bound by a literal entry. Subfield bindings are "
            . 'derived from the handle list; a literal entry means the derivation was '
            . 'replaced or partially unrolled.'
        );
    }

    public static function subfieldHandleProvider(): array
    {
        return array_map(
            static fn(string $handle): array => [$handle],
            array_column(Defaults::SUBFIELDCONFIG, 'handle')
        );
    }

    // ========================================================================= //
    // The meta fields, which have no subfield entry of their own
    // ========================================================================= //

    public function testFormattedIsBound(): void
    {
        // The value issue #158 was reported about.
        $this->assertStringContainsString(
            "path: 'address.formatted'",
            $this->bindingsBlock,
            '`formatted` has no subfield entry, so it must be declared explicitly.'
        );
    }

    public function testRawIsBound(): void
    {
        $this->assertStringContainsString("path: 'address.raw'", $this->bindingsBlock);
    }

    public function testFormattedSelectorMatchesTheHiddenInputName(): void
    {
        // meta.twig renders `name="<namespace>[formatted]"`, matched by suffix.
        $this->assertStringContainsString('input[name$="[formatted]"]', $this->bindingsBlock);
    }

    public function testRawSelectorMatchesTheHiddenInputName(): void
    {
        $this->assertStringContainsString('input[name$="[raw]"]', $this->bindingsBlock);
    }

    public function testBothMetaFieldsAreWriteOnly(): void
    {
        // Nobody types into a hidden input, and reading one back would replace
        // the object in the store with a string.
        $this->assertMatchesRegularExpression(
            "/path: 'address\.formatted',\s*domOnly: true/",
            $this->bindingsBlock
        );
        $this->assertMatchesRegularExpression(
            "/path: 'address\.raw',\s*domOnly: true/",
            $this->bindingsBlock
        );
    }

    public function testRawCarriesAJsonSerializer(): void
    {
        // Address::$raw is a PHP `?array`, so it reaches JS as an object.
        // Without this, `(val ?? '') + ''` writes the literal `[object Object]`.
        $this->assertMatchesRegularExpression(
            "/path: 'address\.raw',.*toDom: toJson/",
            $this->bindingsBlock,
            '`raw` must be serialized. A generic string coercion writes `[object Object]`, '
            . 'which normalizeRaw() then discards, silently wiping stored JSON.'
        );
    }

    // ========================================================================= //
    // The machinery both flags depend on
    // ========================================================================= //

    public function testDomToStoreLoopHonorsDomOnly(): void
    {
        $this->assertMatchesRegularExpression(
            '/bindings\.forEach\(\(\{ selector, path, domOnly \}\) => \{\s*'
            . '\/\/[^\n]*\n\s*if \(domOnly\) \{\s*return;/s',
            $this->storeSource,
            'The DOM-to-store loop must skip domOnly bindings.'
        );
    }

    public function testStoreToDomLoopHonorsToDom(): void
    {
        $this->assertStringContainsString(
            "const next = (toDom ? toDom(val) : (val ?? '') + '');",
            $this->storeSource,
            'The store-to-DOM loop must use a binding\'s serializer when it has one.'
        );
    }

    public function testStoreToDomLoopDestructuresToDom(): void
    {
        $this->assertStringContainsString(
            'bindings.forEach(({ selector, path, toDom }) => {',
            $this->storeSource
        );
    }

    // ========================================================================= //
    // The serializer itself
    // ========================================================================= //

    public function testToJsonExists(): void
    {
        $this->assertMatchesRegularExpression('/const toJson = \(value\) => \{/', $this->storeSource);
    }

    public function testToJsonPassesStringsThroughUnchanged(): void
    {
        // The store legitimately holds BOTH shapes: an object after
        // initFromDom() seeds it from PHP, and a string after
        // _applyPlaceToData() sets JSON.stringify(place).
        $this->assertMatchesRegularExpression(
            "/if \('string' === typeof value\) \{\s*return value;/",
            $this->storeSource
        );
    }

    public function testToJsonReturnsEmptyStringForNullish(): void
    {
        $this->assertMatchesRegularExpression(
            "/if \(null === value \|\| undefined === value\) \{\s*return '';/",
            $this->storeSource
        );
    }

    public function testToJsonSerializesObjectsRatherThanCoercingThem(): void
    {
        $this->assertStringContainsString('return JSON.stringify(value);', $this->storeSource);
    }

    public function testToJsonCannotThrow(): void
    {
        // A circular reference would otherwise break every page render.
        $this->assertMatchesRegularExpression(
            '/try \{\s*return JSON\.stringify\(value\);\s*\} catch \(e\) \{\s*return \'\';/',
            $this->storeSource
        );
    }

    // ========================================================================= //
    // Coordinates, which were never broken and must stay bound
    // ========================================================================= //

    /**
     * @dataProvider coordinatePathProvider
     */
    public function testCoordinatePathIsBound(string $path, string $suffix): void
    {
        $this->assertStringContainsString("path: 'coords.{$path}'", $this->bindingsBlock);
        $this->assertStringContainsString($suffix, $this->bindingsBlock);
    }

    public static function coordinatePathProvider(): array
    {
        return [
            'latitude'  => ['lat',  'input[name$="[lat]"]'],
            'longitude' => ['lng',  'input[name$="[lng]"]'],
            'zoom'      => ['zoom', 'input[name$="[zoom]"]'],
        ];
    }

    public function testCoordinateSelectorsSupportTheNestedNamingToo(): void
    {
        // Some namespaces render `[coords][lat]` rather than `[lat]`.
        $this->assertStringContainsString('input[name$="[coords][lat]"]', $this->bindingsBlock);
        $this->assertStringContainsString('input[name$="[coords][lng]"]', $this->bindingsBlock);
        $this->assertStringContainsString('input[name$="[coords][zoom]"]', $this->bindingsBlock);
    }

    // ========================================================================= //
    // Selector strategy
    // ========================================================================= //

    public function testEverySelectorMatchesByNameSuffix(): void
    {
        // Suffix matching is what lets one store serve both the entry form
        // (`fields[address][street1]`) and the field settings live preview
        // (`types[...][settingsPreview][street1]`).
        preg_match_all('/selector: `([^`]+)`/', $this->bindingsBlock, $m);

        $this->assertNotEmpty($m[1], 'No selectors found in the bindings array.');

        foreach ($m[1] as $selector) {
            $this->assertStringContainsString('name$=', $selector, sprintf(
                'Selector `%s` does not match by name suffix, so it will miss at '
                . 'least one Craft namespace.',
                $selector
            ));
        }
    }
}
