<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use UnitTester;

class GeolocationServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testCanGetUsersIpAddress()
    {
        $ip = GoogleMapsPlugin::$plugin->geolocation->ip;

        $this->assertSame('1.1.1.1', $ip); // Assuming always run on LD laptop
    }

//    public function testCanPerformGeolocation()
//    {
////        $testKey = time(); // Random test key
////        GoogleMapsPlugin::$plugin->api->setServerKey($testKey);
////        $key = GoogleMaps::getServerKey();
//
//        $this->assertSame($key, $testKey);
//    }

}
