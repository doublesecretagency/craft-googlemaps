<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\web\AssetBundle;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for the five asset bundles.
 *
 * These exist because of a defect this suite was written to catch.
 * `GoogleMapsAsset` called `GoogleMapsPlugin::getInstance()` without importing
 * the class, so PHP resolved the name relative to the bundle's own namespace
 * and it did not exist. Registering the bundle threw a class-not-found error.
 *
 * It was latent rather than live, because nothing registers it: the front end
 * loads its JavaScript through `GoogleMaps::getAssets()` and `registerJsFile()`
 * instead, bypassing asset bundles entirely. So the bug sat behind dead code,
 * invisible to every runtime check, and only a reference audit finds it.
 *
 * The test below is the audit, generalized: any class naming `GoogleMapsPlugin`
 * must import it.
 */
class AssetBundlesTest extends TestCase
{
    private const BUNDLES = [
        'AddressFieldAsset',
        'AddressFieldSettingsAsset',
        'GoogleMapsAsset',
        'JsApiAsset',
        'SettingsAsset',
    ];

    private function _path(string $bundle): string
    {
        return dirname(__DIR__, 2) . "/src/web/assets/{$bundle}.php";
    }

    private function _class(string $bundle): string
    {
        return "doublesecretagency\\googlemaps\\web\\assets\\{$bundle}";
    }

    // ========================================================================= //
    // Every bundle is a bundle
    // ========================================================================= //

    /**
     * @dataProvider bundleProvider
     */
    public function testBundleFileExists(string $bundle): void
    {
        $this->assertTrue(file_exists($this->_path($bundle)));
    }

    /**
     * @dataProvider bundleProvider
     */
    public function testBundleExtendsAssetBundle(string $bundle): void
    {
        $this->assertTrue((new ReflectionClass($this->_class($bundle)))->isSubclassOf(AssetBundle::class));
    }

    /**
     * @dataProvider bundleProvider
     */
    public function testBundleOverridesInit(string $bundle): void
    {
        $reflection = new ReflectionClass($this->_class($bundle));

        $this->assertSame(
            $this->_class($bundle),
            $reflection->getMethod('init')->getDeclaringClass()->getName(),
            'Every bundle here assembles its files at runtime, so it must declare init().'
        );
    }

    /**
     * @dataProvider bundleProvider
     */
    public function testBundleCallsParentInitFirst(string $bundle): void
    {
        // Skipping it leaves sourcePath and depends unprocessed.
        $this->assertStringContainsString('parent::init();', file_get_contents($this->_path($bundle)));
    }

    public static function bundleProvider(): array
    {
        return array_combine(
            self::BUNDLES,
            array_map(static fn(string $b): array => [$b], self::BUNDLES)
        );
    }

    // ========================================================================= //
    // 🛑 The regression this file exists for
    // ========================================================================= //

    /**
     * A class that names GoogleMapsPlugin must import it.
     *
     * Without the import, PHP resolves the bare name inside the current
     * namespace, finds nothing, and throws on first use. The failure is
     * invisible until something actually instantiates the class, which for a
     * dead bundle may be never.
     *
     * @dataProvider bundleProvider
     */
    public function testBundleImportsEveryPluginClassItReferences(string $bundle): void
    {
        $source = file_get_contents($this->_path($bundle));

        if (!preg_match('/\bGoogleMapsPlugin::/', $source)) {
            // Nothing to import. Assert that explicitly rather than skipping,
            // so the test still counts as having run.
            $this->assertStringNotContainsString('GoogleMapsPlugin::', $source);
            return;
        }

        $this->assertStringContainsString(
            'use doublesecretagency\googlemaps\GoogleMapsPlugin;',
            $source,
            "{$bundle} calls GoogleMapsPlugin:: without importing it. PHP will resolve "
            . 'that to doublesecretagency\\googlemaps\\web\\assets\\GoogleMapsPlugin, '
            . 'which does not exist, and throw on first use.'
        );
    }

    /**
     * The same audit across the whole plugin, not just the bundles.
     *
     * The bundle case is what actually happened, but nothing makes bundles
     * special, so the check is widened to every source file.
     */
    public function testNoSourceFileReferencesThePluginClassWithoutImportingIt(): void
    {
        $offenders = [];
        $root = dirname(__DIR__, 2) . '/src';

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ('php' !== $file->getExtension() || str_contains($file->getPathname(), '/node_modules/')) {
                continue;
            }

            $source = file_get_contents($file->getPathname());

            // The plugin class itself declares the name rather than importing it.
            if (preg_match('/^\s*class GoogleMapsPlugin\b/m', $source)) {
                continue;
            }

            $references = (bool) preg_match('/\bGoogleMapsPlugin::/', $source);
            $imports = str_contains($source, 'use doublesecretagency\googlemaps\GoogleMapsPlugin;');

            if ($references && !$imports) {
                $offenders[] = str_replace($root . '/', '', $file->getPathname());
            }
        }

        $this->assertSame([], $offenders, sprintf(
            'These files reference GoogleMapsPlugin without importing it, and will '
            . 'throw a class-not-found error on first use: %s',
            implode(', ', $offenders)
        ));
    }

    public function testTheAuditActuallyInspectedFiles(): void
    {
        // Guards the sweep above against silently examining nothing.
        $root = dirname(__DIR__, 2) . '/src';
        $count = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ('php' === $file->getExtension() && !str_contains($file->getPathname(), '/node_modules/')) {
                $count++;
            }
        }

        $this->assertGreaterThan(30, $count, 'The source sweep found suspiciously few PHP files.');
    }

    // ========================================================================= //
    // Source paths and dependencies
    // ========================================================================= //

    /**
     * @dataProvider sourcePathProvider
     */
    public function testBundleUsesTheExpectedSourcePath(string $bundle, string $alias): void
    {
        $this->assertStringContainsString(
            "\$this->sourcePath = '{$alias}';",
            file_get_contents($this->_path($bundle))
        );
    }

    public static function sourcePathProvider(): array
    {
        return [
            'control panel field'    => ['AddressFieldAsset', '@doublesecretagency/googlemaps/web/assets/dist'],
            'field settings'         => ['AddressFieldSettingsAsset', '@doublesecretagency/googlemaps/web/assets/dist'],
            'plugin settings'        => ['SettingsAsset', '@doublesecretagency/googlemaps/web/assets/dist'],
            'front-end map scripts'  => ['JsApiAsset', '@doublesecretagency/googlemaps/resources'],
        ];
    }

    public function testTheAliasesUsedBySourcePathsAreRegisteredInComposer(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);

        // Craft registers plugin aliases from the psr-4 roots.
        $this->assertArrayHasKey('doublesecretagency\\googlemaps\\', $composer['autoload']['psr-4']);
    }

    public function testJsApiAssetHonorsTheMinifyJsFilesSetting(): void
    {
        $source = file_get_contents($this->_path('JsApiAsset'));

        $this->assertStringContainsString('minifyJsFiles', $source);
        $this->assertStringContainsString('js/googlemaps.{$min}js', $source);
        $this->assertStringContainsString('js/dynamicmap.{$min}js', $source);
    }

    public function testGoogleMapsAssetLoadsTheApiUrl(): void
    {
        $source = file_get_contents($this->_path('GoogleMapsAsset'));

        $this->assertStringContainsString('GoogleMaps::getApiUrl()', $source);
        $this->assertStringContainsString("'defer' => true", $source);
    }

    public function testEveryBundleGuardsAgainstTheUninstantiatedPlugin(): void
    {
        // A bundle can be constructed before the plugin instance exists, and
        // dereferencing a typed static property that is unset is a fatal.
        foreach (['GoogleMapsAsset', 'JsApiAsset', 'AddressFieldAsset', 'AddressFieldSettingsAsset'] as $bundle) {
            $source = file_get_contents($this->_path($bundle));

            $this->assertMatchesRegularExpression(
                '/GoogleMapsPlugin::getInstance\(\)/',
                $source,
                "{$bundle} should reach the plugin through getInstance(), which returns "
                . 'null when uninstantiated, rather than the static property.'
            );
        }
    }

    // ========================================================================= //
    // The built assets the bundles point at
    // ========================================================================= //

    /**
     * @dataProvider builtAssetProvider
     */
    public function testBuiltAssetExists(string $file): void
    {
        $path = dirname(__DIR__, 2) . "/src/web/assets/dist/{$file}";

        $this->assertTrue(file_exists($path), "Missing built asset: {$file}. Run `npm run dev` in src/.");
        $this->assertGreaterThan(0, filesize($path), "{$file} is empty.");
    }

    public static function builtAssetProvider(): array
    {
        $files = [
            'js/address.js',
            'js/address-settings.js',
            'js/settings.js',
            'js/Sortable.min.js',
            'css/address.css',
            'css/address-settings.css',
        ];
        return array_combine($files, array_map(static fn(string $f): array => [$f], $files));
    }

    public function testTheMixManifestListsEveryBuiltAsset(): void
    {
        $manifest = json_decode(
            file_get_contents(dirname(__DIR__, 2) . '/src/mix-manifest.json'),
            true
        );

        $this->assertIsArray($manifest);
        $this->assertNotEmpty($manifest);

        foreach ($manifest as $key => $value) {
            $path = dirname(__DIR__, 2) . '/src' . $key;
            $this->assertTrue(file_exists($path), "Manifest names a missing file: {$key}");
        }
    }

    /**
     * @dataProvider storeBearingBundleProvider
     */
    public function testTheBundleCarriesTheCurrentStoreBehavior(string $bundle): void
    {
        // AddressStore.js is compiled into both control panel bundles, so an
        // edit to the store without a rebuild ships stale behavior.
        //
        // Checked by content rather than by modification time. An mtime
        // comparison flips on any `cp`, `git checkout`, or fresh clone, which
        // makes it a proxy for the question rather than an answer to it.
        $dist = file_get_contents(dirname(__DIR__, 2) . "/src/web/assets/dist/js/{$bundle}");

        foreach (['addressSubfields', 'subfieldBindings', 'domOnly', 'toJson'] as $token) {
            $this->assertStringContainsString(
                $token,
                $dist,
                "dist/js/{$bundle} does not contain `{$token}` from AddressStore.js. "
                . 'Run `npm run dev` in src/ to rebuild.'
            );
        }
    }

    public static function storeBearingBundleProvider(): array
    {
        return [
            'address.js'          => ['address.js'],
            'address-settings.js' => ['address-settings.js'],
        ];
    }
}
