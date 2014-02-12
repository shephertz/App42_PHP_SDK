<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\BillResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\BillMonth;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once 'BillMonth.php';
include_once 'BillResponseBuilder.php';
include_once 'App42NotFoundException.php';

class SubscribeService {

    protected $resource = "package";
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
     * @param schemeName
     * @param pkgData
     *
     * @return
     */
    function subscribe($uName, $schemeName, $packageName, $renew) {
        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($packageName, "Package Name");
        Util::throwExceptionIfNullOrBlank($renew, "Autorenew");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['uName'] = $uName;
            $params['schemeName'] = $schemeName;
            $params['packageName'] = $packageName;
            $params['renew'] = $renew;
            $signature = urlencode($objUtil->sign($params)); //die();
            $body = null;
//        $body = '{"app42":{"app":{"subscribe":"' . $chargeUser . '"}}}';
            $params['body'] = $body;
            $params['signature'] = $signature;
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

    /**
     * @return
     */
    function getSusbscriptions() {
       
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
     * @param pkgData
     *
     * @return
     */
    function unSusbscriptions($uName, $schemeName, $packageName) {
        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($packageName, "Package Name");
//         $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['uName'] = $uName;
            $params['schemeName'] = $schemeName;
            $params['packageName'] = $packageName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
           $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

}
?>
