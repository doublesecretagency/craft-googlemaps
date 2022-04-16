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
use craft\web\Controller;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class LookupController
 * @since 4.0.0
 */
class LookupController extends Controller
{

    /**
     * @inheritdoc
     */
    protected array|bool|int $allowAnonymous = true;

    /**
     * Returns all results from geocoding lookup.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAll(): Response
    {
        return $this->_performLookup('all');
    }

    /**
     * Returns first result from geocoding lookup.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionOne(): Response
    {
        return $this->_performLookup('one');
    }

    /**
     * Returns coordinates of first result from geocoding lookup.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionCoords(): Response
    {
        return $this->_performLookup('coords');
    }

    // ========================================================================= //

    /**
     * Perform the actual geocoding lookup.
     * Returns the results based on specified format.
     *
     * @param string $format
     * @return Response
     * @throws BadRequestHttpException
     */
    private function _performLookup(string $format): Response
    {
        // Must be a POST request
        $this->requirePostRequest();

        // Get the specified target
        $target = Craft::$app->getRequest()->getBodyParam('target');

        // Configure Lookup Model based on given target
        $lookup = GoogleMaps::lookup($target);

        // If something went wrong, return an error
        if (!$lookup) {
            return $this->asJson([
                'success' => false,
                'error' => "Invalid target. Unable to perform geocoding lookup.",
                'results' => null
            ]);
        }

        // Perform geocoding lookup
        switch ($format) {
            case 'all':
                $results = $lookup->all();
                break;
            case 'one':
                $results = $lookup->one();
                break;
            case 'coords':
                $results = $lookup->coords();
                break;
        }

        // If something went wrong, return an error
        if (null === $results) {
            // Except the one error message that isn't actually an error
            $exception = 'The geocode was successful but returned no results.';
            // But for any other reason, return an error
            if ($exception !== $lookup->error) {
                return $this->asJson([
                    'success' => false,
                    'error' => $lookup->error,
                    'results' => null
                ]);
            }
        }

        // If no results and we wanted `all`
        if (!$results && ('all' === $format)) {
            // Convert null response to an empty array
            $results = [];
        }

        // Return successful results
        return $this->asJson([
            'success' => true,
            'error' => null,
            'results' => $results
        ]);
    }

}
