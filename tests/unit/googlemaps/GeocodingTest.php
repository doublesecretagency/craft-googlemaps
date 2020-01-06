<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\models\Address as AddressModel;
use doublesecretagency\googlemaps\models\Lookup as LookupModel;
use UnitTester;

class GeocodingTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testLookupReturnsALookupModel()
    {
        $target = '123 Main St.';
        $lookup = GoogleMaps::lookup($target);

        $this->assertSame(LookupModel::class, get_class($lookup));
    }

    public function testLookupDotAllReturnsAnArrayOfAddressModels()
    {
        $target = '123 Main St.';
        $all = GoogleMaps::lookup($target)->all();

        self::assertIsArray($all);
        $this->assertSame(AddressModel::class, get_class($all[0]));
    }

    public function testLookupDotOneReturnsASingleAddressModel()
    {
        $target = '123 Main St.';
        $one = GoogleMaps::lookup($target)->one();

        $this->assertSame(AddressModel::class, get_class($one));
    }

    public function testLookupDotCoordsReturnsASingleSetOfCoordinates()
    {
        $target = '123 Main St.';
        $coords = GoogleMaps::lookup($target)->coords();

        self::assertIsArray($coords);
        $this->assertSame(2, count($coords));
        $this->assertArrayHasKey('lat', $coords);
        $this->assertArrayHasKey('lng', $coords);
    }

}
