<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\BillResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\BillMonth;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once 'BillMonth.php';
include_once 'BillResponseBuilder.php';

/**
 * AppTab - Billing service. This service is used along with the Usage service.
 * It generates Bill for a particular based on Usage Scheme. For e.g. if user
 * sid's bill has to be seen for May and 2012. This service will list all the
 * charging transactions and calculate the bill for May and tell the total usage
 * and price. The calculation is done based on the Price which is given during
 * scheme creation, the unit of charging and corresponding usage. AppTab
 * currently just maintains the data and does calculation. How the Bill is
 * rendered and the interface with Payment Gateway is left with the App
 * developer.
 *
 * @see Usage
 * @see Bill
 *
 */
class BillService {

    protected $resource = "bill";
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
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageTimeByMonthAndYear($userName, $usageName, $billMonth, $year) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/time/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageStorageByMonthAndYear($userName, $usageName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storage/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageBandwidthByMonthAndYear($userName, $usageName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/bandwidth/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageLevelByMonthAndYear($userName, $usageName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/level/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageOneTimeByMonthAndYear($userName, $usageName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/oneTime/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param usageName
     *            - The name of the Scheme
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageFeatureByMonthAndYear($userName, $usageName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($usageName, "Usage Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['usageName'] = $usageName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/feature/" . $usageName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

    /**
     * Get usage for Scheme based on Month and Year. This is useful to show the
     * user the charging details of the User for the Scheme
     *
     * @param userName
     *            - The user for which the charging information has to be
     *            fetched
     * @param licenseName
     *            - The name of the License
     * @param billMonth
     *            - The month name for which the usage has to be fetched e.g.
     *            BillMonth.JANUARY, BillMonth.DECEMBER
     * @param year
     *            - The year for which the usage has to be fetched e.g. 2012,
     *            2011
     *
     * @returns All the charging transactions with the total usage and total
     *          price for that month
     */
    function usageLicenseByMonthAndYear($userName, $licenseName, $billMonth, $year) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($licenseName, "License Name");
        Util::throwExceptionIfNullOrBlank($billMonth, "Bill Month");
        Util::throwExceptionIfNullOrBlank($year, "Year");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $billMonthObj = new BillMonth();
            if ($billMonthObj->isAvailable($billMonth) == "null") {
                throw new App42Exception("Bill Month '$billMonth' doesnot exists");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['licenseName'] = $licenseName;
            $params['year'] = $year;
            $params['month'] = $billMonth;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/license/" . $licenseName . "/" . $year . "/" . $billMonth . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $billResponseObj = new BillResponseBuilder();
            $billObj = $billResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $billObj;
    }

}
?>
