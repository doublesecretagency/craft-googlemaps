<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\migrations\m240530_122024_multisite_support;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for the 4.6.0 multisite migration (#139).
 *
 * This migration still runs for any site upgrading from 4.5.x or earlier, and
 * it used to fail in three ways, all reproduced against real MySQL and Postgres
 * installs on 2026-11-06:
 *
 * - Before 4.6 nothing prevented two rows for the same element and field. The
 *   unique (elementId, siteId, fieldId) index is built before `siteId` is
 *   filled in, so Phase 2 gave a leftover duplicate the same site as its twin
 *   and the whole migration failed with an integrity violation.
 * - On MySQL a failed run is not rolled back (DDL commits implicitly), and each
 *   retry created another full set of randomly named indexes.
 * - On Postgres, `LIKE` against the `jsonb` columns `fieldlayouts.config` and
 *   `elements_sites.content` is an error, so every Postgres site with an
 *   Address field failed in Phase 1, duplicates or not.
 *
 * The live checks need a database, so these tests pin the shape of each fix.
 */
class MultisiteMigrationTest extends TestCase
{
    private ReflectionClass $reflection;
    private string $source;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(m240530_122024_multisite_support::class);
        $this->source = file_get_contents($this->reflection->getFileName());
    }

    private function _methodBody(string $method): string
    {
        $reflection = $this->reflection->getMethod($method);
        $lines = file($this->reflection->getFileName());

        return implode('', array_slice(
            $lines,
            $reflection->getStartLine() - 1,
            $reflection->getEndLine() - $reflection->getStartLine() + 1
        ));
    }

    // ========================================================================= //
    // Duplicate rows
    // ========================================================================= //

    public function testDuplicatesAreRemovedBeforeTheIndexesAreBuilt(): void
    {
        $safeUp = $this->_methodBody('safeUp');

        $columnsAt = strpos($safeUp, '_newColumns()');
        $removeAt = strpos($safeUp, '_removeDuplicates()');
        $indexesAt = strpos($safeUp, '_newIndexes()');

        $this->assertNotFalse($removeAt, 'safeUp() no longer removes duplicate Addresses.');
        $this->assertLessThan($removeAt, $columnsAt, 'The siteId column must exist before duplicates are found by it.');
        $this->assertLessThan($indexesAt, $removeAt, 'Duplicates must be gone before the unique index is built.');
    }

    public function testOnlyUnassignedDuplicatesAreRemoved(): void
    {
        // Rows that already have a site came from 4.6+ and are unique by construction
        $body = $this->_methodBody('_removeDuplicates');

        $this->assertStringContainsString("'[[siteId]]' => null", $body);
        $this->assertStringContainsString("having('COUNT(*) > 1')", $body);
    }

    public function testTheMostRecentlyUpdatedDuplicateIsKept(): void
    {
        $body = $this->_methodBody('_removeDuplicates');

        $this->assertStringContainsString("'[[dateUpdated]]' => SORT_DESC, '[[id]]' => SORT_DESC", $body);
        $this->assertStringContainsString('array_shift($ids);', $body);
    }

    public function testBothLaterPhasesAssignThroughTheCollisionCheck(): void
    {
        // Phase 1, or a failed earlier run, can already have given a twin a site
        $this->assertStringContainsString('$this->_assignSite($address, $siteIds[0]);', $this->_methodBody('_phase2'));
        $this->assertStringContainsString('$this->_assignSite($address, $primarySiteId);', $this->_methodBody('_phase3'));
        $this->assertStringContainsString("['not', ['[[siteId]]' => null]]", $this->_methodBody('_assignSite'));

        $assign = $this->_methodBody('_assignSite');
        $existsAt = strpos($assign, '->exists()');
        $deleteAt = strpos($assign, '$this->delete(');
        $updateAt = strpos($assign, '$this->update(');

        $this->assertNotFalse($existsAt);
        $this->assertLessThan($updateAt, $deleteAt, 'A stale copy must be deleted, not assigned.');
        $this->assertLessThan($deleteAt, $existsAt);
    }

    public function testPhase3NoLongerUpdatesEveryNullRowAtOnce(): void
    {
        $this->assertStringNotContainsString(
            "['[[siteId]]' => \$primarySiteId],\n            ['[[siteId]]' => null]",
            $this->_methodBody('_phase3')
        );
    }

    // ========================================================================= //
    // Every site keeps the Address
    // ========================================================================= //

    public function testEverySiteOfTheElementGetsACopy(): void
    {
        // Before 4.6 one Address was shared by every site, so no site may lose it
        $populate = $this->_methodBody('_populateData');
        $phase3At = strpos($populate, '_phase3()');
        $fillAt = strpos($populate, '_fillMissingSites()');

        $this->assertNotFalse($fillAt, 'Addresses are no longer copied to the other sites.');
        $this->assertLessThan($fillAt, $phase3At, 'Copies must be made after every row has a site.');

        $fill = $this->_methodBody('_fillMissingSites');
        $this->assertStringContainsString('unset($copy[\'id\']);', $fill, 'A copy must not reuse the original ID (#129).');
        $this->assertStringContainsString('StringHelper::UUID()', $fill);
        $this->assertStringContainsString('($rows[$primarySiteId] ?? reset($rows))', $fill);
    }

    public function testThePrimarySiteComesFirst(): void
    {
        $this->assertStringContainsString('usort($siteIds', $this->_methodBody('_elementSiteIds'));
        $this->assertStringContainsString('_elementSiteIds($address[\'elementId\'], $primarySiteId)', $this->_methodBody('_phase2'));
    }

    // ========================================================================= //
    // Retrying after a failed run
    // ========================================================================= //

    public function testIndexesAreOnlyCreatedWhenMissing(): void
    {
        $body = $this->_methodBody('_newIndexes');

        $this->assertStringContainsString('Db::findIndex(Install::GM_ADDRESSES, $columns, $unique, $this->db)', $body);
        $this->assertSame(1, substr_count($body, 'createIndex('), 'Every index should go through the existence check.');
    }

    public function testTheForeignKeyIsOnlyCreatedWhenMissing(): void
    {
        $this->assertStringContainsString(
            "Db::findForeignKey(Install::GM_ADDRESSES, ['siteId'], \$this->db)",
            $this->_methodBody('_newForeignKeys')
        );
    }

    public function testTheSiteColumnIsOnlyAddedWhenMissing(): void
    {
        $this->assertStringContainsString("columnExists(Install::GM_ADDRESSES, 'siteId')", $this->_methodBody('_newColumns'));
    }

    // ========================================================================= //
    // Postgres and large sites
    // ========================================================================= //

    public function testEveryLikeSearchCastsJsonForPostgres(): void
    {
        $this->assertSame(0, preg_match("/\\['like', '\\[\\[/", $this->source), 'A LIKE on a raw JSON column fails on Postgres.');
        $this->assertSame(2, substr_count($this->source, "['like', \$this->_searchable("));

        $searchable = $this->_methodBody('_searchable');
        $this->assertStringContainsString('getIsPgsql()', $searchable);
        $this->assertStringContainsString('AS TEXT', $searchable);
    }

    public function testNoMysqlOnlySyntax(): void
    {
        // PHP's own spaceship operator is fine; only a quoted SQL fragment may not use it
        $this->assertDoesNotMatchRegularExpression('/[\'"][^\'"\n]*<=>[^\'"\n]*[\'"]/', $this->source);
        $this->assertDoesNotMatchRegularExpression('/DELETE\s+\w+\s+FROM/i', $this->source);
    }

    public function testElementSitesAreReadInBatches(): void
    {
        $body = $this->_methodBody('_migrate');

        $this->assertStringContainsString('Db::each($elementSites)', $body);
        $this->assertStringNotContainsString('->all()', $body);
    }
}
