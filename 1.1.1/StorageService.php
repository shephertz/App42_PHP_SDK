<?php

namespace com\shephertz\app42\paas\sdk\php\storage;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\storage\StorageResponseBuilder;
use com\shephertz\app42\paas\sdk\php\JSONObject;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'StorageResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';
include_once 'App42Log.php';
include_once "JSONObject.php";

/**
 * Storage service on cloud provides the way to store the JSON document in NoSQL
 * database running on cloud. One can store the JSON document, update it ,
 * searchit and can apply the map-reduce search on stored documents. Example :
 * If one will store JSON doc {"date":"5Feb"} it will be stored with unique
 * Object Id and stored JSON object will looklike { "date" : "5Feb" , "_id" :
 * { "$oid" : "4f423dcce1603b3f0bd560cf"}}. This oid can further be used to
 * access/search the document.
 * 
 * @see Storage
 * @see App42Response
 *
 */
class StorageService {

    private $version = "1.0";
    private $resource = "storage";
    private $apiKey;
    private $secretKey;
    private $sessionId;
    private $adminKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * this is a constructor that takes
     *
     * @params apiKey
     * @params secretKey
     * @params baseURL
     *
     */
    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Save the JSON document in given database name and collection name.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc has to be saved
     * @params json
     *            - Target JSON document to be saved
     *
     * @return Storage object
     */
    function insertJSONDocument($dbName, $collectionName, $json) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($json, "JSON");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);

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
            $body = '{"app42":{"storage":{"jsonDoc":' . $json . '}}}';
            // App42Log::debug($body);
            $params['body'] = $body;
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/insert" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * Find all documents stored in given database and collection.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     *
     * @return Storage object
     *
     */
    function findAllDocuments($dbName, $collectionName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
            Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
            $encodedDbName = Util::encodeParams($dbName);
            $encodedCollectionName = Util::encodeParams($collectionName);
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
                $params['dbName'] = $dbName;
                $params['collectionName'] = $collectionName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/findAll" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $storageResponseObj = new StorageResponseBuilder();
                $storageObj = $storageResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $storageObj;
        } else {
            /**
             * Find all documents stored in given database and collection.
             *
             * @params dbName
             *            - Unique handler for storage name
             * @params collectionName
             *            - Name of collection under which JSON doc needs to be searched
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return Storage object

             */
            Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
            Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedDbName = Util::encodeParams($dbName);
            $encodedCollectionName = Util::encodeParams($collectionName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
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
                $params['dbName'] = $dbName;
                $params['collectionName'] = $collectionName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/findAll" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $storageResponseObj = new StorageResponseBuilder();
                $storageObj = $storageResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $storageObj;
        }
    }

    /**
     * Find target document by given unique object id.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params docId
     *            - Unique Object Id handler
     * 
     * @return Storage object
     */
    function findDocumentById($dbName, $collectionName, $docId) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($docId, "DocumentId");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedDocId = Util::encodeParams($docId);
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['docId'] = $docId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findDocById" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/docId" . "/" . $encodedDocId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            //return $response;

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * Find target document using key value search parameter. This key value
     * pair will be searched in the JSON doc stored on the cloud and matching
     * Doc will be returned as a result of this method.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params key
     *            - Key to be searched for target JSON doc
     * @params value
     *            - Value to be searched for target JSON doc
     *
     * @return Storage object
     */
    function findDocumentByKeyValue($dbName, $collectionName, $key, $value) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($key, "KEY");
        Util::throwExceptionIfNullOrBlank($value, "Value");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedKey = Util::encodeParams($key);
        $encodedValue = Util::encodeParams($value);
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['key'] = $key;
            $params['value'] = $value;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findDocByKV" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/" . $encodedKey . "/" . $encodedValue;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            //return $response;

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * Update target document using key value search parameter. This key value
     * pair will be searched in the JSON doc stored in the cloud and matching
     * Doc will be updated with new value passed.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params key
     *            - Key to be searched for target JSON doc
     * @params value
     *            - Value to be searched for target JSON doc
     * @params newJsonDoc
     *            - New Json document to be added
     *
     * @return Storage object

     */
    function updateDocumentByKeyValue($dbName, $collectionName, $key, $value, $newJsonDoc) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($key, "KEY");
        Util::throwExceptionIfNullOrBlank($value, "Value");
        Util::throwExceptionIfNullOrBlank($newJsonDoc, "New Json Document");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedKey = Util::encodeParams($key);
        $encodedValue = Util::encodeParams($value);
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
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"storage":{"jsonDoc":"' . $newJsonDoc . '"}}}}';

            $params['body'] = $body;
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['key'] = $key;
            $params['value'] = $value;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/update" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/" . $encodedKey . "/" . $encodedValue;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            //return $response;

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * Update target document using the document id.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params docId
     *            - Id of the document to be searched for target JSON doc
     * @params newJsonDoc
     *            - New Json document to be added
     *
     * @return Storage object

     */
    function updateDocumentByDocId($dbName, $collectionName, $docId, $newJsonDoc) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($docId, "Document Id");
        Util::throwExceptionIfNullOrBlank($newJsonDoc, "New Json Document");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedDocId = Util::encodeParams($docId);
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
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"storage":{"jsonDoc":"' . $newJsonDoc . '"}}}}';
            $params['body'] = $body;
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['docId'] = $docId;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/updateByDocId" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/docId" . "/" . $encodedDocId;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            //return $response;

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * Delete target document using Object Id from given db and collection. The
     * Object Id will be searched in the JSON doc stored on the cloud and
     * matching Doc will be deleted.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params docId
     *            - Unique Object Id handler
     *
     * @return App42Response object if deleted successfully

     */
    function deleteDocumentById($dbName, $collectionName, $docId) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($docId, "DocumentID");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedDocId = Util::encodeParams($docId);
        $responseObj = new App42Response();
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['docId'] = $docId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/deleteDocById" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName . "/docId" . "/" . $encodedDocId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            //return $response;

            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($storageObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Map reduce function to search the target document. Please see detail
     * information on map-reduce http://en.wikipedia.org/wiki/MapReduce
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     * @params mapFunction
     *            - Map function to be used to search the document
     * @params reduceFunction
     *            - Reduce function to be used to search the document
     *
     * @return Returns the target JSON document.
     *
     */
    function mapReduce($dbName, $collectionName, $mapFunction, $reduceFunction) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($mapFunction, "Map Function");
        Util::throwExceptionIfNullOrBlank($reduceFunction, "Reduce Function");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
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
            $body = '{"app42":{"storage":{"map":"' . $mapFunction . '","reduce":"' . $reduceFunction . '"}}}';
            $params['body'] = $body;
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/mapReduce" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $response;
    }

    /**
     * Save the JSON document in given database name and collection name. It
     * accepts the HashMap containing key-value and convert it into JSON.
     * Converted JSON doc further saved on the cloud using given db name and
     * collection name.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc has to be saved
     * @params map
     *            - HashMap containing key-value pairs
     *
     * @return Storage object

     */
    public function insertJsonDocUsingMap($dbName, $collectionName, $map) {

        $jsonBody = $this->getJsonFromMap($map);
        return $this->insertJSONDocument($dbName, $collectionName, $jsonBody);
    }

    /**
     * Gets the count of all documents stored in given database and collection.
     *
     * @params dbName
     *            - Unique handler for storage name
     * @params collectionName
     *            - Name of collection under which JSON doc needs to be searched
     *
     * @return App42Response object

     */
    function findAllDocumentsCount($dbName, $collectionName) {

        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $storageObj = new App42Response();
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findAll" . "/count" . "/dbName" . "/" . $encodedDbName . "/collectionName" . "/" . $encodedCollectionName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $storageObj->setStrResponse($response->getResponse());
            $storageObj->setResponseSuccess(true);
            $storageResponseObj = new StorageResponseBuilder();
            $storageObj->setTotalRecords($storageResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    /**
     * @param map
     *
     * @return
     */
    private function getJsonFromMap($map) {
        Util::throwExceptionIfNullOrBlank($map, "Map");
        $sb = array();
        array_push($sb, '{');
        if ($map != null && count($map) != 0) {
            $keySet = array_keys($map);
            $i = 0;
            $totalCount = count($keySet);
            foreach ($keySet as $key) {
                $i++;
                $value = $map[$key];
                //$sb->append("\"" + $key + "\"" + ":" + "\"" + $value + "\"");
                array_push($sb, "\"" . $key . "\"" . ":" . "\"" . $value . "\" ");
                if ($totalCount > 1 && $i != $totalCount)
                    array_push($sb, ',');
            }
        }

        array_push($sb, '}');
        return implode("", $sb);
    }

    function findDocumentsByQuery($dbName, $collectionName, $query) {
        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($query, "query");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        $queryObject = null;
        if ($query instanceof JSONObject) {
            $queryObject = array();
            array_push($queryObject, $query);
        } else {
            $queryObject = $query;
        }
        try {
            $storageObj = new App42Response();
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['jsonQuery'] = json_encode($queryObject);
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findDocsByQuery" . "/dbName/" . $encodedDbName . "/collectionName/" . $encodedCollectionName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $storageResponseObj = new StorageResponseBuilder();
            $storagekObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            echo($e->getMessage());
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storagekObj;
    }

    function findDocumentsByQueryWithPaging($dbName, $collectionName, $query, $max, $offset) {
        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($query, "query");
        Util::validateMax($max);
        Util::throwExceptionIfNullOrBlank($max, "Max");
        Util::throwExceptionIfNullOrBlank($offset, "Offset");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedMax = Util::encodeParams($max);
        $encodedOffset = Util::encodeParams($offset);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        $queryObject = null;
        if ($query instanceof JSONObject) {
            $queryObject = array();
            array_push($queryObject, $query);
        } else {
            $queryObject = $query;
        }
        try {
            $storageObj = new App42Response();
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['jsonQuery'] = json_encode($queryObject);
            $params['max'] = $max;
            $params['offset'] = $offset;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findDocsByQuery" . "/dbName/" . $encodedDbName . "/collectionName/" . $encodedCollectionName . "/" . $encodedMax . "/" . $encodedOffset;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $storageObj->setStrResponse($response->getResponse());
            $storageObj->setResponseSuccess(true);
            $storageResponseObj = new StorageResponseBuilder();
            $storagekObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            echo($e->getMessage());
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storagekObj;
    }

    function findDocsWithQueryPagingOrderBy($dbName, $collectionName, $query, $max, $offset, $orderByKey, $type) {
        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($query, "query");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedMax = Util::encodeParams($max);
        $encodedOffset = Util::encodeParams($offset);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        $queryObject = null;
        if ($query instanceof JSONObject) {
            $queryObject = array();
            array_push($queryObject, $query);
        } else {
            $queryObject = $query;
        }
        try {
            $orderByTypeObj = new OrderByType();
            if ($orderByTypeObj->isAvailable($type) == "null") {
                throw new App42Exception("The OrderByType '$type' does not Exist ");
            }
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
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['jsonQuery'] = json_encode($queryObject);
            $params['max'] = $max;
            $params['offset'] = $offset;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['orderByKey'] = $orderByKey;
            $params['orderByType'] = $type;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/findDocsByQuery" . "/dbName/" . $encodedDbName . "/collectionName/" . $encodedCollectionName . "/" . $encodedMax . "/" . $encodedOffset;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $storageResponseObj = new StorageResponseBuilder();
            $storageObj = $storageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            echo($e->getMessage());
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    function grantAccessOnDoc($dbName, $collectionName, $docId, $aclList) {
        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($docId, "DocumentID");
        Util::throwExceptionIfNullOrBlank($aclList, "ACL List");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedDocId = Util::encodeParams($docId);

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
            $aclArray = array();
            foreach ($aclList as $acl) {
                $aclValue = $acl->getJSONObject();
                array_push($aclArray, $aclValue);
            }
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"storage":{"acls": { "acl": ' . json_encode($aclArray) . '}}}}';
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['docId'] = $docId;
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/grantAccessOnDoc" . "/" . $encodedDbName . "/" . $encodedCollectionName . "/" . $encodedDocId;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $storage = new StorageResponseBuilder();
            $storageObj = $storage->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
    }

    function revokeAccessOnDoc($dbName, $collectionName, $docId, $aclList) {
        Util::throwExceptionIfNullOrBlank($dbName, "DataBase Name");
        Util::throwExceptionIfNullOrBlank($collectionName, "Collection Name");
        Util::throwExceptionIfNullOrBlank($docId, "DocumentID");
        Util::throwExceptionIfNullOrBlank($aclList, "ACL List");
        $encodedDbName = Util::encodeParams($dbName);
        $encodedCollectionName = Util::encodeParams($collectionName);
        $encodedDocId = Util::encodeParams($docId);

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
            $aclArray = array();
            foreach ($aclList as $acl) {
                $aclValue = $acl->getJSONObject();
                array_push($aclArray, $aclValue);
            }
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"storage":{"acls": { "acl": ' . json_encode($aclArray) . '}}}}';
            $params['dbName'] = $dbName;
            $params['collectionName'] = $collectionName;
            $params['docId'] = $docId;
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/revokeAccessOnDoc" . "/" . $encodedDbName . "/" . $encodedCollectionName . "/" . $encodedDocId;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $storage = new StorageResponseBuilder();
            $storageObj = $storage->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $storageObj;
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
