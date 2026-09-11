<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\db\Migration;
use doublesecretagency\googlemaps\enums\Defaults;
use doublesecretagency\googlemaps\migrations\FromScratch;
use doublesecretagency\googlemaps\migrations\FromSmartMap;
use doublesecretagency\googlemaps\migrations\Install;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for the install migration and the schema it creates.
 *
 * Address data lives in its own table rather than in element content, so
 * `googlemaps_addresses` is the plugin's entire persistence layer. Renaming a
 * column, dropping an index, or losing a cascading foreign key is a
 * data-integrity hazard that no other test would notice.
 *
 * Note that `Install` is a router rather than the schema. It calls
 * `FromScratch::update()` to build the table, then `FromSmartMap::update()` if
 * the legacy table is present, then drops the legacy table. So a schema change
 * means editing FromScratch, and these tests read it there.
 */
class InstallMigrationTest extends TestCase
{
    private string $installSource;
    private string $schemaSource;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/migrations';

        foreach (['Install', 'FromScratch'] as $file) {
            $this->assertTrue(file_exists("{$dir}/{$file}.php"), "{$file}.php should exist in {$dir}");
        }

        $this->installSource = file_get_contents("{$dir}/Install.php");
        $this->schemaSource = file_get_contents("{$dir}/FromScratch.php");
        $this->reflection = new ReflectionClass(Install::class);
    }

    // ========================================================================= //
    // Install, the router
    // ========================================================================= //

    public function testExtendsMigration(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Migration::class));
    }

    public function testHasSafeUpAndSafeDown(): void
    {
        $this->assertTrue($this->reflection->hasMethod('safeUp'));
        $this->assertTrue($this->reflection->hasMethod('safeDown'));
    }

    public function testNamesBothAddressTables(): void
    {
        $this->assertSame('{{%googlemaps_addresses}}', Install::GM_ADDRESSES);
        $this->assertSame('{{%smartmap_addresses}}', Install::SM_ADDRESSES);
    }

    public function testDelegatesTheSchemaToFromScratch(): void
    {
        $this->assertStringContainsString('FromScratch::update($this)', $this->installSource);
    }

    public function testMigratesFromSmartMapOnlyWhenTheLegacyTableExists(): void
    {
        $this->assertMatchesRegularExpression(
            '/if \(!\$this->db->tableExists\(static::SM_ADDRESSES\)\)/',
            $this->installSource
        );
        $this->assertStringContainsString('FromSmartMap::update()', $this->installSource);
    }

    public function testDropsTheLegacyTableAfterMigrating(): void
    {
        $this->assertStringContainsString('$this->dropTableIfExists(static::SM_ADDRESSES);', $this->installSource);
    }

    public function testUninstallDropsOnlyThePluginsOwnTable(): void
    {
        $this->assertMatchesRegularExpression(
            '/public function safeDown\(\)[\s\S]{0,200}dropTableIfExists\(static::GM_ADDRESSES\)/',
            $this->installSource
        );
    }

    public function testBothMigrationHelpersExist(): void
    {
        $this->assertTrue(class_exists(FromScratch::class));
        $this->assertTrue(class_exists(FromSmartMap::class));
    }

    // ========================================================================= //
    // The schema
    // ========================================================================= //

    /**
     * @dataProvider columnProvider
     */
    public function testTableHasColumn(string $column, string $type): void
    {
        $this->assertMatchesRegularExpression(
            "/'{$column}'\s*=>\s*static::\\\$_migration->{$type}\(/",
            $this->schemaSource,
            "Column `{$column}` is missing or is no longer a {$type}."
        );
    }

    public static function columnProvider(): array
    {
        return [
            'id'           => ['id', 'primaryKey'],
            'elementId'    => ['elementId', 'integer'],
            'siteId'       => ['siteId', 'integer'],
            'fieldId'      => ['fieldId', 'integer'],
            'formatted'    => ['formatted', 'string'],
            'raw'          => ['raw', 'text'],
            'name'         => ['name', 'string'],
            'street1'      => ['street1', 'string'],
            'street2'      => ['street2', 'string'],
            'city'         => ['city', 'string'],
            'state'        => ['state', 'string'],
            'zip'          => ['zip', 'string'],
            'neighborhood' => ['neighborhood', 'string'],
            'county'       => ['county', 'string'],
            'country'      => ['country', 'string'],
            'countryCode'  => ['countryCode', 'string'],
            'placeId'      => ['placeId', 'string'],
            'zoom'         => ['zoom', 'tinyInteger'],
            'dateCreated'  => ['dateCreated', 'dateTime'],
            'dateUpdated'  => ['dateUpdated', 'dateTime'],
            'uid'          => ['uid', 'uid'],
        ];
    }

    public function testRawIsATextColumnRatherThanAString(): void
    {
        // A complete Google API response runs past 1000 characters, so a
        // varchar would truncate it.
        $this->assertMatchesRegularExpression(
            "/'raw'\s*=>\s*static::\\\$_migration->text\(\)/",
            $this->schemaSource
        );
    }

    public function testCoordinatesHaveEnoughPrecision(): void
    {
        // decimal(12,8) holds eight decimal places, roughly a millimetre.
        // Anything narrower silently rounds pins away from their location.
        foreach (['lat', 'lng'] as $column) {
            $this->assertMatchesRegularExpression(
                "/'{$column}'\s*=>\s*static::\\\$_migration->decimal\(12, ?8\)/",
                $this->schemaSource,
                "Column `{$column}` no longer has decimal(12,8) precision."
            );
        }
    }

    public function testTheOwnershipColumnsAreNotNullable(): void
    {
        // A row that belongs to no element, site or field is unreachable and
        // uncollectable.
        foreach (['elementId', 'siteId', 'fieldId'] as $column) {
            $this->assertMatchesRegularExpression(
                "/'{$column}'\s*=>\s*static::\\\$_migration->integer\(\)->notNull\(\)/",
                $this->schemaSource,
                "Column `{$column}` is nullable."
            );
        }
    }

    public function testEverySubfieldHandleHasAColumn(): void
    {
        // The contract between the field's configuration and its storage.
        foreach (array_column(Defaults::SUBFIELDCONFIG, 'handle') as $handle) {
            $this->assertMatchesRegularExpression(
                "/'{$handle}'\s*=>\s*static::\\\$_migration->/",
                $this->schemaSource,
                "Subfield `{$handle}` has no column."
            );
        }
    }

    // ========================================================================= //
    // Indexes
    // ========================================================================= //

    /**
     * @dataProvider indexProvider
     */
    public function testIndexExists(string $columns, bool $unique): void
    {
        $suffix = $unique ? ', true' : '';

        $this->assertMatchesRegularExpression(
            "/createIndex\(null, Install::GM_ADDRESSES, \[{$columns}\]{$suffix}\)/",
            $this->schemaSource,
            "Missing index on [{$columns}]."
        );
    }

    public static function indexProvider(): array
    {
        return [
            'elementId'                     => ["'elementId'", false],
            'siteId'                        => ["'siteId'", false],
            'fieldId'                       => ["'fieldId'", false],
            'siteId + fieldId'              => ["'siteId', 'fieldId'", false],
            'elementId + siteId'            => ["'elementId', 'siteId'", false],
            'elementId + fieldId'           => ["'elementId', 'fieldId'", false],
            'elementId + siteId + fieldId'  => ["'elementId', 'siteId', 'fieldId'", true],
        ];
    }

    public function testOneAddressPerElementSiteAndFieldIsEnforcedByAUniqueIndex(): void
    {
        // Without this a save could append rather than replace, and a field
        // would read back whichever duplicate the query happened to order first.
        $this->assertMatchesRegularExpression(
            "/createIndex\(null, Install::GM_ADDRESSES, \['elementId', 'siteId', 'fieldId'\], true\)/",
            $this->schemaSource
        );
    }

    // ========================================================================= //
    // Foreign keys
    // ========================================================================= //

    /**
     * @dataProvider foreignKeyProvider
     */
    public function testForeignKeyCascades(string $column, string $table): void
    {
        $this->assertMatchesRegularExpression(
            "/addForeignKey\(null, Install::GM_ADDRESSES, \['{$column}'\],\s*Table::{$table},\s*\['id'\], 'CASCADE'\)/",
            $this->schemaSource,
            "Foreign key on `{$column}` is missing or no longer cascades. Without "
            . 'the cascade, deleting the parent leaves an orphaned address row.'
        );
    }

    public static function foreignKeyProvider(): array
    {
        return [
            'elements' => ['elementId', 'ELEMENTS'],
            'sites'    => ['siteId', 'SITES'],
            'fields'   => ['fieldId', 'FIELDS'],
        ];
    }

    public function testForeignKeysUseCraftsTableConstants(): void
    {
        // Rather than raw `{{%elements}}` strings, which drift silently.
        $this->assertStringContainsString('use craft\db\Table;', $this->schemaSource);
    }

    // ========================================================================= //
    // The schema version
    // ========================================================================= //

    public function testPluginDeclaresASchemaVersion(): void
    {
        $plugin = file_get_contents(dirname(__DIR__, 2) . '/src/GoogleMapsPlugin.php');

        $this->assertMatchesRegularExpression(
            "/public string \\\$schemaVersion = '\d+\.\d+\.\d+';/",
            $plugin,
            'Craft compares this against the stored value to decide whether '
            . 'migrations need to run.'
        );
    }

    public function testEveryDatedMigrationFollowsTheNamingConvention(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/migrations';

        foreach (glob("{$dir}/m*.php") as $path) {
            $this->assertMatchesRegularExpression(
                '/^m\d{6}_\d{6}_[a-z0-9_]+\.php$/',
                basename($path),
                'Craft discovers migrations by filename, so a malformed one never runs.'
            );
        }
    }

    public function testEveryDatedMigrationDeclaresSafeUp(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/migrations';

        foreach (glob("{$dir}/m*.php") as $path) {
            $this->assertStringContainsString(
                'function safeUp()',
                file_get_contents($path),
                basename($path) . ' has no safeUp().'
            );
        }
    }
}
