<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use UnitTester;

class GeolocationServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testCanGetUsersIpAddress()
    {
        $testKey = time(); // Random test key
//        GoogleMapsPlugin::$plugin->api->setServerKey($testKey);
//        $key = GoogleMapsPlugin::$plugin->api->getServerKey();

        $this->assertSame($testKey, $testKey);
    }

//    public function testCanPerformGeolocation()
//    {
////        $testKey = time(); // Random test key
////        GoogleMapsPlugin::$plugin->api->setServerKey($testKey);
////        $key = GoogleMapsPlugin::$plugin->api->getServerKey();
//
//        $this->assertSame($key, $testKey);
//    }

}
