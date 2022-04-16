<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by the Google Maps API.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\enums;

/**
 * Class GoogleConstants
 * @since 4.1.1
 */
abstract class GoogleConstants
{

    /**
     * Array of constants, mapped to their replacement types.
     */
    public const TYPES = [
        // Level 1
        'mapTypeId' => 'MapTypeId',
        // Level 2
        'fullscreenControlOptions' => [
            'position' => 'ControlPosition',
        ],
        'mapTypeControlOptions' => [
            'position' => 'ControlPosition',
            'style' => 'MapTypeControlStyle',
        ],
        'motionTrackingControlOptions' => [
            'position' => 'ControlPosition',
        ],
        'panControlOptions' => [
            'position' => 'ControlPosition',
        ],
        'rotateControlOptions' => [
            'position' => 'ControlPosition',
        ],
        'scaleControlOptions' => [
            'style' => 'ScaleControlStyle',
        ],
        'streetViewControlOptions' => [
            'position' => 'ControlPosition',
        ],
        'zoomControlOptions' => [
            'position' => 'ControlPosition',
        ],
    ];

    /**
     * Constant tokens as defined by the Google Maps JavaScript API.
     *
     * Match strings prefixed with `google.maps` (eg: "google.maps.ControlPosition.TOP")
     */
    public const VALUES = [

        'ControlPosition' => [
            // Broad
            'TOP'           =>  2,
            'LEFT'          =>  5,
            'RIGHT'         =>  7,
            'BOTTOM'        => 11,
            // Specific
            'TOP_LEFT'      =>  1,
            'TOP_CENTER'    =>  2,
            'TOP_RIGHT'     =>  3,
            'LEFT_CENTER'   =>  4,
            'LEFT_TOP'      =>  5,
            'LEFT_BOTTOM'   =>  6,
            'RIGHT_TOP'     =>  7,
            'RIGHT_CENTER'  =>  8,
            'RIGHT_BOTTOM'  =>  9,
            'BOTTOM_LEFT'   => 10,
            'BOTTOM_CENTER' => 11,
            'BOTTOM_RIGHT'  => 12,
            'CENTER'        => 13,
        ],

        'MapTypeControlStyle' => [
            'DEFAULT'        => 0,
            'HORIZONTAL_BAR' => 1,
            'DROPDOWN_MENU'  => 2,
            'INSET'          => 3,
            'INSET_LARGE'    => 4,
        ],

        'MapTypeId' => [
            'HYBRID'    => 'hybrid',
            'ROADMAP'   => 'roadmap',
            'SATELLITE' => 'satellite',
            'TERRAIN'   => 'terrain',
        ],

        'ScaleControlStyle' => [
            'DEFAULT' => 0,
        ],

    ];

}
