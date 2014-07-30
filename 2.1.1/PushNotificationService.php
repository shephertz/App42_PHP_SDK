<?php

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'PushNotificationResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Service.php';
include_once 'App42Response.php';

class PushNotificationService extends App42Service {

    protected $resource = "push";
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
    public function __construct($apiKey, $secretKey) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $this->version . "/" . $this->resource;
    }

    function storeDeviceToken($userName, $deviceToken, $type) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        Util::throwExceptionIfNullOrBlank($type, "Device Type");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $deviceTypeObj = new DeviceType();
            if ($deviceTypeObj->isAvailable($type) == "null") {
                throw new App42Exception("The device with  type '$type' does not Exist ");
            }

            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"userName":"' . $userName . '","deviceToken":"' . $deviceToken . '","type":"' . $type . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/storeDeviceToken/" . $encodedUserName;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = '{"app42":{"push":{"channel":{"name":"' . $channel . '","description":"' . $description . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/createAppChannel/";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = '{"push":{"channel":{"userName":"' . $userName . '","name":"' . $channel . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/subscribeToChannel/" . $encodedUserName;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"push":{"channel":{"userName":"' . $userName . '","name":"' . $channel . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/unsubscribeToChannel/" . $encodedUserName;
            $response = RestClient::put($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"channel":"' . $channel . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendPushMessageToChannel/" . $encodedChannel;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendPushMessageToAll";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"userName":"' . $userName . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendMessage/" . $encodedUserName;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
		    $params = null;

            $deviceTypeObj = new DeviceType();
            if ($deviceTypeObj->isAvailable($type) == "null") {
                throw new App42Exception("The device with  type '$type' does not Exist ");
            }

            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","type":"' . $type . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendMessageToAllByType";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function registerAndSubscribe($userName, $channelName, $deviceToken, $deviceType) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($channelName, "Channel Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        Util::throwExceptionIfNullOrBlank($deviceType, "Device Type");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $deviceTypeObj = new DeviceType();
            if ($deviceTypeObj->isAvailable($deviceType) == "null") {
                throw new App42Exception("The device with  type '$deviceType' does not Exist ");
            }

            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"userName":"' . $userName . '","channelName":"' . $channelName . '","deviceToken":"' . $deviceToken . '","type":"' . $deviceType . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/subscribeDeviceToChannel";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function unsubscribeDeviceToChannel($userName, $channelName, $deviceToken) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($channelName, "Channel Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"userName":"' . $userName . '","channelName":"' . $channelName . '","deviceToken":"' . $deviceToken . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/unsubscribeDeviceToChannel";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function deleteDeviceToken($userName, $deviceToken) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "DeviceToken");
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $signParams['userName'] = $userName;
            $signParams['deviceToken'] = $deviceToken;

            $queryParams['userName'] = $userName;
            $queryParams['deviceToken'] = $deviceToken;

            $params = array_merge($queryParams, $signParams);
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL;
            $response = RestClient::delete($baseURL, $params, null, null, $contentType, $accept, $headerParams);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    function sendPushMessageToGroup($message, $userList) {
        Util::throwExceptionIfNullOrBlank($message, "message");
        Util::throwExceptionIfNullOrBlank($userList, "userList");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            if (is_array($userList)) {
                $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '","users": { "user": ' . json_encode($userList) . '}}}}}';
            } else {
                $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '","users": { "user": "' . $userList . '"}}}}}';
            }
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendPushMessageToGroup";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendPushToTargetUsers($message, $dbName, $collectionName, $query) {
        Util::throwExceptionIfNullOrBlank($message, "Message");
        Util::throwExceptionIfNullOrBlank($dbName, "DbName");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($query, "Query");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            if ($query instanceof JSONObject) {
                $queryObject = array();
                array_push($queryObject, $query);
                $signParams['jsonQuery'] = json_encode($queryObject);
                $queryParams['jsonQuery'] = json_encode($queryObject);
            } else {
                $signParams['jsonQuery'] = json_encode($queryObject);
                $queryParams['jsonQuery'] = json_encode($queryObject);
            }

            $params = array_merge($queryParams, $signParams);
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendTargetPush/" . $encodedDbName . "/" . $encodedCollectionName;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function sendMessageToInActiveUsers($startDate, $endDate, $message) {
        Util::throwExceptionIfNullOrBlank($message, "Message");
        Util::throwExceptionIfNullOrBlank($startDate, "startDate");
        Util::throwExceptionIfNullOrBlank($endDate, "endDate");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
            $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"startDate":"' . $strStartDate . '","endDate":"' . $strEndDate . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendMessageToInActiveUsers";
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function scheduleMessageToUser($userName, $message, $expiryDate) {
        Util::throwExceptionIfNullOrBlank($message, "Message");
        Util::throwExceptionIfNullOrBlank($userName, "UserName");
        Util::throwExceptionIfNullOrBlank($expiryDate, "expiryDate");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
            $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"userName":"' . $userName . '","payload":"' . $message . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendMessage/" . $encodedUserName;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function unsubscribeDevice($userName, $deviceToken) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"push":{"userName":"' . $userName . '","deviceToken":"' . $deviceToken . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/unsubscribeDevice";
            $response = RestClient::put($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function resubscribeDevice($userName, $deviceToken) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"push":{"userName":"' . $userName . '","deviceToken":"' . $deviceToken . '"}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/reSubscribeDevice";
            $response = RestClient::put($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }

    function deleteAllDevices($userName) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $signParams['userName'] = $userName;
            $queryParams['userName'] = $userName;
            $params = array_merge($queryParams, $signParams);
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/deleteAll";
            $response = RestClient::delete($baseURL, $params, null, null, $contentType, $accept, $headerParams);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    function sendPushMessageToDevice($username, $deviceId, $message) {
        Util::throwExceptionIfNullOrBlank($username, "username");
        Util::throwExceptionIfNullOrBlank($message, "message");
        Util::throwExceptionIfNullOrBlank($deviceId, "deviceId");
        $encodedUsername = Util::encodeParams($username);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
            $body = '{"app42":{"push":{"message":{"username":"' . $username . '","payload":"' . $message . '","deviceId":"' . $deviceId . '","expiry":"' . date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z" . '"}}}}';
            $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/sendMessageToDevice/" . $encodedUsername;
            $response = RestClient::post($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }


     function updatePushBadgeforDevice($userName,$deviceToken,$badges) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($deviceToken, "Device Token");
        Util::throwExceptionIfNullOrBlank($badges, "badges");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
             $body = '{"app42":{"push":{"userName":"' . $userName . '","deviceToken":"' . $deviceToken . '","increment":"' . $badges . '"}}}';
             $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/resetPushBadgeforDevice";
            $response = RestClient::put($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
            $pushResponseObj = new PushNotificationResponseBuilder();
            $pushObj = $pushResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $pushObj;
    }


      function updatePushBadgeforUser($userName,$badges) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($badges, "badges");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
		    $params = null;
            $headerParams = array();
            $queryParams = array();
            $signParams = $this->populateSignParams();
            $metaHeaders = $this->populateMetaHeaderParams();
            $headerParams = array_merge($signParams, $metaHeaders);
            $body = null;
             $body = '{"app42":{"push":{"userName":"' . $userName . '","increment":"' . $badges . '"}}}';
             $signParams['body'] = $body;
            $signature = urlencode($objUtil->sign($signParams)); //die();
            $headerParams['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $baseURL = $this->url;
            $baseURL = $baseURL . "/resetPushBadgeforUser";
            $response = RestClient::put($baseURL, $params, null, null, $contentType, $accept, $body, $headerParams);
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
