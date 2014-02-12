<?php

namespace com\shephertz\app42\paas\sdk\php\charge;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';

/**
 *
 * This Charge object is the value object which contains the properties of
 * Charge along with the setter & getter for those properties.
 *
 */
class Charge {

    protected $resource = "charge";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";
    public static $START = "START";
    public static $STOP = "STOP";
    public static $OPEN = "OPEN";
    public static $CLOSE = "CLOSE";

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
     * The charges applied at the time of starting an app
     *
     * @param chargeUser
     *            - User who will be charged
     *
     * @return Charge object containing the time the app was started
     */
    function chargeStart($chargeUser) {
        Util::throwExceptionIfNullOrBlank($chargeUser, "Charge User");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"charge":{"user":"' . $chargeUser . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

    /**
     * The charges applied at the time the app was stopped
     *
     * @param transId
     *            - TransId created when the app is stopped and is in charge
     *            phase
     *
     * @return Charge object containing the time the app was stopped
     */
    function chargeStop($transId) {

        Util::throwExceptionIfNullOrBlank($transId, "TransactionId");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"charge":{"transactionId":"' . $transId . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

    /**
     * Get all the transactions
     *
     * @return charge object containing all the transactions
     */
    function getAllTransactions() {
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
            $this->url = $this->url . "/" . transactions;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

  /**
	 * Get all the transactions based on id
	 *
	 * @params id
	 *            - The transId that has to be fetched
	 *
	 * @return charge object containing the transactions based on id
	 */

    function getTransactionById($id) {

        Util::throwExceptionIfNullOrBlank($id, "TransactionId");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['transactionId'] = $id;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['timeStamp'];
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . transactions . "/" . id . "/" . $id;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

    /**
	 * Get all the transactions based on the state
	 *
	 * @params state
	 *            - The state of an app that has to be fetched which can be
	 *            either OPEN (for chargeStart) or CLOSE (for chargeStop)
	 *
	 * @return charge Object containing the transactions based on state
	 */

    function getTransactionByState($state) {

        Util::throwExceptionIfNullOrBlank($state, "Transaction State");

        if ($state != $OPEN && $state != $CLOSE) {
            throw new App42Exception("State can be either 'OPEN' or 'CLOSE'. Use Charge.OPEN or Charge.CLOSE");
        }

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['state'] = $state;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['timeStamp'];
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . transactions . "/" . state . "/" . $state;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

    /**
	 * Get all the transactions based on the userName
	 *
	 * @param userName
	 *            - Name of the user for which the transaction details has to be
	 *            fetched
	 *
	 * @return charge Object containing the transactions based on userName
	 */

    function getTransactionByUsername($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "UserName");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['timeStamp'];
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . transactions . "/" . username . "/" . $userName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $chargeResponseObj = new ChargeResponseBuilder();
            $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

   /**
	 * Get all the transactions based on the startDate and endDate
	 *
	 * @param startDate
	 *            - Date on which the app was started
	 * @param endDate
	 *            - Date on which the app was stopped
	 *
	 * @return charge Object containing the transactions based on date range
	 */


    function getTransactionByDateRange($startDate, $endDate) {

        Util::throwExceptionIfNullOrBlank($startDate, "StarDate");
        Util::throwExceptionIfNullOrBlank($endDate, "EndDate");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try{
        $params = array();
        $params['apiKey'] = $this->apiKey;
        $params['version'] = $this->version;
        date_default_timezone_set('UTC');
        $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
        $params['startDate'] = $startDate;
        $params['endDate'] = $endDate;
        $signature = urlencode($objUtil->sign($params)); //die();
        $params['signature'] = $signature;
        $contentType = $this->content_type;
        $accept = $this->accept;
        $this->url = $this->url . "/" . transactions . "/" . startDate . "/" . (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z") . "/" . endDate . "/" . (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");
        $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
        $chargeResponseObj = new ChargeResponseBuilder();
        $chargeObj = $chargeResponseObj->buildResponse($response->getResponse());
    }catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $chargeObj;
    }

}
?>