<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\helpers\ApiHelper;
use doublesecretagency\googlemaps\helpers\GeolocationHelper;
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
        $ip = GeolocationHelper::$ip;

        self::assertSame('1.1.1.1', $ip); // Assuming always run on LD laptop
    }

//    public function testCanPerformGeolocation()
//    {
////        $testKey = time(); // Random test key
////        ApiHelper::setServerKey($testKey);
////        $key = GoogleMaps::getServerKey();
//
//        self::assertSame($key, $testKey);
//    }

}
