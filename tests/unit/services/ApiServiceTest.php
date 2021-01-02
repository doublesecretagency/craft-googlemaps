<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\helpers\ApiHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use UnitTester;

class ApiServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testSetAndGetTheServerKey()
    {
        $testKey = time(); // Random test key
        ApiHelper::setServerKey($testKey);
        $key = GoogleMaps::getServerKey();

        self::assertSame($key, $testKey);
    }

    public function testSetAndGetTheBrowserKey()
    {
        $testKey = time(); // Random test key
        GoogleMaps::setBrowserKey($testKey);
        $key = GoogleMaps::getBrowserKey();

        self::assertSame($key, $testKey);
    }

}
