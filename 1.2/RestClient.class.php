<?php

namespace com\shephertz\app42\paas\sdk\php\connection;

use stdClass;
use com\shephertz\app42\paas\sdk\php\App42BadParameterException;
use com\shephertz\app42\paas\sdk\php\App42LimitException;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;
use com\shephertz\app42\paas\sdk\php\App42SecurityException;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\ConfigurationException;

class RestClient {

    private $curl;
    private $url;
    private $response = "";
    private $headers = array();
    private $method = "GET";
    private $params = null;
    private $contentType = null;
    private $accept = null;
    private $file = null;
    private $postBody;
    protected $sessionId;
    protected $adminKey;

    /**
     * Private Constructor, sets default options
     */
    private function __construct() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true); // This make sure will follow redirects
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true); // This too
        curl_setopt($this->curl, CURLOPT_HEADER, true); // THis verbose option for extracting the headers
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
    }

    /**
     * Execute the call to the webservice
     * @return RestClient
     */
    public function execute() {
        if ($this->method === "POST") {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->params);
            if ($this->postBody)
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postBody);
            $this->treatURL();
        } else if ($this->method == "GET") {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
            $this->treatURL();
        } else if ($this->method === "PUT") {
            curl_setopt($this->curl, CURLOPT_PUT, true);
            if ($this->postBody)
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postBody);
            $this->treatURL();
            $this->file = tmpFile();
            fwrite($this->file, $this->postBody);
            fseek($this->file, 0);
            curl_setopt($this->curl, CURLOPT_INFILE, $this->file);
            curl_setopt($this->curl, CURLOPT_INFILESIZE, strlen($this->postBody));
        } else {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);
            $this->treatURL();
        }
        if ($this->contentType != null) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("Content-Type: " . $this->contentType, "Accept: " . $this->accept, "sessionId: " . $this->sessionId, "adminKey: " . $this->adminKey));
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        $r = curl_exec($this->curl);
        $this->treatResponse($r); // Extract the headers and response
        return $this;
    }

    /**
     * Treats URL
     */
    private function treatURL() {
        if (is_array($this->params) && count($this->params) >= 1) { // Transform parameters in key/value pars in URL
            if (!strpos($this->url, '?'))
                $this->url .= '?';
            foreach ($this->params as $k => $v) {
                $this->url .= "&" . urlencode($k) . "=" . urlencode($v);
            }
        }
        return $this->url;
    }

    /*
     * Treats the Response for extracting the Headers and Response
     */

    private function treatResponse($r) {
        if ($r == null or strlen($r) < 1) {
            return;
        }
        $parts = explode("\n\r", $r); // HTTP packets define that Headers end in a blank line (\n\r) where starts the body
        while (preg_match('@HTTP/1.[0-1] 100 Continue@', $parts[0]) or preg_match("@Moved@", $parts[0])) {
            // Continue header must be bypass
            for ($i = 1; $i < count($parts); $i++) {
                $parts[$i - 1] = trim($parts[$i]);
            }
            unset($parts[count($parts) - 1]);
        }
        preg_match("@Content-Type: ([a-zA-Z0-9-]+/?[a-zA-Z0-9-]*)@", $parts[0], $reg); // This extract the content type
        $this->headers['content-type'] = $reg[1];
        preg_match("@HTTP/1.[0-1] ([0-9]{3}) ([a-zA-Z ]+)@", $parts[0], $reg); // This extracts the response header Code and Message
        $this->headers['code'] = $reg[1];
        $this->headers['message'] = $reg[2];
        $this->response = "";
        for ($i = 1; $i < count($parts); $i++) {//This make sure that exploded response get back togheter
            if ($i > 1) {
                $this->response .= "\n\r";
            }
            $this->response .= $parts[$i];
        }
    }

    /*
     * @return array
     */

    public function getHeaders() {
        return $this->headers;
    }

    /*
     * @return string
     */

    public function getResponse() {
        return $this->response;
    }

    /*
     * HTTP response code (404,401,200,etc)
     * @return int
     */

    public function getResponseCode() {
        return (int) $this->headers['code'];
    }

    /*
     * HTTP response message (Not Found, Continue, etc )
     * @return string
     */

    public function getResponseMessage() {
        return $this->headers['message'];
    }

    /*
     * Content-Type (text/plain, application/xml, etc)
     * @return string
     */

    public function getResponseContentType() {
        return $this->headers['content-type'];
    }

    /**
     * This sets that will not follow redirects
     * @return RestClient
     */
    public function setNoFollow() {
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, false);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
        return $this;
    }

    /**
     * This closes the connection and release resources
     * @return RestClient
     */
    public function close() {
        curl_close($this->curl);
        $this->curl = null;
        if ($this->file != null) {
            fclose($this->file);
        }
        return $this;
    }

    /**
     * Sets the URL to be Called
     * @return RestClient
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Set the Content-Type of the request to be send
     * Format like "application/xml" or "text/plain" or other
     * @param string $contentType
     * @return RestClient
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Set the Accept parameter of the request to be send
     * Format like "application/xml" or "text/plain" or other
     * @param string $contentType
     * @return RestClient
     */
    public function setAccept($accept) {
        $this->accept = $accept;
        return $this;
    }

    /**
     * Set the Credentials for BASIC Authentication
     * @param string $user
     * @param string $pass
     * @return RestClient
     */
    public function setCredentials($user, $pass) {
        if ($user != null) {
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->curl, CURLOPT_USERPWD, "{$user}:{$pass}");
        }
        return $this;
    }

    /**
     * Set the Request HTTP Method
     * For now, only accepts GET and POST
     * @param string $method
     * @return RestClient
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * Set Parameters to be send on the request
     * It can be both a key/value par array (as in array("key"=>"value"))
     * or a string containing the body of the request, like a XML, JSON or other
     * Proper content-type should be set for the body if not a array
     * @param mixed $params
     * @return RestClient
     */
    public function setParameters($params) {
        $this->params = $params;
        return $this;
    }

    /**
     * Set the Admin-Key of the request to be send
     * Format like "application/xml" or "text/plain" or other
     * @param string $adminKey
     * @return RestClient
     */
    public function setAdminKey($adminKey) {
        if ($adminKey)
            $this->adminKey = $adminKey;
        return $this;
    }

    /**
     * Set the Session-Id of the request to be send
     * Format like "application/xml" or "text/plain" or other
     * @param string $sessionId
     * @return RestClient
     */
    public function setSessionId($sessionId) {
        if ($sessionId)
            $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Creates the RESTClient
     * @param string $url=null [optional]
     * @return RestClient
     */
    public static function createClient($url = null) {
        $client = new RestClient;
        if ($url != null) {
            $client->setUrl($url);
        }
        return $client;
    }

    /**
     * Convenience method wrapping a commom POST call
     * @param string $url
     * @param mixed params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType="multpary/form-data" [optional] commom post (multipart/form-data) as default
     * @return RestClient
     */
    public static function post($url, $params = null, $user = null, $pwd = null, $contentType = "", $accept = null, $postBody = null) {
        try {
            $returnResult = self::call("POST", $url, $params, $user, $pwd, $contentType, $accept, $postBody);
            $appErrorCode = null;
            $httpErrorCode = null;
            $errorPayLoad = null;
            if ($accept == "application/json") {
                $jsonBody = json_decode($returnResult->response);
                if (isset($jsonBody->app42Fault)) {
                    $appErrorCode = $jsonBody->app42Fault->appErrorCode;
                    $httpErrorCode = $jsonBody->app42Fault->httpErrorCode;
                    $errorPayLoad = $jsonBody->app42Fault->details;
                }
            } else if ($accept == "application/xml") {
                $xmlBody = simplexml_load_string($returnResult->response);
                $appErrorCode = "{$xmlBody['appErrorCode']}";
                $httpErrorCode = "{$xmlBody['httpErrorCode']}";
                $errorPayLoad = "{$xmlBody->details}";
            }

            if ($httpErrorCode == 404) {
                throw new App42NotFoundException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 400) {
                throw new App42BadParameterException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 401) {
                throw new App42SecurityException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 413) {
                throw new App42LimitException($errorPayLoad, $httpErrorCode, 1413);
            } else if ($httpErrorCode == 500) {
                throw new App42Exception($errorPayLoad, $httpErrorCode, $appErrorCode);
            }

            return $returnResult;
        } catch (HttpResponseException $e) {
            throw $e;
        }
    }

    /**
     * Convenience method wrapping a commom PUT call
     * @param string $url
     * @param string $body
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType=null [optional]
     * @return RestClient
     */
    public static function put($url, $body, $user = null, $pwd = null, $contentType = null, $accept = null, $postBody = null) {
        try {
            $returnResult = self::call("PUT", $url, $body, $user, $pwd, $contentType, $accept, $postBody);
            $appErrorCode = null;
            $httpErrorCode = null;
            $errorPayLoad = null;
            if ($accept == "application/json") {
                $jsonBody = json_decode($returnResult->response);
                if (isset($jsonBody->app42Fault)) {
                    $appErrorCode = $jsonBody->app42Fault->appErrorCode;
                    $httpErrorCode = $jsonBody->app42Fault->httpErrorCode;
                    $errorPayLoad = $jsonBody->app42Fault->details;
                }
            } else if ($accept == "application/xml") {
                $xmlBody = simplexml_load_string($returnResult->response);
                $appErrorCode = "{$xmlBody['appErrorCode']}";
                $httpErrorCode = "{$xmlBody['httpErrorCode']}";
                $errorPayLoad = "{$xmlBody->details}";
            }
            if ($httpErrorCode == 404) {
                throw new App42NotFoundException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 400) {
                throw new App42BadParameterException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 401) {
                throw new App42SecurityException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 413) {
                throw new App42LimitException($errorPayLoad, $httpErrorCode, 1413);
            } else if ($httpErrorCode == 500) {
                throw new App42Exception($errorPayLoad, $httpErrorCode, $appErrorCode);
            }

            return $returnResult;
        } catch (HttpResponseException $e) {
            throw $e;
        }
    }

    /**
     * Convenience method wrapping a commom GET call
     * @param string $url
     * @param array params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @return RestClient
     */
    public static function get($url, array $params = null, $user = null, $pwd = null, $contentType = "", $accept = null) {
        //echo "CONTENT TYPE: ".$contentType;
        //print_r($params);
        // die();
        try {
            $returnResult = self::call("GET", $url, $params, $user, $pwd, $contentType, $accept);
            //print_r($returnResult);
            $appErrorCode = null;
            $httpErrorCode = null;
            $errorPayLoad = null;
            if ($accept == "application/json") {
                $jsonBody = json_decode($returnResult->response);
                if (isset($jsonBody->app42Fault)) {
                    $appErrorCode = $jsonBody->app42Fault->appErrorCode;
                    $httpErrorCode = $jsonBody->app42Fault->httpErrorCode;
                    $errorPayLoad = $jsonBody->app42Fault->details;
                }
            } else if ($accept == "application/xml") {
                $xmlBody = simplexml_load_string($returnResult->response);
                $appErrorCode = "{$xmlBody['appErrorCode']}";
                $httpErrorCode = "{$xmlBody['httpErrorCode']}";
                $errorPayLoad = "{$xmlBody->details}";
            }
            if ($httpErrorCode == 404) {
                throw new App42NotFoundException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 400) {
                throw new App42BadParameterException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 401) {
                throw new App42SecurityException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 413) {
                throw new App42LimitException($errorPayLoad, $httpErrorCode, 1413);
            } else if ($httpErrorCode == 500) {
                throw new App42Exception($errorPayLoad, $httpErrorCode, $appErrorCode);
            }

            return $returnResult;
        } catch (HttpResponseException $e) {
            throw $e;
        }
    }

    /**
     * Convenience method wrapping a commom delete call
     * @param string $url
     * @param array params
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @return RestClient
     */
    public static function delete($url, array $params = null, $user = null, $pwd = null, $contentType = "", $accept = null) {
        try {
            $returnResult = self::call("DELETE", $url, $params, $user, $pwd, $contentType, $accept);

            $appErrorCode = null;
            $httpErrorCode = null;
            $errorPayLoad = null;
            if ($accept == "application/json") {
                $jsonBody = json_decode($returnResult->response);
                if (isset($jsonBody->app42Fault)) {
                    $appErrorCode = $jsonBody->app42Fault->appErrorCode;
                    $httpErrorCode = $jsonBody->app42Fault->httpErrorCode;
                    $errorPayLoad = $jsonBody->app42Fault->details;
                }
            } else if ($accept == "application/xml") {
                $xmlBody = simplexml_load_string($returnResult->response);
                $appErrorCode = "{$xmlBody['appErrorCode']}";
                $httpErrorCode = "{$xmlBody['httpErrorCode']}";
                $errorPayLoad = "{$xmlBody->details}";
            }
            if ($httpErrorCode == 404) {
                throw new App42NotFoundException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 400) {
                throw new App42BadParameterException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 401) {
                throw new App42SecurityException($errorPayLoad, $httpErrorCode, $appErrorCode);
            } else if ($httpErrorCode == 413) {
                throw new App42LimitException($errorPayLoad, $httpErrorCode, 1413);
            } else if ($httpErrorCode == 500) {
                throw new App42Exception($errorPayLoad, $httpErrorCode, $appErrorCode);
            }

            return $returnResult;
        } catch (HttpResponseException $e) {
            throw $e;
        }
    }

    public function setPostBody($postBody) {
        $this->postBody = $postBody;
        return $this;
    }

    /**
     * Convenience method wrapping a commom custom call
     * @param string $method
     * @param string $url
     * @param string $body
     * @param string $user=null [optional]
     * @param string $password=null [optional]
     * @param string $contentType=null [optional]
     * @return RestClient
     */
    public static function call($method, $url, $body, $user = null, $pwd = null, $contentType = null, $accept = null, $postBody = null) {
        return self::createClient($url)
                        ->setParameters($body)
                        ->setMethod($method)
                        ->setCredentials($user, $pwd)
                        ->setContentType($contentType)
                        ->setAccept($accept)
                        ->setpostBody($postBody)
                        ->setAdminKey(array_key_exists('adminKey', $body) ? $body["adminKey"] : null)
                        ->setSessionId(array_key_exists('sessionId', $body) ? $body["sessionId"] : null)
                        ->execute()
                        ->close();
    }

}

?>
