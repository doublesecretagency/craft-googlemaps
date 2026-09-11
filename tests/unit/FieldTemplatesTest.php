<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\enums\Defaults;
use PHPUnit\Framework\TestCase;

/**
 * Source-level tests for the Twig templates that render the Address field.
 *
 * Since 5.2.0 Twig owns the markup and Vue only hydrates it, which makes these
 * templates the field's actual form. Every input a value can post through is
 * declared here, so a missing input is a value that can never be saved no
 * matter what the JavaScript does.
 *
 * 🛑 A source-level test reads a template as text, and parseability is not a
 * property of text. A template that throws a Twig syntax error on every request
 * still satisfies every assertion in this file. That gap cannot be closed
 * without a Twig environment, which means bootstrapping Craft, which this suite
 * deliberately does not do. Verify rendering in the sandbox.
 */
class FieldTemplatesTest extends TestCase
{
    private function _template(string $path): string
    {
        $full = dirname(__DIR__, 2) . "/src/templates/{$path}";
        $this->assertTrue(file_exists($full), "Template should exist at: {$full}");
        return file_get_contents($full);
    }

    // ========================================================================= //
    // Every template the field needs
    // ========================================================================= //

    /**
     * @dataProvider templateProvider
     */
    public function testTemplateExists(string $path): void
    {
        $this->assertTrue(file_exists(dirname(__DIR__, 2) . "/src/templates/{$path}"));
    }

    public static function templateProvider(): array
    {
        $templates = [
            'address/index.twig',
            'address/subfields.twig',
            'address/meta.twig',
            'address/coords.twig',
            'address/map.twig',
            'address/toggle.twig',
            'address-settings/index.twig',
            'address-settings/subfield-manager.twig',
            'address-settings/dropdown-fields.twig',
            'address-settings/live-preview.twig',
            'address-settings/additional-notes.twig',
            'settings.twig',
            'maps/info-window-error.twig',
            '_utility/test-google-api-keys/index.twig',
            '_utility/test-address-lookup/index.twig',
        ];
        return array_combine($templates, array_map(static fn(string $t): array => [$t], $templates));
    }

    // ========================================================================= //
    // meta.twig, which carries the two values issue #158 was about
    // ========================================================================= //

    public function testMetaTemplateRendersAFormattedInput(): void
    {
        $meta = $this->_template('address/meta.twig');

        $this->assertStringContainsString('[formatted]', $meta);
        $this->assertStringContainsString('type="hidden"', $meta);
    }

    public function testMetaTemplateRendersARawInput(): void
    {
        $this->assertStringContainsString('[raw]', $this->_template('address/meta.twig'));
    }

    public function testBothMetaInputsAreNamespaced(): void
    {
        // Craft namespaces field inputs, and the Vue store matches by suffix,
        // so an unnamespaced input would collide across Matrix blocks.
        $meta = $this->_template('address/meta.twig');

        $this->assertSame(
            2,
            preg_match_all('/name="\{\{ config\.namespace\.handle \}\}\[/', $meta),
            'Both meta inputs must use the namespaced handle.'
        );
    }

    public function testTheRawInputIsJsonEncoded(): void
    {
        // `raw` is a PHP array. Rendering it without json_encode would emit
        // the literal word Array.
        $this->assertMatchesRegularExpression(
            '/value="\{\{ config\.data\.address\[.raw.\]\|json_encode \}\}"/',
            $this->_template('address/meta.twig')
        );
    }

    public function testTheFormattedInputIsSeededFromStoredData(): void
    {
        // This is what makes an unbound input re-post its previous value rather
        // than an empty one, which is why issue #158 presented as two bugs.
        $this->assertStringContainsString(
            "value=\"{{ config.data.address['formatted'] }}\"",
            $this->_template('address/meta.twig')
        );
    }

    // ========================================================================= //
    // subfields.twig
    // ========================================================================= //

    public function testSubfieldsAreRenderedFromTheConfigRatherThanListed(): void
    {
        // The same derivation principle as the store's bindings. A hardcoded
        // list here would drop a subfield the same way.
        $this->assertStringContainsString(
            '{% for subfield in config.settings.subfieldConfig',
            $this->_template('address/subfields.twig')
        );
    }

    public function testSubfieldInputNameIsBuiltFromTheHandle(): void
    {
        $this->assertStringContainsString(
            'name="{{ "#{config.namespace.handle}[#{subfield.handle}]" }}"',
            $this->_template('address/subfields.twig')
        );
    }

    public function testSubfieldValueIsSeededFromStoredData(): void
    {
        $this->assertStringContainsString(
            'value="{{ config.data.address[subfield.handle] }}"',
            $this->_template('address/subfields.twig')
        );
    }

    public function testDisabledSubfieldsStillRenderButAreHidden(): void
    {
        // 🛑 Load-bearing. A disabled subfield keeps its input in the DOM, so
        // its stored value keeps posting rather than being wiped on the next
        // save. Removing the input entirely would silently discard data.
        $subfields = $this->_template('address/subfields.twig');

        $this->assertStringContainsString('{% if not subfield.enabled %}', $subfields);
        $this->assertStringContainsString('display:none;', $subfields);
    }

    public function testSubfieldWidthIsClampedToOneHundredPercent(): void
    {
        $subfields = $this->_template('address/subfields.twig');

        $this->assertStringContainsString('{% if 100 < width %}', $subfields);
    }

    public function testRequiredSubfieldsAreMarkedInThePlaceholder(): void
    {
        $this->assertStringContainsString(
            "subfield.required ? ' *'",
            $this->_template('address/subfields.twig')
        );
    }

    public function testAutocompleteAttributeIsSetToDefeatTheBrowser(): void
    {
        // Chrome ignores `off`, so the plugin uses a nonsense value.
        $this->assertStringContainsString(
            'autocomplete="chrome-off"',
            $this->_template('address/subfields.twig')
        );
    }

    // ========================================================================= //
    // Every value has somewhere to post through
    // ========================================================================= //

    public function testEverySubfieldHandleCanPostThroughSomeTemplate(): void
    {
        // Either by the generic subfield loop, or by a named input.
        $subfields = $this->_template('address/subfields.twig');
        $meta = $this->_template('address/meta.twig');
        $coords = $this->_template('address/coords.twig');

        $rendersEverySubfield = str_contains($subfields, 'config.settings.subfieldConfig');

        foreach (array_column(Defaults::SUBFIELDCONFIG, 'handle') as $handle) {
            $named = str_contains($meta, "[{$handle}]")
                || str_contains($coords, "[{$handle}]")
                || str_contains($coords, "key: '{$handle}'");

            $this->assertTrue(
                $rendersEverySubfield || $named,
                "Subfield `{$handle}` has no input to post through."
            );
        }
    }

    public function testCoordinateInputsExist(): void
    {
        // Rendered from a config list rather than three literal inputs, so the
        // keys are asserted where they are declared.
        $coords = $this->_template('address/coords.twig');

        foreach (['lat', 'lng', 'zoom'] as $key) {
            $this->assertMatchesRegularExpression(
                "/key: '{$key}'/",
                $coords,
                "No coordinate input declared for `{$key}`."
            );
        }
    }

    public function testCoordinateInputNamesAreBuiltFromTheConfigKey(): void
    {
        $this->assertStringContainsString(
            'name="{{ "#{config.namespace.handle}[#{input.key}]" }}"',
            $this->_template('address/coords.twig')
        );
    }

    public function testCoordinateInputsAreRenderedFromTheConfigList(): void
    {
        $this->assertStringContainsString(
            '{% for input in coordsConfig',
            $this->_template('address/coords.twig')
        );
    }

    public function testZoomIsExemptFromTheRequiredCoordinatesRule(): void
    {
        // A zoom level is not a coordinate, so requiring it would block a save
        // on a field that only requires lat and lng.
        $this->assertStringContainsString(
            "input.key != 'zoom'",
            $this->_template('address/coords.twig')
        );
    }

    // ========================================================================= //
    // Hydration hooks
    // ========================================================================= //

    public function testTheFieldTemplateExposesARootForVueToHydrate(): void
    {
        $index = $this->_template('address/index.twig');

        $this->assertMatchesRegularExpression(
            '/data-gm|id="\{\{ config\.namespace/',
            $index,
            'Vue needs a root element to hydrate, identified per field instance.'
        );
    }

    public function testTheMapWrapperCarriesTheRoleTheStoreLooksFor(): void
    {
        // `_applyVisibilityToDom()` queries `[data-role="map-wrapper"]`.
        $this->assertStringContainsString(
            'data-role="map-wrapper"',
            $this->_template('address/map.twig') . $this->_template('address/index.twig')
        );
    }

    public function testTheToggleCarriesTheAttributesTheHydratorQueries(): void
    {
        // `address-hydrator.js` looks for these exact hooks.
        $toggle = $this->_template('address/toggle.twig');

        $this->assertStringContainsString('data-gm-toggle', $toggle);
    }

    // ========================================================================= //
    // Template hygiene
    // ========================================================================= //

    /**
     * @dataProvider templateProvider
     */
    public function testTemplateHasBalancedTwigDelimiters(string $path): void
    {
        // Not a parse check, but it catches the most common typo, and a stray
        // brace is the specific failure that takes down every page.
        $source = $this->_template($path);

        $this->assertSame(
            substr_count($source, '{%'),
            substr_count($source, '%}'),
            "Unbalanced {% %} in {$path}."
        );
        $this->assertSame(
            substr_count($source, '{#'),
            substr_count($source, '#}'),
            "Unbalanced {# #} in {$path}."
        );
    }

    /**
     * @dataProvider templateProvider
     */
    public function testTemplateIsNotEmpty(string $path): void
    {
        $this->assertNotSame('', trim($this->_template($path)));
    }
}
