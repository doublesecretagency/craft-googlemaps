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

    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var string
     */
    private $_target = '123 Main St.';

    // Protected methods
    // =========================================================================

    protected function _before()
    {
        parent::_before();
        GoogleMaps::setServerKey(getenv('GOOGLEMAPS_SERVERKEY'));
    }

    // Public methods
    // =========================================================================

//    public function testDynamic()
//    {
//        // dynamic(locations, options)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testStatic()
//    {
//        // static(locations, options)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testSetMarkerIcon()
//    {
//        // setMarkerIcon(mapId, markerId, icon)
//        $this->assertSame(
//            true,
//            true);
//    }
//
//    public function testLoadKml()
//    {
//        // loadKml(mapId, kml, options)
//        $this->assertSame(
//            true,
//            true);
//    }

    public function testGetvisitorReturnsAGeolocationModel()
    {
        $visitor = GoogleMaps::getVisitor();

        $this->assertSame(GeolocationModel::class, get_class($visitor));
    }

    public function testLookupCreatesALookupModel()
    {
        $lookup = GoogleMaps::lookup($this->_target);

        $this->assertSame(LookupModel::class, get_class($lookup));
    }

    public function testLookupAllReturnsAnArrayOfAddressModels()
    {
        $all = GoogleMaps::lookup($this->_target)->all();

        self::assertIsArray($all);
        $this->assertSame(AddressModel::class, get_class($all[0]));
    }

    public function testLookupOneReturnsASingleAddressModel()
    {
        $one = GoogleMaps::lookup($this->_target)->one();

        self::assertIsObject($one);
        $this->assertSame(AddressModel::class, get_class($one));
    }

    public function testLookupCoordsReturnsASetOfCoordinates()
    {
        $coords = GoogleMaps::lookup($this->_target)->coords();

        self::assertIsArray($coords);
        $this->assertSame(2, count($coords));
        $this->assertArrayHasKey('lat', $coords);
        $this->assertArrayHasKey('lng', $coords);
    }

    public function testSetAndGetTheServerKey()
    {
        $testKey = time(); // Random test key
        GoogleMaps::setServerKey($testKey);
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
