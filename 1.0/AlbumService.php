<?php

namespace com\shephertz\app42\paas\sdk\php\gallery;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\gallery\AlbumResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'AlbumResponseBuilder.php';
include_once 'App42Response.php';

/**
 * Create Photo Gallery on the cloud. This service allows to manage i.e. create,
 * retrieve and remove albums on the cloud. Its useful for Mobile/Device App and
 * Web App developer who want Photo Gallery functionality. It gives them a
 * complete Photo Gallery out of the box and reduces the footprint on the
 * device. Developers can focus on how the Photo Gallery will be rendered and
 * this Cloud API will manage the Gallery on the cloud thereby reducing
 * development time.
 * 
 * @see Album
 * @see Photo
 */
class AlbumService {

    private $version = "1.0";
    private $resource = "gallery";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * The costructor for the Service
     *
     * @params apiKey
     * @params secretKey
     * @params baseURL
     *
     */
    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Creates Album on the cloud
     *
     * @params userName
     *            - The user to which the album belongs
     * @params albumName
     *            - Name of the album to be created on the cloud
     * @params albumDescription
     *            - Description of the album to be created
     *
     * @return Album object containing the album which has been created
     */
    function createAlbum($userName, $albumName, $albumDescription) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        Util::throwExceptionIfNullOrBlank($albumDescription, "Description");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"album":{"name":"' . $albumName . '","description":"' . $albumDescription . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/album/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $albumResponseObj = new AlbumResponseBuilder();
            $albumObj = $albumResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumObj;
    }

    /**
     * Fetches all the Albums based on the userName
     *
     * @params userName
     *            - The user for which the albums have to be fetched
     *
     * @return List of Album object containing all the album for the given
     *         userName
     *
     * @throws App42Exception
     *
     */
    function getAlbums($userName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            $encodedUserName = Util::encodeParams($userName);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['userName'] = $userName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/album/" . $encodedUserName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $albumResponseObj = new AlbumResponseBuilder();
                $albumObj = $albumResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $albumObj;
        } else {

            /**
             * Fetches all the Albums based on the userName by Paging.
             *
             * @params userName
             *            - The user for which the albums have to be fetched
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return List of Album object containing all the album for the given
             *         userName
             */
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedUserName = Util::encodeParams($userName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);

            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['userName'] = $userName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/album/" . $encodedUserName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $albumResponseObj = new AlbumResponseBuilder();
                $albumObj = $albumResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $albumObj;
        }
    }

    /**
     * Fetches the count of all the Albums based on the userName
     *
     * @params userName
     *            - The user for which the count of albums have to be fetched
     *
     * @return App42Response object containing the count of all the albums for
     *         the given userName
     */
    function getAlbumsCount($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $albumObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/album/" . $encodedUserName . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $albumObj->setStrResponse($response->getResponse());
            $albumObj->setResponseSuccess(true);
            $albumResponseObj = new AlbumResponseBuilder();
            $albumObj->setTotalRecords($albumResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumObj;
    }

    /**
     * Fetch all Albums based on userName and albumName
     *
     * @params userName
     *            - The user for which the album has to be fetched
     * @params albumName
     *            - Name of the album that has to be fetched
     *
     * @return Album object containing album information for the given userName
     *         and albumName
     */
    function getAlbumByName($userName, $albumName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedAlbumName = Util::encodeParams($albumName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['albumName'] = $albumName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/album/" . $encodedUserName . "/" . $encodedAlbumName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $albumResponseObj = new AlbumResponseBuilder();
            $albumObj = $albumResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumObj;
    }

    /**
     * Removes a particular album based on the userName and albumName. Note: All
     * photos added to this Album will also be removed
     *
     * @params userName
     *            - The user for which the album has to be removed
     * @params albumName
     *            - Name of the album that has to be removed
     *
     * @return App42Response if removed successfully
     */
    function removeAlbum($userName, $albumName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedAlbumName = Util::encodeParams($albumName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['albumName'] = $albumName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedAlbumName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $albumResponseObj = new AlbumResponseBuilder();
            $albumObj = $albumResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($albumObj);
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