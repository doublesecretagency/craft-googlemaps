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

namespace doublesecretagency\googlemaps\controllers;

use Craft;
use craft\errors\DeprecationException;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class DeprecationController
 * @since 5.3.0
 */
class DeprecationController extends Controller
{

    /**
     * Log that Address field autocompletion fell back to the legacy Places API.
     *
     * The fallback is detected in the browser, so the Address field reports it here.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionLegacyPlaces(): Response
    {
        // Only accept a JSON POST request
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        try {
            // Log a deprecation warning (logged once, regardless of how often it occurs)
            Craft::$app->getDeprecator()->log(
                'googlemaps.legacyPlacesApi',
                'Address field autocompletion is using the legacy Places API, because "Places API (New)" is not enabled for the Google Maps browser key. Please enable "Places API (New)" in your Google Cloud project. The legacy Places API is not available to new Google Cloud projects, and will eventually be discontinued.',
                __FILE__,
                __LINE__
            );
        } catch (DeprecationException) {
            // The deprecator is configured to throw, don't break the field over it
            return $this->asJson(['success' => false]);
        }

        // Return success
        return $this->asJson(['success' => true]);
    }

}
