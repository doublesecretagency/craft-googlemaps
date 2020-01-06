<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\models\Address as AddressModel;
use doublesecretagency\googlemaps\models\Geolocation as GeolocationModel;
use doublesecretagency\googlemaps\models\Lookup as LookupModel;
use UnitTester;

class GoogleMapsHelperTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

//    public function testMethodDynamic()
//    {
//        // dynamic(locations, options)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testMethodStatic()
//    {
//        // static(locations, options)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testMethodSetMarkerIcon()
//    {
//        // setMarkerIcon(mapId, markerId, icon)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testMethodLoadKml()
//    {
//        // loadKml(mapId, kml, options)
//        $this->assertSame(
//            true,
//            true);
//    }

    public function testMethodGetgeolocationReturnsAGeolocationModel()
    {
        $visitor = GoogleMaps::getGeolocation();

        $this->assertSame(GeolocationModel::class, get_class($visitor));
    }

    public function testMethodLookupReturnsALookupModel()
    {
        $target = '123 Main St.';
        $lookup = GoogleMaps::lookup($target);

        $this->assertSame(LookupModel::class, get_class($lookup));
    }

    public function testMethodLookupAllReturnsAnArrayOfAddressModels()
    {
        $target = '123 Main St.';
        $all = GoogleMaps::lookup($target)->all();

        self::assertIsArray($all);
        $this->assertSame(AddressModel::class, get_class($all[0]));
    }

    public function testMethodLookupOneReturnsASingleAddressModel()
    {
        $target = '123 Main St.';
        $one = GoogleMaps::lookup($target)->one();

        $this->assertSame(AddressModel::class, get_class($one));
    }

    public function testMethodLookupCoordsReturnsASingleSetOfCoordinates()
    {
        $target = '123 Main St.';
        $coords = GoogleMaps::lookup($target)->coords();

        self::assertIsArray($coords);
        $this->assertSame(2, count($coords));
        $this->assertArrayHasKey('lat', $coords);
        $this->assertArrayHasKey('lng', $coords);
    }

//    public function testMethodSetServerKey()
//    {
//        // setServerKey('lorem')
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testMethodSetBrowserKey()
//    {
//        // setBrowserKey('ipsum')
//        $this->assertSame(
//            true,
//            true);
//    }

}
