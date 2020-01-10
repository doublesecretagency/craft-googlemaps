<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\services;

use craft\base\Component;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Class Api
 * @since 4.0.0
 */
class Api extends Component
{

    private $_serverKey;
    private $_browserKey;

    public function getServerKey()
    {
        // Only load once
        if (null === $this->_serverKey) {
            $this->_serverKey = GoogleMapsPlugin::$plugin->getSettings()->serverKey;
        }
        // Return key
        return $this->_serverKey;
    }

    public function getBrowserKey()
    {
        // Only load once
        if (null === $this->_browserKey) {
            $this->_browserKey = GoogleMapsPlugin::$plugin->getSettings()->browserKey;
        }
        // Return key
        return $this->_browserKey;
    }

    public function setServerKey($key)
    {
        $this->_serverKey = $key;
    }

    public function setBrowserKey($key)
    {
        $this->_browserKey = $key;
    }

}
