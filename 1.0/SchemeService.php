<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\BillResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\BillMonth;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;
use com\shephertz\app42\paas\sdk\php\appTab\SchemeResponseBuilder;


include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once 'BillMonth.php';
include_once 'BillResponseBuilder.php';
include_once 'App42NotFoundException.php';
include_once 'SchemeResponseBuilder.php';

class SchemeService {

    protected $resource = "scheme";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * The costructor for the Service
     * @param  apiKey
     * @param  secretKey
     * @param  baseURL
     *
     */
    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * @param aName
     * @param schemeName
     * @param desc
     * @return
     */
    function createScheme($schemeName, $desc) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($desc, "Description");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"app":{"scheme":{"description":"' . $desc . '","name":"' . $schemeName . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $schemeObj = new SchemeResponseBuilder();
            $result = $schemeObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $result;
    }

    function createSchemeWithSchemeData($schemeData) {

        Util::throwExceptionIfNullOrBlank($schemeData, "SchemeData");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['schemeName'] = $schemeName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
//          $body = '{"app42":{"app":{"scheme":"' . $chargeUser . '"}}}';
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    function getSchemes() {
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
            $this->url = $this->url;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    /**
     * @param schemeName
     * @return
     */
    function getSchemeByName($schemeName) {
        Util::throwExceptionIfNullOrBlank($schemeData, "SchemeData");

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
            $this->url = $this->url . "/" . $schemeName .
                    $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

}
?>
