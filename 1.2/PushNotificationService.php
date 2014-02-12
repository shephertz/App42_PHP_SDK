<?php

namespace com\shephertz\app42\paas\sdk\php\push;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\push\PushNotificationResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'PushNotificationResponseBuilder.php';
include_once 'App42Exception.php';

class PushNotificationService {

    protected $resource = "push";
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
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    function storeDeviceToken($userName, $deviceToken, $type) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        Util::throwExceptionIfNullOrBlank($type, "Device Type");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $deviceTypeObj = new DeviceType();
            if ($deviceTypeObj->isAvailable($type) == "null") {
                throw new App42Exception("The device with  type '$type' does not Exist ");
            }

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"push":{"userName":"' . $userName . '","deviceToken":"' . $deviceToken . '","type":"' . $type . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/storeDeviceToken/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function createChannelForApp($channel, $description) {
        Util::throwExceptionIfNullOrBlank($channel, "channel");
        Util::throwExceptionIfNullOrBlank($description, "description");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = '{"app42":{"push":{"channel":{"name":"' . $channel . '","description":"' . $description . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/createAppChannel/";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function subscribeToChannel($channel, $userName) {
        Util::throwExceptionIfNullOrBlank($channel, "channel");
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = '{"push":{"channel":{"userName":"' . $userName . '","name":"' . $channel . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/subscribeToChannel/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function unsubscribeFromChannel($channel, $userName) {
        Util::throwExceptionIfNullOrBlank($channel, "channel");
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"push":{"channel":{"userName":"' . $userName . '","name":"' . $channel . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/unsubscribeToChannel/" . $encodedUserName;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendPushMessageToChannel($channel, $message) {
        Util::throwExceptionIfNullOrBlank($channel, "channel");
        Util::throwExceptionIfNullOrBlank($message, "message");
        $encodedChannel = Util::encodeParams($channel);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"push":{"message":{"channel":"' . $channel . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/sendPushMessageToChannel/" . $encodedChannel;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendPushMessageToAll($message) {
        Util::throwExceptionIfNullOrBlank($message, "message");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/sendPushMessageToAll";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendPushMessageToUser($userName, $message) {
        Util::throwExceptionIfNullOrBlank($message, "message");
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"push":{"message":{"userName":"' . $userName . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/sendMessage/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendPushMessageToAllByType($message, $type) {
        Util::throwExceptionIfNullOrBlank($message, "message");
        Util::throwExceptionIfNullOrBlank($type, "Type");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $deviceTypeObj = new DeviceType();
            if ($deviceTypeObj->isAvailable($type) == "null") {
                throw new App42Exception("The device with  type '$type' does not Exist ");
            }

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","type":"' . $type . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/sendMessageToAllByType";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

}

?>
