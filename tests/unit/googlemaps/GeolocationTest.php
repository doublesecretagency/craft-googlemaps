<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\models\Geolocation as GeolocationModel;
use UnitTester;

class GeolocationTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testGetgeolocationReturnsAGeolocationModel()
    {
        $visitor = GoogleMaps::getGeolocation();

        $this->assertSame(GeolocationModel::class, get_class($visitor));
    }

}
