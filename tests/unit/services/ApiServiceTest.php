<?php
namespace doublesecretagency\googlemaps\tests;

use Codeception\Test\Unit;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
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
        $key = GoogleMapsPlugin::$plugin->api->getServerKey();

        $this->assertSame($key, $testKey);
    }

    public function testSetAndGetTheBrowserKey()
    {
        $testKey = time(); // Random test key
        GoogleMapsPlugin::$plugin->api->setBrowserKey($testKey);
        $key = GoogleMapsPlugin::$plugin->api->getBrowserKey();

        $this->assertSame($key, $testKey);
    }

}
