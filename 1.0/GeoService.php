<?php

namespace com\shephertz\app42\paas\sdk\php\geo;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\geo\GeoResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'GeoResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 *
 * Geo Spatial Service on cloud provides the storage, retrieval, querying and
 * updating geo data. One can store the geo data by unique handler on the
 * cloud and can apply search, update and query on it. Geo spatial query
 * includes finding nearby/In circle target point from given point using geo
 * points stored on the cloud.
 *
 * @see Geo
 */
class GeoService {

    private $version = "1.0";
    private $resource = "geo";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * this is a constructor that takes
     *
     * @param apiKey
     * @param secretKey
     * @param baseURL
     *
     */
    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Stores the geopints with unique handler on the cloud. Geo points data
     * contains lat, lng and marker of the point.
     *
     * @param geoStorageName
     *            - Unique handler for storage name
     * @param geoPointsList
     *            - List of Geo Points to be saved
     *
     * @return Geo object containing List of Geo Points that have been saved
     */
    function createGeoPoints($geoStorageName, $geoPointsList) {

        Util::throwExceptionIfNullOrBlank($geoStorageName, "Geo Storage Name");
        Util::throwExceptionIfNullOrBlank($geoPointsList, "Geo Points List");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //$body = '{"app42":{"storage":{"storageName":"'.$geoStorageName.'" , "points": { "point":{"marker":"'.$k->marker.'","lat":"'.$k->lat.'","lng":"'.$k->lng.'"}}}}}';
            if (is_array($geoPointsList)) {
                $body = '{"app42":{ "geo": {"storage":{"storageName":"' . $geoStorageName . '" , "points": { "point": ' . json_encode($geoPointsList) . '}}}}}';
            } else {
                $body = '{"app42":{ "geo": {"storage":{"storageName":"' . $geoStorageName . '" , "points": { "point": "' . $geoPointsList . '"}}}}}';
            }
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/createGeoPoints";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $geoObj;
    }

    /**
     * Search the near by point in given range(In KM) from specified source
     * point. Points to be searched should already be stored on cloud using
     * unique storage name handler.
     *
     * @param storageName
     *            - Unique handler for storage name
     * @param lat
     *            - Latitude of source point
     * @param lng
     *            - Longitude of source point
     * @param distanceInKM
     *            - Range in KM
     *
     * @return Geo object containing the target points in ascending order of
     *         distance from source point.
     *
     */
    function getNearByPointsByMaxDistance($storageName, $lat, $lng, $distanceInKM) {

        Util::throwExceptionIfNullOrBlank($storageName, "Geo Storage Name");
        Util::throwExceptionIfNullOrBlank($lat, "Latitude");
        Util::throwExceptionIfNullOrBlank($lng, "Longitude");
        Util::throwExceptionIfNullOrBlank($distanceInKM, "Distance");
        $encodedStorageName = Util::encodeParams($storageName);
        $encodedLat = Util::encodeParams($lat);
        $encodedLng = Util::encodeParams($lng);
        $encodedDistanceInKM = Util::encodeParams($distanceInKM);

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['storageName'] = $storageName;
            $params['lat'] = $lat;
            $params['lng'] = $lng;
            $params['distanceInKM'] = $distanceInKM;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/getNearByPoints/storageName/" . $encodedStorageName . "/lat/" . $encodedLat . "/lng/" . $encodedLng . "/distanceInKM/" . $encodedDistanceInKM;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $geoObj;
    }

    /**
     * Search the near by point from specified source point. Points to be
     * searched should already be stored on cloud using unique storage name
     * handler.
     *
     * @param storageName
     *            - Unique handler for storage name
     * @param lat
     *            - Latitude of source point
     * @param lng
     *            - Longitude of source point
     * @param resultLimit
     *            - Maximum number of results to be retrieved
     *
     * @return Geo object containing the target points in ascending order of
     *         distance from source point.
     */
    function getNearByPoint($storageName, $lat, $lng, $resultLimit) {

        Util::throwExceptionIfNullOrBlank($storageName, "Geo Storage Name");
        Util::throwExceptionIfNullOrBlank($lat, "Latitude");
        Util::throwExceptionIfNullOrBlank($lng, "Longitude");
        $encodedStorageName = Util::encodeParams($storageName);
        $encodedLat = Util::encodeParams($lat);
        $encodedLng = Util::encodeParams($lng);
        $encodedResultLimit = Util::encodeParams($resultLimit);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['storageName'] = $storageName;
            $params['lat'] = $lat;
            $params['lng'] = $lng;
            $params['resultLimit'] = $resultLimit;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/getNearByPoint/storageName/" . $encodedStorageName . "/lat/" . $encodedLat . "/lng/" . $encodedLng . "/limit/" . $encodedResultLimit;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $geoObj;
    }

    /**
     * Search the near by point from specified source point with in specified
     * radius. Points to be searched should already be stored on cloud using
     * unique storage name handler.
     *
     * @param storageName
     *            - Unique handler for storage name
     * @param lat
     *            - Lattitude of source point
     * @param lng
     *            - Longitude of source point
     * @param radiusInKM
     *            - Radius in KM
     * @param resultLimit
     *            - Maximum number of results to be retrieved
     *
     * @return Geo object containing the target points in ascending order of
     *         distance from source point.
     */
    function getPointsWithInCircle($storageName, $lat, $lng, $radiusInKM, $resultLimit) {
        Util::throwExceptionIfNullOrBlank($storageName, "Geo Storage Name");
        Util::throwExceptionIfNullOrBlank($lat, "Latitude");
        Util::throwExceptionIfNullOrBlank($lng, "Longitude");
        Util::throwExceptionIfNullOrBlank($radiusInKM, "Radius");
        $encodedStorageName = Util::encodeParams($storageName);
        $encodedLat = Util::encodeParams($lat);
        $encodedLng = Util::encodeParams($lng);
        $encodedRadiusInKM = Util::encodeParams($radiusInKM);
        $encodedResultLimit = Util::encodeParams($resultLimit);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['storageName'] = $storageName;
            $params['lat'] = $lat;
            $params['lng'] = $lng;
            $params['resultLimit'] = $resultLimit;
            $params['radiusInKM'] = $radiusInKM;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/getPointsWithInCircle/storageName/" . $encodedStorageName . "/lat/" . $encodedLat . "/lng/" . $encodedLng . "/radiusInKM/" . $encodedRadiusInKM . "/limit/" . $encodedResultLimit;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $geoObj;
    }

    /**
     * Get All Point from storage.
     *
     * @param storageName
     *            - Unique handler for storage name
     *
     * @return Geo object containing all the stored Geo Points for the specified
     *         storage
     */
    function getAllPoints($storageName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");
            $encodedStorageName = Util::encodeParams($storageName);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['storageName'] = $encodedStorageName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/points/" . $storageName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $geoResponseObj = new GeoResponseBuilder();
                $geoObj = $geoResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $geoObj;
        } else {
            /**
             * Get All Point from storage by paging
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Geo object containing all the stored Geo Points for the specified
             * storage
             */
            Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedStorageName = Util::encodeParams($storageName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');

                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['storageName'] = $storageName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/points/" . $encodedStorageName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $geoResponseObj = new GeoResponseBuilder();
                $geoObj = $geoResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $geoObj;
        }
    }

    /**
     * Fetch the name of all storage stored on the cloud.
     *
     * @return Geo object containing List of all the storage created
     *
     */
    function getAllStorage($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/storage";
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $geoResponseObj = new GeoResponseBuilder();
                $geoObj = $geoResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $geoObj;
        } else {
            /**
             * Fetch the name of all storage stored on the cloud by Paging.

             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return Geo object containing List of all the storage created
             */
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging" . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $geoResponseObj = new GeoResponseBuilder();
                $geoObj = $geoResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $geoObj;
        }
    }

    /**
     * Delete the specified Geo Storage from Cloud.
     * 
     * @param storageName
     *            - Unique handler for storage name
     *
     * @return App42Response object containing the name of the storage that has
     *         been deleted
     *
     * @throws App42Exception
     */
    function deleteStorage($storageName) {

        Util::throwExceptionIfNullOrBlank($storageName, "Geo Storage Name");
        $encodedStorageName = Util::encodeParams($storageName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['storageName'] = $storageName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storage/" . $encodedStorageName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($geoObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Delete the specified Geo points from Cloud.
     *
     * @param storageName
     *            - Unique handler for storage name
     *
     * @return App42Response object containing the name of the storage that has
     *         been deleted
     *
     * @throws App42Exception
     */
    function deleteGeoPoints($geoStorageName, $geoPointsList) {

        Util::throwExceptionIfNullOrBlank($geoStorageName, "Geo Storage Name");
        Util::throwExceptionIfNullOrBlank($geoPointsList, "Geo Points List");
        $encodedStorageName = Util::encodeParams($geoStorageName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            //$params['storageName'] = $geoStorageName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            if (is_array($geoPointsList)) {
                $string = '{"app42":{ "geo": {"storage":{"points": { "point": ' . json_encode($geoPointsList) . '}}}}}';
            } else {
                $string = '{"app42":{ "geo": {"storage":{"points": { "point": "' . $geoPointsList . '"}}}}}';
            }
            $params['geoPoints'] = $string;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/points/" . $encodedStorageName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $geoResponseObj = new GeoResponseBuilder();
            $geoObj = $geoResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($geoObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

}

?>