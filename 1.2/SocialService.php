<?php

namespace com\shephertz\app42\paas\sdk\php\social;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\social\SocialResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'SocialResponseBuilder.php';
include_once 'App42Exception.php';

/**
 * Connect to the User's multiple social accounts. Also used to update the
 * status individually or all at once for the linked social accounts.
 */
class SocialService {

    protected $resource = "social";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * The costructor for the Service
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
     * Links the User Facebook access credentials to the App User account.
     *
     * @param userName
     *            - Name of the user whose Facebook account to be linked
     * @param accessToken
     *            - Facebook Access Token that has been received after
     *            authorization
     * @param appId
     *            - Facebook App Id
     * @param appSecret
     *            - Facebook App Secret
     *
     * @returns The Social object
     */
    function linkUserFacebookAccount($userName, $accessToken, $appId = null, $appSecret = null) {
        $argv = func_get_args();
        if (count($argv) == 4) {
            $response = null;
            $social = null;
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($appId, "appId");
            Util::throwExceptionIfNullOrBlank($appSecret, "appSecret");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");

            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"social":{"userName":"' . $userName . '","accessToken":"' . $accessToken . '","appId":"' . $appId . '","appSecret":"' . $appSecret . '"}}}';
                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/facebook/linkuser";

                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        } else {
            /**
             * Links the User Facebook access credentials to the App User account.
             *
             * @param userName
             *            - Name of the user whose Facebook account to be linked
             * @param accessToken
             *            - Facebook Access Token that has been received after
             *            authorization
             * 
             * @returns The Social object
             */
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"social":{"userName":"' . $userName . '","accessToken":"' . $accessToken . '"}}}';
                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/facebook/linkuser/accesscredentials";
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        }
    }

    /**
     * Updates the Facebook status of the specified user.
     *
     * @param userName
     *            - Name of the user for whom the status needs to be updated
     * @param status
     *            - status that has to be updated
     *
     * @returns The Social object
     */
    function updateFacebookStatus($userName, $status) {

        Util::throwExceptionIfNullOrBlank($userName, "UserName");
        Util::throwExceptionIfNullOrBlank($status, "Status");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"social":{"userName":"' . $userName . '","status":"' . $status . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/facebook/updatestatus";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

    /**
     * Links the User Twitter access credentials to the App User account.
     *
     * @param userName
     *            - Name of the user whose Twitter account to be linked
     * @param consumerKey
     *            - Twitter App Consumer Key
     * @param consumerSecret
     *            - Twitter App Consumer Secret
     * @param accessToken
     *            - Twitter Access Token that has been received after
     *            authorization
     * @param accessTokenSecret
     *            - Twitter Access Token Secret that has been received after
     *            authorization
     *
     * @returns The Social object
     */
    function linkUserTwitterAccount($userName, $accessToken, $accessTokenSecret, $consumerKey = null, $consumerSecret = null) {
        $argv = func_get_args();
        if (count($argv) == 4) {
            $response = null;
            $social = null;
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($consumerKey, "consumerKey");
            Util::throwExceptionIfNullOrBlank($consumerSecret, "consumerSecret");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");
            Util::throwExceptionIfNullOrBlank($accessTokenSecret, "accessTokenSecret");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"social":{"userName":"' . $userName . '","consumerKey":"' . $consumerKey . '","$consumerSecret":"' . $consumerSecret . '","accessToken":"' . $accessToken . '","$accessTokenSecret":"' . $accessTokenSecret . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/twitter/linkuser";

                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        } else {
            /**
             * Links the User Twitter access credentials to the App User account.
             *
             * @param userName
             *            - Name of the user whose Twitter account to be linked
             * @param accessToken
             *            - Twitter Access Token that has been received after
             *            authorization
             * @param accessTokenSecret
             *            - Twitter Access Token Secret that has been received after
             *            authorization
             * 
             * @returns The Social object
             */
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");
            Util::throwExceptionIfNullOrBlank($accessTokenSecret, "accessTokenSecret");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
                $body = '{"app42":{"social":{"userName":"' . $userName . '","accessToken":"' . $accessToken . '","$accessTokenSecret":"' . $accessTokenSecret . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/twitter/linkuser/accesscredentials";
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        }
    }

    /**
     * Updates the Twitter status of the specified user.
     *
     * @param userName
     *            - Name of the user for whom the status needs to be updated
     * @param status
     *            - status that has to be updated
     *
     * @returns The Social object
     */
    function updateTwitterStatus($userName, $status) {
        $response = null;
        $social = null;
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        Util::throwExceptionIfNullOrBlank($status, "status");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        echo"jjjj";
        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"social":{"userName":"' . $userName . '","status":"' . $status . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/twitter/updatestatus";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

    /**
     * Links the User LinkedIn access credentials to the App User account.
     *
     * @param userName
     *            - Name of the user whose LinkedIn account to be linked
     * @param apiKey
     *            - LinkedIn App API Key
     * @param secretKey
     *            - LinkedIn App Secret Key
     * @param accessToken
     *            - LinkedIn Access Token that has been received after
     *            authorization
     * @param accessTokenSecret
     *            - LinkedIn Access Token Secret that has been received after
     *            authorization
     *
     * @returns The Social object
     */
    function linkUserLinkedInAccount($userName, $accessToken, $accessTokenSecret, $apiKey = null, $secretKey = null) {
        $argv = func_get_args();
        if (count($argv) == 4) {
            $response = null;
            $social = null;
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($apiKey, "apiKey");
            Util::throwExceptionIfNullOrBlank($secretKey, "secretKey");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");
            Util::throwExceptionIfNullOrBlank($accessTokenSecret, "accessTokenSecret");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"social":{"userName":"' . $userName . '","apiKey":"' . $apiKey . '","secretKey":"' . $secretKey . '","accessToken":"' . $accessToken . '","accessTokenSecret":"' . $accessTokenSecret . '"}}}';
                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/linkedin/linkuser";

                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        } else {
            /**
             * Links the User LinkedIn access credentials to the App User account.
             *
             * @param userName
             *            - Name of the user whose LinkedIn account to be linked
             * @param accessToken
             *            - LinkedIn Access Token that has been received after
             *            authorization
             * @param accessTokenSecret
             *            - LinkedIn Access Token Secret that has been received after
             *            authorization
             *
             * @returns The Social object
             */
            Util::throwExceptionIfNullOrBlank($userName, "userName");
            Util::throwExceptionIfNullOrBlank($accessToken, "accessToken");
            Util::throwExceptionIfNullOrBlank($accessTokenSecret, "accessTokenSecret");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
                $body = '{"app42":{"social":{"userName":"' . $userName . '","accessToken":"' . $accessToken . '","accessTokenSecret":"' . $accessTokenSecret . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/linkedin/linkuser/accesscredentials";
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $socialResponseObj = new SocialResponseBuilder();
                $socialObj = $socialResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $socialObj;
        }
    }

    /**
     * Updates the LinkedIn status of the specified user.
     *
     * @param userName
     *            - Name of the user for whom the status needs to be updated
     * @param status
     *            - status that has to be updated
     *
     * @returns The Social object
     */
    function updateLinkedInStatus($userName, $status) {
        $response = null;
        $social = null;
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        Util::throwExceptionIfNullOrBlank($status, "status");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"social":{"userName":"' . $userName . '","status":"' . $status . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/linkedin/updatestatus";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

    /**
     * Updates the status for all linked social accounts of the specified user.
     *
     * @param userName
     *            - Name of the user for whom the status needs to be updated
     * @param status
     *            - status that has to be updated
     *
     * @returns The Social object
     */
    function updateSocialStatusForAll($userName, $status) {
        $response = null;
        $social = null;
        Util::throwExceptionIfNullOrBlank($userName, "userName");
        Util::throwExceptionIfNullOrBlank($status, "status");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"social":{"userName":"' . $userName . '","status":"' . $status . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/social/updatestatus/all";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

    /**
     * This function returns a list of facebook friends of the specified user by
     * accessing the facebook account.
     * 
     * @param userName
     *            - Name of the user whose Facebook friends account has to be
     *            retrieve
     * @return Social Object
     */
    function getFacebookFriendsFromLinkUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "UserName");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/facebook/friends/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

    /**
     * This function returns a list of facebook friends of the specified user
     * using a given authorization token. To get the friend list here, user
     * needs not to log into the facebook account.
     * 
     * @param accessToken
     *            - Facebook Access Token that has been received after authorization
     * @return Social Object
     */
    function getFacebookFriendsFromAccessToken($accessToken) {

        Util::throwExceptionIfNullOrBlank($accessToken, "AccessToken");
        $encodedAccessToken = Util::encodeParams($accessToken);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['accessToken'] = $accessToken;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/facebook/friends/OAuth/" . $encodedAccessToken;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $socialResponseObj = new SocialResponseBuilder();
            $socialObj = $socialResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $socialObj;
    }

}

?>
