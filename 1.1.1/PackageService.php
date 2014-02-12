<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\BillResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\BillMonth;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;
use com\shephertz\app42\paas\sdk\php\appTab\PackageResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once 'BillMonth.php';
include_once 'BillResponseBuilder.php';
include_once 'App42NotFoundException.php';
include_once 'PackageResponseBuilder.php';

class PackageService {

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
    function createPackage($schemeName, $pkgData) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($pkgData, "PackageDetails");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        $name = $pkgData->getName();
        $description = $pkgData->getDescription();
        $duration = $pkgData->getDuration();
        $price = $pkgData->getPrice();
        $currency = $pkgData->getCurrency();
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"app":{"scheme":{"packageDetails":{"duration":' . $duration . ',"price":' . $price . ',"description":"' . $description . '","name":"' . $name . '","currency":"' . $currency . '"},"name":"' . $schemeName . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            // echo"cre";
            // print_r($response);
//            $packageDataObj = new PackageResponseBuilder();
//            $result = $packageDataObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    function addStorageInToPackage($schemeName, $packageName, $storageName) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($packageName, "PakageName");
        Util::throwExceptionIfNullOrBlank($storageName, "StorageName");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"app":{"packageName":"' . $packageName . '","storageName":"' . $storageName . '","schemeName":"' . $schemeName . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/package" . "/" . $packageName . "/storage" . "/" . $storageName;
            print_r($this->url);
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            print_r($response);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    function addBandwidthInToPackage($schemeName, $packageName, $bandwidthName) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($packageName, "PakageName");
        Util::throwExceptionIfNullOrBlank($bandwidthName, "BandwidthName");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"app":{"packageName":"' . $packageName . '","bandwidthName":"' . $bandwidthName . '","schemeName":"' . $schemeName . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/package" . "/" . $packageName . "/bandwidth" . "/" . $bandwidthName;
            print_r( $this->url);
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            print_r($response);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    function addFeatureInToPackage($schemeName, $packageName, $featureName) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($pakageName, "PakageName");
        Util::throwExceptionIfNullOrBlank($featureName, "featureName");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['schemeName'] = $schemeName;
            $params['pakageName'] = $pakageName;
            $params['featureName'] = $featureName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
            $body = '{"app42":{"app":}';
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/package/" . $packageName . "/feature" . "/" . $featureName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    function createPackageWithUsage($schemeName, $pkgData) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($pkgData, "PackageDetails");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['schemeName'] = $schemeName;
            $params['pakageName'] = $pakageName;
            $params['featureName'] = $featureName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
            $body = '{"app42":{"app":}';
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/package/" . $schemeName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
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
    function getPackages($schemeName) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
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
            $body = '{"app42":{"app":{"scheme":"' . $chargeUser . '"}}}';
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . scheme . "/" . $schemeName;
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
     * @param packageName
     * @return
     */
    function getPackageByPackageName($schemeName, $packageName) {
        Util::throwExceptionIfNullOrBlank($schemeName, "Name");
        Util::throwExceptionIfNullOrBlank($packageName, "Package Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['schemeName'] = $schemeName;
            $params['$packageName'] = $packageName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $packageName . "/" . scheme . "/" . $schemeName;
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
