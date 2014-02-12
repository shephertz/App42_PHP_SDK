<?php

namespace com\shephertz\app42\paas\sdk\php\session;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\session\SessionResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'SessionResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * The Session Manager manages user sessions on the server side. It is a
 * persistent Session Manager. It allows to save attributes in the session and
 * retrieve them. This Session Manager is especially useful for Mobile/Device
 * Apps which want to do session management.
 *
 * @see Session
 *
 */
class SessionService {

    protected $resource = "session";
    protected $apiKey;
    protected $secretKey;
    protected $sessionId;
    protected $adminKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";

    //public static $MALE = "Male";
    //public static $FEMALE = "Female";

    /**
     * This is a constructor that takes
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
     * Create Session for the User. If the session does not exist it will create
     * a new session
     *
     * @param uName
     *            - UserName for which the session has to be created.
     *
     * @return Session object containing the session id of the created session.
     *         This id has to be used for storing or retrieving attributes.

     */
    function getSession($uName, $isCreate = null) {
        $sessionObj = new Session();
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($uName, "User Name");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                $sessionId = $this->sessionId;
                if ($sessionId != null) {
                    $params['sessionId'] = $this->sessionId;
                }
                $adminKey = $this->adminKey;
                if ($adminKey != null) {
                    $params['adminKey'] = $this->adminKey;
                }
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"session":{"userName":"' . $uName . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url;
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $sessionResponseObj = new SessionResponseBuilder();
                $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $sessionObj;
        } else {
            /**
             * Create User Session based on the isCreate boolean parameter. If isCreate
             * is true and there is an existing session for the user, the existing
             * session is returned. If there is no existing session for the user a new
             * one is created. If isCreate is false and there is an existing session,
             * the existing session is returned if there is no existing session new one
             * is not created
             *
             * @param uName
             *            - UserName for which the session has to be created.
             * @param isCreate
             *            - A boolean value for specifying if an existing session is not
             *            there, should a new one is to be created or not.
             *
             * @return Session object containing the session id of the created session.
             *         This id has to be used for storing or retrieving attributes.
             */
            Util::throwExceptionIfNullOrBlank($uName, "User Name");
            Util::throwExceptionIfNullOrBlank($isCreate, "isCreate");
            $encodedIsCreate = Util::encodeParams($isCreate);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                $sessionId = $this->sessionId;
                if ($sessionId != null) {
                    $params['sessionId'] = $this->sessionId;
                }
                $adminKey = $this->adminKey;
                if ($adminKey != null) {
                    $params['adminKey'] = $this->adminKey;
                }
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"session":{"userName":"' . $uName . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedIsCreate;
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $sessionResponseObj = new SessionResponseBuilder();
                $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $sessionObj;
        }
    }

    /**
     * Invalidate a session based on the session id. All the attributes store in
     * the session will be lost.
     *
     * @param sessionId
     *            - The session id for which the session has to be invalidated.
     *
     * @return App42Response if invalidated successfully
     */
    function invalidate($sessionId) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"session":{"id":"' . $sessionId . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $sessionObj;
    }

    /**
     * Sets attribute in a session whose session id is provided. Attributes are
     * stored in a key value pair.
     *
     * @param sessionId
     *            - Session id for which the attribute has to be saved.
     * @param attributeName
     *            - The attribute key that needs to be stored
     * @param attributeValue
     *            - The attribute value that needs to be stored
     *
     * @return Session object containing the attribute and value which is stored
     */
    function setAttribute($sessionId, $attributeName, $attributeValue) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        Util::throwExceptionIfNullOrBlank($attributeName, "Attribute Name");
        Util::throwExceptionIfNullOrBlank($attributeValue, "Attribute Value");
        $encodedSessionId = Util::encodeParams($sessionId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            $params['sessionId'] = $sessionId;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"session":{"name":"' . $attributeName . '","value":"' . $attributeValue . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/id/" . $encodedSessionId;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $sessionObj;
    }

    /**
     * Gets the attribute value in a session whose session id is provided.
     *
     * @param sessionId
     *            - The session id for which the attribute has to be fetched
     * @param attributeName
     *            - The attribute key that has to be fetched
     *
     * @return Session object containing the attribute and value which is stored
     *         for the session id and attribute name
     */
    function getAttribute($sessionId, $attributeName) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        Util::throwExceptionIfNullOrBlank($attributeName, "Attribute Name");
        $encodedSessionId = Util::encodeParams($sessionId);
        $encodedAttributeName = Util::encodeParams($attributeName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            date_default_timezone_set('UTC');
            $params['sessionId'] = $sessionId;
            $params['name'] = $attributeName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/id/" . $encodedSessionId . "/" . $encodedAttributeName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $sessionObj;
    }

    /**
     * Gets all the attributes for a given session id
     *
     * @param sessionId
     *            - The session id for which the attribute has to be fetched
     *
     * @return Session object containing all the attributes and values which are
     *         stored for the session id
     */
    function getAllAttributes($sessionId) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        $encodedSessionId = Util::encodeParams($sessionId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            date_default_timezone_set('UTC');
            $params['sessionId'] = $sessionId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/id/" . $encodedSessionId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $sessionObj;
    }

    /**
     * Removes the attribute from a session whose session id is provided.
     *
     * @param sessionId
     *            - The session id for which the attribute has to be removed
     * @param attributeName
     *            - The attribute key that has to be removed
     *
     * @return App42Response if removed successfully
     */
    function removeAttribute($sessionId, $attributeName) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        Util::throwExceptionIfNullOrBlank($attributeName, "Attribute Name");
        $encodedSessionId = Util::encodeParams($sessionId);
        $encodedAttributeName = Util::encodeParams($attributeName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            date_default_timezone_set('UTC');
            $params['sessionId'] = $sessionId;
            $params['name'] = $attributeName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/id/" . $encodedSessionId . "/" . $encodedAttributeName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($sessionObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes all the attributes for a given session id
     *
     * @param sessionId
     *            - The session id for which the attributes has to be removed
     *
     * @return App42Response if removed successfully
     */
    function removeAllAttributes($sessionId) {

        Util::throwExceptionIfNullOrBlank($sessionId, "session Id");
        $encodedSessionId = Util::encodeParams($sessionId);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $authSessionId = $this->sessionId;
            if ($authSessionId != null) {
                $params['sessionId'] = $this->sessionId;
            }
            $adminKey = $this->adminKey;
            if ($adminKey != null) {
                $params['adminKey'] = $this->adminKey;
            }
            date_default_timezone_set('UTC');
            $params['sessionId'] = $sessionId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/id/" . $encodedSessionId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $sessionResponseObj = new SessionResponseBuilder();
            $sessionObj = $sessionResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($sessionObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    public function getSessionId() {
        return $this->sessionId;
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }

    public function getAdminKey() {
        return $this->adminKey;
    }

    public function setAdminKey($adminKey) {
        $this->adminKey = $adminKey;
    }

}

?>
