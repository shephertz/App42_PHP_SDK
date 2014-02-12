<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\LicenseResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\Currency;
use com\shephertz\app42\paas\sdk\php\App42Response;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'LicenseResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'Currency.php';
include_once 'App42Response.php';
/**
 *
 * AppTab - License. This service provides traditional License engine. This can
 * be useful to App developers who want to sell their applications on license
 * keys and want to use a license manager on the cloud. It allows to create a
 * license for a particular App. Once the license scheme is created. The App
 * developer can issue license, revoke license and check for validity of the
 * license.
 *
 * When a license is issued a license key is generated and returned. Which is
 * used for revoking and checking the validity of the license. The Bill service
 * is used to find licenses issued to a particular user.
 *
 * @see BillService
 * @see License
 *
 */
class LicenseService {

    protected $resource = "license";
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
     * Creates the license scheme for an app.
     *
     * @param licenseName
     *            - The name of the Scheme to be created
     * @param licensePrice
     *            - Price of the Scheme to be created
     * @param licenseCurrency
     *            - Currency of the Scheme to be created
     * @param licenseDescription
     *            - Description of the Scheme to be created
     *
     * @returns Created license Scheme
     */
    function createLicense($licenseName, $licensePrice, $licenseCurrency, $licenseDescription) {

        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");
        Util::throwExceptionIfNullOrBlank($licensePrice, "License Price");
        Util::throwExceptionIfNullOrBlank($licenseCurrency, "License Currency");
        Util::throwExceptionIfNullOrBlank($licenseDescription, "License Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $currencyTypeObj = new Currency();
            if ($currencyTypeObj->isAvailable($licenseCurrency) == "null") {
                throw new App42Exception("The currency with  type '$licenseCurrency' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"appTab":{"license":{"name":"' . $licenseName . '","price":"' . $licensePrice . '","currency":"' . $licenseCurrency . '","description":"' . $licenseDescription . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Issues license based on license scheme name. It returns a license key
     * which can be used in future to fetch information about the license, or to
     * revoke it or to find its validity.
     *
     * @param userName
     *            - The user for whom the license has to be issued
     * @param licenseName
     *            - The name of the Scheme to be issued
     *
     * @returns Issued license Scheme
     */
    function issueLicense($userName, $licenseName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"appTab":{"license":{"user":"' . $userName . '","name":"' . $licenseName . '"}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/issue";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Fetches information about the license. This can be used by an app
     * developers to display license information/pricing plan about their app to
     * their customers.
     *
     * @param licenseName
     *            - The name of the Scheme to be fetched
     *
     * @returns Fetched license Scheme
     */
    function getLicense($licenseName) {
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $licenseName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $licenseName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Fetches all the licenses for an App. This can be used by app developers
     * to display license information/pricing plan about their app to their
     * customers.
     *
     * @returns All license Schemes
     */
    function getAllLicenses() {

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
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Fetches all licenses issued to a particular user. This can be used by app
     * developers to show the users their order history.
     *
     * @param userName
     *            - User Name for whom issued licenses have to be fetched
     * @param licenseName
     *            - Name of the Scheme to be fetched
     *
     * @returns All issued licenses
     */
    function getIssuedLicenses($userName, $licenseName) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['userName'] = $userName;
            $params['name'] = $licenseName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $userName . "/" . $licenseName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Checks whether a particular license key is Valid or not.
     *
     * @param userName
     *            - The user for whom the validity has to be checked
     * @param licenseName
     *            - The scheme name for which the validity has to be checked
     * @param key
     *            - The license key which has to be validated
     *
     * @returns Whether the license for the user is valid or not
     */
    function isValid($userName, $licenseName, $key) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");
        Util::throwExceptionIfNullOrBlank($key, "Key");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['userName'] = $userName;
            $params['name'] = $licenseName;
            $params['key'] = $key;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $userName . "/" . $licenseName . "/" . $key;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $licenseObj;
    }

    /**
     * Revokes license for a particular user. Once revoked the method isValid
     * will return that the key is inValid. Note: Once a license is revoked it
     * cannot be made valid again.
     *
     * @param userName
     *            - The user for which the license has to be revoked
     * @param licenseName
     *            - The scheme name which has to be revoked
     * @param key
     *            - The license key which has to be revoked
     *
     * @returns License information which has been revoked
     */
    function revokeLicense($userName, $licenseName, $key) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");
        Util::throwExceptionIfNullOrBlank($key, "Key");
          $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['userName'] = $userName;
            $params['name'] = $licenseName;
            $params['key'] = $key;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $userName . "/" . $licenseName . "/" . $key;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, "");
            $licenseResponseObj = new LicenseResponseBuilder();
            $licenseObj = $licenseResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($licenseObj);
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
