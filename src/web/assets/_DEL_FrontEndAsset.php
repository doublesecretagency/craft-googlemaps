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

namespace doublesecretagency\googlemaps\web\assets;

use craft\web\AssetBundle;

/**
 * Class FrontEndAsset
 * @since 4.0.0
 */
class DELFrontEndAsset extends AssetBundle
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/resources';

        $this->js = [
            'js/smartmap.js',
        ];
    }

}
