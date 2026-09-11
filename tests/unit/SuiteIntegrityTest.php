<?php
namespace doublesecretagency\googlemaps\tests\unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests about the suite itself.
 *
 * A suite that collects nothing reports green, which is a failure of the
 * instrument rather than of the code and is invisible from every other test. So
 * the checks that guard the runner get their own file.
 */
class SuiteIntegrityTest extends TestCase
{
    private function _root(): string
    {
        return dirname(__DIR__, 2);
    }

    // ========================================================================= //
    // Configuration
    // ========================================================================= //

    public function testPhpunitConfigExists(): void
    {
        $this->assertTrue(file_exists($this->_root() . '/phpunit.xml'));
    }

    public function testConfigBootstrapsTheSandboxAutoloader(): void
    {
        // The plugin is symlinked into the sandbox's vendor directory, so the
        // sandbox autoloader is the one that can resolve both Craft and the
        // plugin. A relative bootstrap path cannot.
        $config = file_get_contents($this->_root() . '/phpunit.xml');

        $this->assertStringContainsString('bootstrap="/var/www/html/vendor/autoload.php"', $config);
    }

    public function testConfigDeclaresTheUnitSuite(): void
    {
        $config = file_get_contents($this->_root() . '/phpunit.xml');

        $this->assertStringContainsString('<testsuite name="unit">', $config);
        $this->assertStringContainsString('<directory>tests/unit</directory>', $config);
    }

    // ========================================================================= //
    // Nothing is skipped
    // ========================================================================= //

    /**
     * The suite excludes nothing, and should stay that way.
     *
     * An exclusion is how a directory of unrunnable tests survives in a
     * repository: the files keep their `Test.php` names, the runner is told to
     * look away, and nobody notices they are dead. Deleting is the honest
     * alternative, so the absence of an exclusion is the guard.
     */
    public function testTheSuiteExcludesNothing(): void
    {
        $config = file_get_contents($this->_root() . '/phpunit.xml');

        $this->assertStringNotContainsString(
            '<exclude>',
            $config,
            'phpunit.xml has gained an exclusion. Whatever it skips is either dead, '
            . 'in which case delete it, or alive, in which case fix it.'
        );
    }

    public function testTestsDirectoryHoldsOnlyTheUnitSuite(): void
    {
        $entries = array_values(array_diff(scandir($this->_root() . '/tests'), ['.', '..', '.DS_Store']));

        $this->assertSame(['unit'], $entries, sprintf(
            'Unexpected entries under tests/: %s',
            implode(', ', $entries)
        ));
    }

    public function testUnitDirectoryHoldsOnlyTestsAndItsReadme(): void
    {
        $entries = array_values(array_diff(scandir(__DIR__), ['.', '..', '.DS_Store']));
        $unexpected = array_filter(
            $entries,
            static fn(string $e): bool => 'README.md' !== $e && !str_ends_with($e, 'Test.php')
        );

        $this->assertSame([], array_values($unexpected), sprintf(
            'Unexpected entries under tests/unit/: %s',
            implode(', ', $unexpected)
        ));
    }

    public function testPhpunitIsTheOnlyTestFramework(): void
    {
        $composer = json_decode(file_get_contents($this->_root() . '/composer.json'), true);
        $dev = array_keys($composer['require-dev'] ?? []);

        $this->assertContains('phpunit/phpunit', $dev);

        // A second framework means two ways to run the tests, and one of them
        // will quietly stop being run.
        foreach ($dev as $package) {
            $this->assertStringNotContainsString('test-framework', $package);
        }
    }

    // ========================================================================= //
    // Composer wiring
    // ========================================================================= //

    public function testComposerRequiresPhpunit(): void
    {
        $composer = json_decode(file_get_contents($this->_root() . '/composer.json'), true);

        $this->assertArrayHasKey('phpunit/phpunit', $composer['require-dev']);
    }

    public function testComposerAutoloadsTheTestNamespace(): void
    {
        $composer = json_decode(file_get_contents($this->_root() . '/composer.json'), true);

        $this->assertSame(
            'tests/',
            $composer['autoload-dev']['psr-4']['doublesecretagency\\googlemaps\\tests\\']
        );
    }

    public function testComposerExposesATestScript(): void
    {
        $composer = json_decode(file_get_contents($this->_root() . '/composer.json'), true);

        $this->assertSame('phpunit', $composer['scripts']['test']);
    }

    public function testThePhpunitCacheIsIgnored(): void
    {
        $gitignore = file_get_contents($this->_root() . '/.gitignore');

        $this->assertStringContainsString('.phpunit.cache', $gitignore);
    }

    // ========================================================================= //
    // The suite collects what it should
    // ========================================================================= //

    public function testTheSuiteHasAMeaningfulNumberOfTestFiles(): void
    {
        // Guards against an exclusion pattern widening and silently emptying
        // the suite, which would report green.
        $files = glob(__DIR__ . '/*Test.php');

        $this->assertGreaterThanOrEqual(
            14,
            count($files),
            'Fewer test files than expected. Check whether an exclusion in '
            . 'phpunit.xml has started matching the real suite.'
        );
    }

    /**
     * Every test file this suite expects to exist.
     *
     * Named explicitly rather than counted, so deleting one is a failure rather
     * than a smaller number.
     *
     * @dataProvider expectedTestFileProvider
     */
    public function testExpectedTestFileExists(string $file): void
    {
        $this->assertTrue(file_exists(__DIR__ . "/{$file}"), "Missing test file: {$file}");
    }

    public static function expectedTestFileProvider(): array
    {
        $files = [
            'AddressFieldNormalizationTest.php',
            'AddressFieldShapeTest.php',
            'AddressModelTest.php',
            'AddressStoreBindingsTest.php',
            'AssetBundlesTest.php',
            'ControlPanelSurfaceTest.php',
            'DefaultsEnumTest.php',
            'FieldConversionTest.php',
            'FieldTemplatesTest.php',
            'GeocodingHelperRestructureTest.php',
            'GoogleConstantsTest.php',
            'GoogleMapsFacadeTest.php',
            'InstallMigrationTest.php',
            'LocationModelTest.php',
            'MapHelperTest.php',
            'PluginRegistrationTest.php',
            'ProximitySearchTest.php',
            'SettingsModelTest.php',
            'SuiteIntegrityTest.php',
            'UniversalMethodsParityTest.php',
        ];
        return array_combine($files, array_map(static fn(string $f): array => [$f], $files));
    }

    /**
     * 🛑 The list above must name every file on disk.
     *
     * Without this, adding a test file and forgetting to list it means the
     * per-file checks below silently skip it, and the suite is smaller than it
     * reports. A count would not catch a swap.
     */
    public function testTheExpectedFileListNamesEveryTestFileOnDisk(): void
    {
        $onDisk = array_map('basename', glob(__DIR__ . '/*Test.php'));
        $listed = array_keys(self::expectedTestFileProvider());

        sort($onDisk);
        sort($listed);

        $this->assertSame($listed, $onDisk, sprintf(
            'The expected-file list is out of step with the directory. '
            . 'Unlisted: %s. Listed but absent: %s.',
            implode(', ', array_diff($onDisk, $listed)) ?: 'none',
            implode(', ', array_diff($listed, $onDisk)) ?: 'none'
        ));
    }

    /**
     * @dataProvider expectedTestFileProvider
     */
    public function testEveryTestFileUsesTheSuiteNamespace(string $file): void
    {
        $source = file_get_contents(__DIR__ . "/{$file}");

        $this->assertStringContainsString(
            'namespace doublesecretagency\googlemaps\tests\unit;',
            $source,
            "{$file} is outside the suite namespace, so Composer cannot autoload it."
        );
    }

    /**
     * @dataProvider expectedTestFileProvider
     */
    public function testEveryTestFileExtendsTheRealTestCase(string $file): void
    {
        $this->assertStringContainsString(
            'use PHPUnit\Framework\TestCase;',
            file_get_contents(__DIR__ . "/{$file}")
        );

        // Asserted through reflection rather than by searching the source for a
        // parent class name, which would flag this very file.
        $class = __NAMESPACE__ . '\\' . basename($file, '.php');

        $this->assertTrue(class_exists($class), "Could not autoload {$class}.");
        $this->assertSame(
            TestCase::class,
            get_parent_class($class),
            "{$class} does not extend PHPUnit's TestCase."
        );
    }

    // ========================================================================= //
    // The suite does not bootstrap Craft
    // ========================================================================= //

    /**
     * 🛑 The whole reason this suite runs in milliseconds.
     *
     * Booting Craft would make it slower, order-dependent, and dependent on a
     * database. Any test needing a container belongs in the sandbox as a manual
     * check instead.
     *
     * @dataProvider expectedTestFileProvider
     */
    public function testNoTestFileBootstrapsCraft(string $file): void
    {
        // 🛑 Code only, with comments and string literals stripped.
        //
        // Several tests legitimately quote `Craft::$app` while asserting what a
        // SOURCE FILE contains, and this file names the forbidden tokens in its
        // own list. A raw text search flags all of them, which is the documented
        // hazard of a source-level test reading its own prose.
        //
        $code = $this->_codeOnly(file_get_contents(__DIR__ . "/{$file}"));

        foreach (['Craft::$app', 'CRAFT_BASE_PATH', 'bootstrap/console.php', 'bootstrap/web.php'] as $forbidden) {
            $this->assertStringNotContainsString(
                $forbidden,
                $code,
                "{$file} calls `{$forbidden}`. This suite must run without a Craft container."
            );
        }
    }

    /**
     * Strip comments and string literals, leaving only executable code.
     *
     * Without this, an assertion ABOUT a string is indistinguishable from code
     * that uses it.
     */
    private function _codeOnly(string $source): string
    {
        $keep = '';

        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $keep .= $token;
                continue;
            }

            [$id, $text] = $token;

            if (in_array($id, [
                T_COMMENT,
                T_DOC_COMMENT,
                T_CONSTANT_ENCAPSED_STRING,
                T_ENCAPSED_AND_WHITESPACE,
            ], true)) {
                continue;
            }

            $keep .= $text;
        }

        return $keep;
    }

    public function testTheCodeStripperRemovesStringsAndComments(): void
    {
        // A known positive and a known negative, so the stripper cannot pass
        // by returning everything or nothing.
        $sample = <<<'PHP'
        <?php
        // Craft::$app in a comment
        $a = 'Craft::$app in a string';
        $b = SomeClass::method();
        PHP;

        $stripped = $this->_codeOnly($sample);

        $this->assertStringNotContainsString('Craft::$app', $stripped);
        $this->assertStringContainsString('SomeClass::method', $stripped);
    }

    public function testCraftIsNotBootedDuringTheSuite(): void
    {
        // A positive check on the running process rather than on the source.
        $this->assertFalse(
            class_exists('Craft', false),
            'Craft has been loaded into the test process. Something in the suite is '
            . 'bootstrapping the application.'
        );
    }

    // ========================================================================= //
    // Documented limits
    // ========================================================================= //

    public function testTheSuiteCarriesAReadme(): void
    {
        // It records the three categories, the no-Craft boundary, and what the
        // suite cannot see, none of which is derivable from the tests.
        $readme = $this->_root() . '/tests/unit/README.md';

        $this->assertTrue(file_exists($readme), 'tests/unit/README.md should exist.');

        $contents = file_get_contents($readme);

        foreach (['Pure unit', 'Reflection and shape', 'Source-level', 'does not catch'] as $section) {
            $this->assertStringContainsString(
                $section,
                $contents,
                "The README no longer covers `{$section}`."
            );
        }
    }
}
