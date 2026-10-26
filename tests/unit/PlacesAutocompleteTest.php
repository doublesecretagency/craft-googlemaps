<?php
namespace doublesecretagency\googlemaps\tests\unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for Address field autocompletion on Places API (New) (issue #140).
 *
 * Google closed the legacy Places API to Cloud projects created after
 * 2025-03-01, so new customers got no Autocomplete at all. The field now asks
 * Places API (New) first, through its own suggestion menu on the existing
 * input, and falls back to the legacy widget page-wide when the new API isn't
 * enabled (gRPC PERMISSION_DENIED, code 7). The fallback is reported once, in
 * the console and in Craft's Deprecation Warnings.
 *
 * Everything here is JavaScript or a template, so the tests are structural.
 * The live behavior (both APIs filling identical subfields, the fallback, the
 * deprecation entry) was verified in the browser against two Cloud projects.
 */
class PlacesAutocompleteTest extends TestCase
{
    private string $module;
    private string $store;
    private string $controller;
    private string $utility;
    private string $utilityJs;

    protected function setUp(): void
    {
        $src = dirname(__DIR__, 2) . '/src';
        $this->module = file_get_contents("{$src}/web/assets/src/vue/places/PlacesAutocomplete.js");
        $this->store = file_get_contents("{$src}/web/assets/src/vue/stores/AddressStore.js");
        $this->controller = file_get_contents("{$src}/controllers/DeprecationController.php");
        $this->utility = file_get_contents("{$src}/templates/_utility/test-google-api-keys/index.twig");
        $this->utilityJs = file_get_contents("{$src}/templates/_utility/test-google-api-keys/browser-key.twig");
    }

    private function _function(string $source, string $name): string
    {
        // From the declaration to the next top-level declaration
        $start = strpos($source, "function {$name}(");
        $this->assertNotFalse($start, "Missing function {$name}().");
        $end = strpos($source, "\n}\n", $start);
        return substr($source, $start, $end - $start);
    }

    // ========================================================================= //
    // The module
    // ========================================================================= //

    public function testModuleExportsItsApi(): void
    {
        foreach (['attachAutocomplete', 'toLegacyPlace', 'isPermissionError', 'isAnyMenuOpen', 'getProvider'] as $name) {
            $this->assertStringContainsString("export function {$name}(", $this->module);
        }
    }

    public function testOnlyPermissionErrorsTriggerTheFallback(): void
    {
        // A network blip must not switch the page to the legacy API
        $body = $this->_function($this->module, 'isPermissionError');

        $this->assertStringContainsString("'RpcError' === error?.name && 7 === error?.code", $body);
    }

    public function testTheAdapterProducesTheLegacyShape(): void
    {
        // Everything downstream (subfields, `raw`, coordinates) reads the legacy shape
        $body = $this->_function($this->module, 'toLegacyPlace');

        foreach ([
            'long_name: c.longText',
            'short_name: c.shortText',
            'formatted_address: place.formattedAddress',
            'geometry: {location: place.location',
            'html_attributions: []',
            'name: place.displayName',
            'place_id: place.id',
        ] as $mapping) {
            $this->assertStringContainsString($mapping, $body);
        }
    }

    public function testSuggestionsShareOneSessionToken(): void
    {
        // Without a session token, every keystroke is billed separately
        $this->assertStringContainsString('token = token || new places.AutocompleteSessionToken();', $this->module);
        $this->assertStringContainsString('sessionToken: token,', $this->module);
        $this->assertMatchesRegularExpression('/await place\.fetchFields\(\{fields: PLACE_FIELDS\}\);\s*\n(?:\s*\/\/[^\n]*\n)*\s*token = null;/', $this->module);
    }

    public function testTheMenuShowsTheGoogleMapsAttribution(): void
    {
        // Required by Google when predictions are shown without a map
        $this->assertStringContainsString("attribution.textContent = 'Google Maps';", $this->module);
        $this->assertStringContainsString("attribution.setAttribute('translate', 'no');", $this->module);
    }

    public function testTheMenuIsAnAccessibleCombobox(): void
    {
        foreach ([
            "input.setAttribute('role', 'combobox');",
            "input.setAttribute('aria-expanded', 'false');",
            "input.setAttribute('aria-controls', id);",
            "input.setAttribute('aria-activedescendant'",
            "list.setAttribute('role', 'listbox');",
            "li.setAttribute('role', 'option');",
            "li.setAttribute('aria-selected'",
        ] as $attribute) {
            $this->assertStringContainsString($attribute, $this->module);
        }
    }

    public function testEnterNeverSubmitsWhileTheMenuIsOpen(): void
    {
        $this->assertMatchesRegularExpression("/case 'Enter':\s*\n\s*\/\/[^\n]*\n\s*e\.preventDefault\(\);/", $this->module);
    }

    public function testTheFallbackIsPageWideAndReportedOnce(): void
    {
        $this->assertStringContainsString("_provider = 'legacy';", $this->module);
        $this->assertStringContainsString("if ('legacy' === _provider || !places?.AutocompleteSuggestion) {", $this->module);

        $report = $this->_function($this->module, '_reportLegacy');
        $this->assertStringContainsString('if (_reported) {', $report);
        $this->assertStringContainsString("sendActionRequest?.('POST', 'google-maps/deprecation/legacy-places')", $report);
    }

    // ========================================================================= //
    // The store
    // ========================================================================= //

    public function testTheStoreTriesTheNewApiFirst(): void
    {
        $this->assertStringContainsString("import { attachAutocomplete, isAnyMenuOpen } from '../places/PlacesAutocomplete';", $this->store);
        $this->assertStringContainsString('const menu = attachAutocomplete(el, {', $this->store);
    }

    public function testTheLegacyWidgetOnlyLoadsAsAFallback(): void
    {
        // One construction site, reached from the fallback or an already-legacy page
        $this->assertSame(1, substr_count($this->store, 'new window.google.maps.places.Autocomplete('));
        $this->assertMatchesRegularExpression('/onFallback: \(\) => \{[^}]*_connectLegacyAutocomplete\(el\);/s', $this->store);
    }

    public function testAFallbackMidTypingStillOpensTheLegacyMenu(): void
    {
        // The legacy menu only opens after the input gains focus
        $this->assertStringContainsString('_refocusWhenLegacyReady(el);', $this->store);
        $this->assertStringContainsString("document.querySelectorAll('.pac-container').length", $this->store);
    }

    public function testTheSubmitGuardKnowsAboutTheNewMenu(): void
    {
        $this->assertMatchesRegularExpression('/function _isPlacesMenuOpen\(\) \{\s*\n(?:\s*\/\/[^\n]*\n)*\s*if \(isAnyMenuOpen\(\)\) \{/', $this->store);
    }

    // ========================================================================= //
    // The deprecation warning
    // ========================================================================= //

    public function testTheFallbackIsLoggedAsADeprecation(): void
    {
        $this->assertStringContainsString("'googlemaps.legacyPlacesApi',", $this->controller);
        $this->assertStringContainsString('Craft::$app->getDeprecator()->log(', $this->controller);
        $this->assertStringContainsString('$this->requirePostRequest();', $this->controller);
        $this->assertStringNotContainsString('allowAnonymous', $this->controller);
    }

    public function testADeprecatorWhichThrowsDoesNotBreakTheField(): void
    {
        // Sites may set `deprecator.throwExceptions`, which turned the request into a 500
        $this->assertStringContainsString('} catch (DeprecationException) {', $this->controller);
    }

    // ========================================================================= //
    // The "Test Google API Keys" utility
    // ========================================================================= //

    public function testTheUtilityListsEveryRequiredApi(): void
    {
        foreach ([
            'Maps JavaScript API' => 'maps-backend.googleapis.com',
            'Maps Static API' => 'static-maps-backend.googleapis.com',
            'Places API (New)' => 'places.googleapis.com',
            'Geocoding API' => 'geocoding-backend.googleapis.com',
        ] as $api => $service) {
            $this->assertStringContainsString("<th scope=\"row\">{$api}</th>", $this->utility);
            $this->assertStringContainsString("apis/library/{$service}", $this->utility);
        }
        $this->assertStringNotContainsString('places-backend.googleapis.com', $this->utility);
    }

    public function testEveryStatusIsStatedInWords(): void
    {
        // A color-blind user couldn't tell the old green and red rows apart
        $this->assertStringContainsString('<span class="gm-status-label">{{ label }}</span>', $this->utility);
        $this->assertStringNotContainsString('tr.valid td', $this->utility);
        $this->assertStringNotContainsString('tr.invalid td', $this->utility);

        foreach (['pending', 'valid', 'legacy', 'invalid'] as $state) {
            $this->assertMatchesRegularExpression("/{$state}: '<svg [^']*aria-hidden=\"true\"/", $this->utility);
        }
    }

    public function testTheUtilityReportsTheLegacyPlacesApi(): void
    {
        $this->assertStringContainsString("gm_setStatus('gm-status-places', 'legacy', 'Using legacy API'", $this->utilityJs);
    }
}
