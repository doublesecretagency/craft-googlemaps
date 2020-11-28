<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
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
        GoogleMapsPlugin::$plugin->api->setServerKey($testKey);
        $key = GoogleMaps::getServerKey();

        $this->assertSame($key, $testKey);
    }

    public function testSetAndGetTheBrowserKey()
    {
        $testKey = time(); // Random test key
        GoogleMaps::setBrowserKey($testKey);
        $key = GoogleMaps::getBrowserKey();

        $this->assertSame($key, $testKey);
    }

}
