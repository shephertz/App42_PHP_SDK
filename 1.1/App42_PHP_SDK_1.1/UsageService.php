<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\UsageResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\Currency;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'Currency.php';
include_once 'UsageResponseBuilder.php';
include_once 'App42Exception.php';

/**
 *
 * UsageService is part of AppTab which is a rating, metering, charging and
 * billing engine.
 *
 * This service allows app developers to specify the rate for a particular usage
 * parameter. e.g. Level - Storage - space, Bandwidth, Time, Feature, Level of
 * game, OneTime - Which can be used for one time charging e.g. charging for
 * downloads and License for traditional license based charging.
 *
 * It provides methods for first creating the scheme for charging which
 * specifies the unit of charging and the associated price. Subsequently a
 * chargeXXX call has to be made for charging. e.g. If an App developer wants to
 * charge on Storage, He can use the method createStorageCharge and specify that
 * for 10 KB/MB/GB TB the price is 10 USD. Once the scheme is created. The app
 * developer can call the chargeStorage whenever storage is utilized. e.g. 5MB.
 *
 * Using the Bill service the app developer can find out what is the monthly
 * bill for a particular user based on his utilization. The bill is calculated
 * based on scheme which is specified.
 *
 * @see BillService
 *
 */
class UsageService {

    protected $resource = "usage";
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
     * Creates the scheme for level based charging. Level based charging is
     * suited for usage based charging.
     *
     * @param levelName
     *            - The name of the scheme
     * @param levelPrice
     *            - The price of the level scheme
     * @param levelCurrency
     *            - Currency to be used for scheme
     * @param levelDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme
     */
    function createLevelCharge($levelName, $levelPrice, $levelCurreny, $levelDescription) {

        Util::throwExceptionIfNullOrBlank($levelName, "Level Name");
        Util::throwExceptionIfNullOrBlank($levelPrice, "Level Price");
        Util::throwExceptionIfNullOrBlank($levelCurreny, "Level Currency");
        Util::throwExceptionIfNullOrBlank($levelDescription, "Level Description");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($levelCurreny) == "null") {
                throw new App42Exception("The currency with  type '$levelCurreny' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"level":{"name":"' . $levelName . '","price":"' . $levelPrice . '","currency":"' . $levelCurreny . '","description":"' . $levelDescription . '"}}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/level";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme. This method can be used by the app
     * developers to show the pricing plans to their users.
     *
     * @param levelName
     *            - The Name of scheme
     *
     * @returns Scheme Information
     */
    function getLevel($levelName) {

        Util::throwExceptionIfNullOrBlank($levelName, "Level Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $levelName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/level" . "/" . $levelName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Remove a particular scheme. Note: A level is not physically deleted from
     * the storage. Only the state is changed so that it is available to fetch
     * older information.
     *
     * @param levelName
     *            - The name of scheme
     *
     * @returns Scheme Information which has been removed
     */
    function removeLevel($levelName) {
        Util::throwExceptionIfNullOrBlank($levelName, "Level Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $levelName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/level" . "/" . $levelName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Creates the scheme for one time based charging. One Time based charging
     * is suited for downloads. e.g. app, Images, Music, Video, software etc.
     * downloads.
     *
     * @param oneTimeName
     *            - The name of the scheme
     * @param oneTimePrice
     *            - The price of the level scheme
     * @param oneTimeCurrency
     *            - Currency to be used for scheme
     * @param oneTimeDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme
     */
    function createOneTimeCharge($oneTimeName, $oneTimePrice, $oneTimeCurrency, $oneTimeDescription) {

        Util::throwExceptionIfNullOrBlank($oneTimeName, "One Time Name");
        Util::throwExceptionIfNullOrBlank($oneTimePrice, "One Time Price");
        Util::throwExceptionIfNullOrBlank($oneTimeCurrency, "One Time Currency");
        Util::throwExceptionIfNullOrBlank($oneTimeDescription, "One Time Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($oneTimeCurrency) == "null") {
                throw new App42Exception("The currency with  type '$oneTimeCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"usage":{"oneTime":{"name":"' . $oneTimeName . '","price":"' . $oneTimePrice . '","currency":"' . $oneTimeCurrency . '","description":"' . $oneTimeDescription . '"}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/oneTime";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme. This method can be used by the app
     * developer to show the pricing plans to their users.
     *
     * @param oneTimeName
     *            - The name of scheme
     *
     * @returns Scheme Information
     */
    function getOneTime($oneTimeName) {

        Util::throwExceptionIfNullOrBlank($oneTimeName, "One Time Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $oneTimeName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/oneTime" . "/" . $oneTimeName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Removes a particular scheme. Note: A level is not physically deleted from
     * the storage. Only the state is changed so that it is available to fetch
     * older information.
     *
     * @param oneTimeName
     *            - The name of scheme to be removed
     *
     * @returns Scheme Information which has been removed
     */
    function removeOneTime($oneTimeName) {
        Util::throwExceptionIfNullOrBlank($oneTimeName, "One Time Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $oneTimeName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/oneTime" . "/" . $oneTimeName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Creates the scheme for feature based charging. Feature based charging is
     * suited for Software Applications. E.g. Within mobile, desktop, SaaS based
     * charging based on features. One can charge based on number of features
     * one uses.
     *
     * @param featureName
     *            - The name of the scheme
     * @param featurePrice
     *            - The price of the scheme
     * @param featureCurrency
     *            - Currency to be used for that scheme
     * @param featureDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme

     */
    function createFeatureCharge($featureName, $featurePrice, $featureCurrency, $featureDescription) {

        Util::throwExceptionIfNullOrBlank($featureName, "Feature Name");
        Util::throwExceptionIfNullOrBlank($featurePrice, "Feature Price");
        Util::throwExceptionIfNullOrBlank($featureCurrency, "Feature Currency");
        Util::throwExceptionIfNullOrBlank($featureDescription, "Feature Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($featureCurrency) == "null") {
                throw new App42Exception("The currency with  type '$featureCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"usage":{"feature":{"name":"' . $featureName . '","price":"' . $featurePrice . '","currency":"' . $featureCurrency . '","description":"' . $featureDescription . '"}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/feature";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme. This method can be used by the app
     * developer to show his pricing plans to their users.
     *
     * @param featureName
     *            - The name of scheme
     *
     * @returns Returns Scheme
     *
     */
    function getFeature($featureName) {

        Util::throwExceptionIfNullOrBlank($featureName, "Feature Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $featureName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/feature" . "/" . $featureName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Remove a particular scheme. Note: A level is not physically deleted from
     * the storage. Only the state is changed so that it is available to fetch
     * older information.
     *
     * @param featureName
     *            - The name of scheme which has to be removed
     *
     * @returns Scheme Information which has been removed
     */
    function removeFeature($featureName) {
        Util::throwExceptionIfNullOrBlank($featureName, "Feature Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $featureName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/feature" . "/" . $featureName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Creates the scheme for bandwidth based charging. It is best suited for
     * network based bandwidth usage.
     *
     * @param bandwidthName
     *            - name of the scheme
     * @param bandwidthUsage
     *            - bandwidth usage for the scheme
     * @param usageBandWidth
     *            - bandwidth unit for the scheme
     * @param bandwidthPrice
     *            - The price of the level scheme
     * @param bandwidthCurrency
     *            - Currency to be used for the scheme
     * @param bandwidthDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme
     */
    function createBandwidthCharge($bandwidthName, $bandwidthUsage, $usageBandWidth, $bandwidthPrice, $bandwidthCurrency, $bandwidthDescription) {

        Util::throwExceptionIfNullOrBlank($bandwidthName, "Bandwidth Name");
        Util::throwExceptionIfNullOrBlank($bandwidthUsage, "Bandwidth Usage");
        Util::throwExceptionIfNullOrBlank($usageBandWidth, "Bandwidth Width");
        Util::throwExceptionIfNullOrBlank($bandwidthPrice, "Bandwidth Price");
        Util::throwExceptionIfNullOrBlank($bandwidthCurrency, "Bandwidth Currency");
        Util::throwExceptionIfNullOrBlank($bandwidthDescription, "Bandwidth Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $usageBandWidthObj = new BandWidthUnit();
            if ($usageBandWidthObj->isAvailable($usageBandWidth) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'KB', 'MB', 'GB' or 'TB' ");
            }
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($bandwidthCurrency) == "null") {
                throw new App42Exception("The currency with  type '$bandwidthCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"bandwidth":{"name":"' . $bandwidthName . '","bandwidth":"' . $bandwidthUsage . '","unit":"' . $usageBandWidth . '","price":"' . $bandwidthPrice . '","currency":"' . $bandwidthCurrency . '","description":"' . $bandwidthDescription . '"}}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/bandwidth";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme. This method can be used by the app
     * developers to show the pricing plans to their users.
     *
     * @param bandwidthName
     *            - The name of scheme
     *
     * @returns Scheme Information
     */
    function getBandwidth($bandwidthName) {

        Util::throwExceptionIfNullOrBlank($bandwidthName, "Bandwidth Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $bandwidthName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/bandwidth" . "/" . $bandwidthName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Remove a particular scheme. Note: A level is not physically deleted from
     * the storage. Only the state is changed so that it is available to fetch
     * older information.
     *
     * @param bandwidthName
     *            - The name of the scheme to be removed
     *
     * @returns Scheme Information which has been removed
     */
    function removeBandwidth($bandwidthName) {
        Util::throwExceptionIfNullOrBlank($bandwidthName, "Bandwidth Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $bandwidthName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/bandwidth" . "/" . $bandwidthName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Creates the scheme for storage based charging. It is best suited for disk
     * based storage usage. E.g. photo Storage, file Storage, RAM usage,
     * Secondary Storage.
     *
     * @param storageName
     *            - The name of the scheme
     * @param storageSpace
     *            - storage space for the scheme
     * @param usageStorage
     *            - Storage units to be used for the scheme
     * @param storagePrice
     *            - The price of the scheme
     * @param storageCurrency
     *            - Currency to be used for that scheme
     * @param storageDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme
     */
    function createStorageCharge($storageName, $storageSpace, $usageStorage, $storagePrice, $storageCurrency, $storageDescription) {

        Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");
        Util::throwExceptionIfNullOrBlank($storageSpace, "Storage Usage");
        Util::throwExceptionIfNullOrBlank($usageStorage, "Storage Width");
        Util::throwExceptionIfNullOrBlank($storagePrice, "Storage Price");
        Util::throwExceptionIfNullOrBlank($storageCurrency, "Storage Currency");
        Util::throwExceptionIfNullOrBlank($storageDescription, "Storage Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $usageBandWidthObj = new StorageUnit();
            if ($usageBandWidthObj->isAvailable($usageStorage) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'KB', 'MB', 'GB' or 'TB' ");
            }
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($storageCurrency) == "null") {
                throw new App42Exception("The currency with  type '$storageCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"storage":{"name":"' . $storageName . '","space":"' . $storageSpace . '","unit":"' . $usageStorage . '","price":"' . $storagePrice . '","currency":"' . $storageCurrency . '","description":"' . $storageDescription . '"}}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storage";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme. This method can be used by the App
     * developer to show his pricing plans to their users.
     *
     * @param storageName
     *            - The name of scheme
     *
     * @returns Scheme Information
     */
    function getStorage($storageName) {

        Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $storageName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storage" . "/" . $storageName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Remove a particular scheme. Note: A level is not physically deleted from
     * the storage. Only the state is changed so that it is available to fetch
     * older information.
     *
     * @param storageName
     *            - The name of scheme
     *
     * @returns Scheme Information which has been removed
     */
    function removeStorage($storageName) {
        Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $storageName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storage" . "/" . $storageName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Creates the scheme for time based charging. It is best suited for
     * applications which want to charge based on time usage or elapsed. E.g.
     * How long one is listening to music or watching a video. How long the
     * person is reading a online book or magazine etc.
     *
     * @param timeName
     *            - The name of the scheme
     * @param timeUsage
     *            - usage time for the scheme
     * @param usageTime
     *            - unit of time for the scheme
     * @param timePrice
     *            - The price of the level scheme
     * @param timeCurrency
     *            - Currency used for the scheme
     * @param timeDescription
     *            - Description of the scheme
     *
     * @returns Created Scheme
     */
    function createTimeCharge($timeName, $timeUsage, $usageTime, $timePrice, $timeCurrency, $timeDescription) {

        Util::throwExceptionIfNullOrBlank($timeName, "Time Name");
        Util::throwExceptionIfNullOrBlank($timeUsage, "Time Usage");
        Util::throwExceptionIfNullOrBlank($usageTime, "Time Width");
        Util::throwExceptionIfNullOrBlank($timePrice, "Time Price");
        Util::throwExceptionIfNullOrBlank($timeCurrency, "Time Currency");
        Util::throwExceptionIfNullOrBlank($timeDescription, "Time Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $usageBandWidthObj = new TimeUnit();
            if ($usageBandWidthObj->isAvailable($usageTime) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'SECONDS', 'MINUTES', 'HOURS'");
            }
             $currencyObj = new Currency();
            if ($currencyObj->isAvailable($timeCurrency) == "null") {
                throw new App42Exception("The currency with  type '$timeCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"time":{"name":"' . $timeName . '","time":"' . $timeUsage . '","unit":"' . $usageTime . '","price":"' . $timePrice . '","currency":"' . $timeCurrency . '","description":"' . $timeDescription . '"}}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/time";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Gets the information for the scheme based on timeName. This method can be
     * used by the app developers to show his pricing plans to their users.
     *
     * @param timeName
     *            - The name of scheme
     *
     * @returns Scheme Information
     */
    function getTime($timeName) {

        Util::throwExceptionIfNullOrBlank($timeName, "Time Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['name'] = $timeName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/time" . "/" . $timeName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Remove a particular scheme based on timeName. Note: A level is not
     * physically deleted from the storage. Only the state is changed so that it
     * is available to fetch older information.
     *
     * @param timeName
     *            - The name of scheme
     *
     * @returns Scheme Information which has been removed
     */
    function removeTime($timeName) {
        Util::throwExceptionIfNullOrBlank($timeName, "Storage Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $timeName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/time" . "/" . $timeName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Removes a particular scheme. Note: A Custom charge is not physically
     * deleted from the storage. Only the state is changed so that it is
     * available to fetch older information.
     *
     * @param customName
     *            - The name of scheme
     *
     * @returns Scheme Information which has been removed

     */
    function removeCustom($customName) {
        Util::throwExceptionIfNullOrBlank($customName, "custom Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['customName'] = $customName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/time" . "/" . $timeName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param levelName
     *            - The name of scheme
     *
     * @returns Returns charging information
     */
    function chargeLevel($chargeUser, $levelName) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($levelName, "Level Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"usage":{"charge":{"level":{"user":"' . $chargeUser . '","name":"' . $levelName . '"}}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/level";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param oneTimeName
     *            - The name of scheme
     *
     * @returns Returns charging information
     *
     */
    function chargeOneTime($chargeUser, $oneTimeName) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($oneTimeName, "One Time Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"usage":{"charge":{"oneTime":{"user":"' . $chargeUser . '","name":"' . $oneTimeName . '"}}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/oneTime";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param featureName
     *            - The name of scheme
     *
     * @returns Returns charging information
     */
    function chargeFeature($chargeUser, $featureName) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($featureName, "Feature Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"usage":{"charge":{"feature":{"user":"' . $chargeUser . '","name":"' . $featureName . '"}}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/feature";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param bandwidthName
     *            - The name of scheme
     * @param bandwidth
     *            - bandwidth for which the charging has to be done
     * @param usageBandWidth
     *            - unit of bandwidth charging
     *
     * @returns Returns charging information
     */
    function chargeBandwidth($chargeUser, $bandwidthName, $bandwidth, $usageBandWidth) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($bandwidthName, "Bandwidth Name");
        Util::throwExceptionIfNullOrBlank($bandwidth, "Bandwidth");
        Util::throwExceptionIfNullOrBlank($usageBandWidth, "Usage Bandwidth");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $usageBandWidthObj = new BandWidthUnit();
            if ($usageBandWidthObj->isAvailable($usageBandWidth) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'KB', 'MB', 'GB' or 'TB' ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"charge":{"bandwidth":{"user":"' . $chargeUser . '","name":"' . $bandwidthName . '","bandwidth":"' . $bandwidth . '","unit":"' . $usageBandWidth . '"}}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/bandwidth";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param storageName
     *            - The name of scheme
     * @param storageSpace
     *            - storage for which the charging has to be done
     * @param usageStorage
     *            - unit of storage charging
     *
     * @returns Returns charging information
     */
    function chargeStorage($chargeUser, $storageName, $storagePrice, $usageStorage) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($storageName, "Storage Name");
        Util::throwExceptionIfNullOrBlank($storagePrice, "Storage Price");
        Util::throwExceptionIfNullOrBlank($usageStorage, "Usage Storage");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $usageBandWidthObj = new StorageUnit();
            if ($usageBandWidthObj->isAvailable($usageStorage) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'KB', 'MB', 'GB' or 'TB' ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"charge":{"storage":{"user":"' . $chargeUser . '","name":"' . $storageName . '","space":"' . $storagePrice . '","unit":"' . $usageStorage . '"}}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/storage";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Charge on a particular scheme. A Charging record is created whenever this
     * method is called. Which is used for billing and usage behaviour analysis
     * purpose.
     *
     * @param chargeUser
     *            - The user against whom the charging has to be done
     * @param timeName
     *            - The name of scheme
     * @param chargetime
     *            - time for which the charging has to be done
     * @param usageTime
     *            - unit of time charging
     *
     * @returns Returns charging information
     */
    function chargeTime($chargeUser, $timeName, $chargetime, $usageTime) {

        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        Util::throwExceptionIfNullOrBlank($timeName, "Time Name");
        Util::throwExceptionIfNullOrBlank($chargetime, "Time Price");
        Util::throwExceptionIfNullOrBlank($usageTime, "Usage Time");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $usageBandWidthObj = new TimeUnit();
            if ($usageBandWidthObj->isAvailable($usageTime) == "null") {
                throw new App42Exception("The Request parameters are invalid. Unit can be either 'SECONDS', 'MINUTES', 'HOURS'");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"usage":{"charge":{"time":{"user":"' . $chargeUser . '","name":"' . $timeName . '","time":"' . $chargetime . '","unit":"' . $usageTime . '"}}}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/charge" . "/time";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the app
     * developers to display their usage based pricing plan.
     */
    function getAllLevelUsage() {

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
            $this->url = $this->url . "/level";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the App developer
     * to display their usage based pricing plan
     *
     */
    function getAllOneTimeUsage() {

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
            $this->url = $this->url . "/oneTime";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the App developer
     * to display their usage based pricing plan
     *
     */
    function getAllFeatureUsage() {

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
            $this->url = $this->url . "/feature";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the App developer
     * to display their usage based pricing plan
     *
     */
    function getAllBandwidthUsage() {

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
            $this->url = $this->url . "/bandwidth";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the App developer
     * to display their usage based pricing plan
     *
     */
    function getAllStorageUsage() {

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
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

    /**
     * Returns all the schemes for this usage type. This can be used by the App developer
     * to display their usage based pricing plan
     *
     */
    function getAllTimeUsage() {

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
            $this->url = $this->url . "/time";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $usageResponseObj = new UsageResponseBuilder();
            $usageObj = $usageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $usageObj;
    }

}
?>
