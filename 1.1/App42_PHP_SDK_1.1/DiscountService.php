<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\appTab\DiscountResponseBuilder;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;

include_once 'RestClient.class.php';
include_once 'DiscountResponseBuilder.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once 'DiscountData.php';
include_once 'App42NotFoundException.php';

class DiscountService {

    protected $resource = "discount";
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
     * create a discount package for the app.
     *
     * @param discountName
     * 				- Create a Discount Name which should be unique
     * @param discountType
     * 				- Type of discount that you want't to create Like Storage, Bandwidth etc
     * @param discount
     * 				- Discount contains the details of the package you create
     * 				  like discountPercent , discountUsage , startDate, endDate etc
     * @param description
     * 				- Description of the discountData to be created
     * @return	The Created DiscountData Object
     *
     * @throws App42Exception
     */
    function createDiscount($discountName, $discountType, Discount $discount, $description) {
        Util::throwExceptionIfNullOrBlank($discountName, "Discount Name");
        Util::throwExceptionIfNullOrBlank($discountType, "Discount Type");
        Util::throwExceptionIfNullOrBlank($discount, "Discount");
        Util::throwExceptionIfNullOrBlank($description, "Description");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        $discountValue = new Discount();
        $percentage = $discount->getDiscountPercent();
        $startDate = $discount->getStartDate();
        $endDate = $discount->getEndDate();
        $strStartDate = (date("D M d H:i:s T Y", strtotime($startDate)) );
        $strEndDate = (date("D M d H:i:s T Y", strtotime($endDate)));
        echo $strEndDate;
        $usage = $discount->getUsage();
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"app":{"discount":{"discountDetails":{"startDate":"' . $strStartDate . '","percentage":' . $percentage . ',"usage":' . $usage . ',"endDate":"' . $strEndDate . '"},"description":"' . $description . '","discountName":"' . $discountName . '","type":"' . $discountType . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            print_r($response);
            $discountDataObj = new DiscountResponseBuilder();
            $result = $discountDataObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $result;
    }

    function gettAllDiscount() {
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $discountDataObj = new DiscountResponseBuilder();
            $discount = $discountDataObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $discount;
    }

    function getDiscountByName($discountName) {
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['discountName'] = $discountName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/".$discountName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            print_r($response);
            $discountDataObj = new DiscountResponseBuilder();
            $discount = $discountDataObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $discount;
    }

    function deleteDiscountByName($discountName) {
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['discountName'] = $discountName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $body = null;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $discountName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $discountDataObj = new DiscountResponseBuilder();
            $discount = $discountDataObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $discount;
    }

}
?>
