<?php

namespace com\shephertz\app42\paas\sdk\php\gallery;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\gallery\AlbumResponseBuilder;
use com\shephertz\app42\paas\sdk\php\App42Log;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'AlbumResponseBuilder.php';
include_once 'App42Log.php';
include_once 'App42Response.php';

/**
 * Adds Photo to the created Album on the Cloud All photos for a given Album can
 * be managed through this service. Photos can be uploaded to the cloud.
 * Uploaded photos are accessible through the generated URL. The service also
 * creates a thumbnail for the Photo which has been uploaded.
 *
 * @see Album
 * @see Photo
 */
class PhotoService {

    private $version = "1.0";
    private $resource = "gallery";
    private $apiKey;
    private $secretKey;
    private $sessionId;
    private $adminKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * The costructor for the Service
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
     * @param dbName Unique handler for storage name
     * @param collectionName Name of collection under which JSON doc has to be saved.
     * @param json Target JSON document to be saved
     * @return Returns the saved document containing Object Id as a unique handler of saved document.
     * 
     */
    public function testUpload($path) {
        return;
    }

    /**
     * Adds Photo for a particular user and album. The Photo is uploaded on the
     * cloud
     *
     * @params userName
     *            - Name of the User whose photo has to be added
     * @params albumName
     *            - Name of the Album in which photo has to be added
     * @params photoName
     *            - Name of the Photo that has to be added
     * @params photoDescription
     *            - Description of the Photo that has to be added
     * @params path
     *            - Path from where Photo has to be picked for addition
     *
     * @return Album object containing the Photo which has been added
     */
    function addPhoto($userName, $albumName, $photoName, $photoDescription, $path) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        Util::throwExceptionIfNullOrBlank($photoName, "Photo Name");
        Util::throwExceptionIfNullOrBlank($photoDescription, "Description");
        Util::throwExceptionIfNullOrBlank($path, "Path");
        Util::throwExceptionIfNotValidImageExtension($path, "Photo Path");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        if (!file_exists($path)) {
            throw new App42Exception("File Not Found");
        }
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
            $params['userName'] = $userName;
            $params['albumName'] = $albumName;
            $params['name'] = $photoName;
            $params['description'] = $photoDescription;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $path;
            $contentType = "multipart/form-data";
            $body = null;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            App42Log::debug($response);
            $photoResponseObj = new AlbumResponseBuilder();
            $photoObj = $photoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $photoObj;
    }

    /**
     * Fetches all the Photos based on the userName
     *
     * @params userName
     *            - Name of the User whose photos have to be fetched
     *
     * @return List of Album object containing all the Photos for the given
     *         userName
     */
    public function getPhotos($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        $albumList = array();
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
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $photoResponseObj = new AlbumResponseBuilder();
            $albumList = $photoResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumList;
    }

    /**
     * Fetches all Photos based on the userName and albumName
     *
     * @params userName
     *            - Name of the User whose photos have to be fetched
     * @params albumName
     *            - Name of the Album from which photos have to be fetched
     *
     * @return Album object containing all the Photos for the given userName and
     *         albumName
     */
    public function getPhotosByAlbumName($userName, $albumName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
            $encodedUserName = Util::encodeParams($userName);
            $encodedAlbumName = Util::encodeParams($albumName);
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
                $params['albumName'] = $albumName;
                $params['userName'] = $userName;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedAlbumName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $photoResponseObj = new AlbumResponseBuilder();
                $photoObj = $photoResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $photoObj;
        } else {

            /**
             * Fetches all Photos based on the userName and album name by paging.
             *
             * @params userName
             *            - Name of the User whose photos have to be fetched
             * @params albumName
             *            - Name of the Album from which photos have to be fetched
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return Album object containing all the Photos for the given userName and
             *         albumName
             */
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedUserName = Util::encodeParams($userName);
            $encodedAlbumName = Util::encodeParams($albumName);
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
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['albumName'] = $albumName;
                $params['userName'] = $userName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/album/" . $encodedUserName . "/" . $encodedAlbumName . "/paging/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $photoResponseObj = new AlbumResponseBuilder();
                $photoObj = $photoResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $photoObj;
        }
    }

    /**
     * Fetches the count of all Photos based on the userName and album name
     *
     * @params userName
     *            - Name of the User whose count of photos have to be fetched
     * @params albumName
     *            - Name of the Album from which count of photos have to be
     *            fetched
     *
     * @return App42Response object containing the count of all the Photos for
     *         the given userName and albumName
     */
    public function getPhotosCountByAlbumName($userName, $albumName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedAlbumName = Util::encodeParams($albumName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $photoObj = new App42Response();
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
            $params['albumName'] = $albumName;
            $params['userName'] = $userName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedAlbumName . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $photoObj->setStrResponse($response->getResponse());
            $photoObj->setResponseSuccess(true);
            $photoResponseObj = new AlbumResponseBuilder();
            $photoObj->setTotalRecords($photoResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $photoObj;
    }

    /**
     * Fetches the particular photo based on the userName, album name and photo
     * name
     *
     * @params userName
     *            - Name of the User whose photo has to be fetched
     * @params albumName
     *            - Name of the Album from which photo has to be fetched
     * @params photoName
     *            - Name of the Photo that has to be fetched
     *
     * @return Album object containing the Photo for the given userName,
     *         albumName and photoName
     */
    public function getPhotosByAlbumAndPhotoName($userName, $albumName, $photoName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        Util::throwExceptionIfNullOrBlank($photoName, "Photo Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedAlbumName = Util::encodeParams($albumName);
        $encodedPhotoName = Util::encodeParams($photoName);

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
            $params['albumName'] = $albumName;
            $params['userName'] = $userName;
            $params['name'] = $photoName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedAlbumName . "/" . $encodedPhotoName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $photoResponseObj = new AlbumResponseBuilder();
            $photoObj = $photoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $photoObj;
    }

    /**
     * Removes the particular Photo from the specified Album for a particular
     * user. Note: The Photo is removed from the cloud and wont be accessible in
     * future
     *
     * @param userName
     *            - Name of the User whose photo has to be removed
     * @param albumName
     *            - Name of the Album in which photo has to be removed
     * @param photoName
     *            - Name of the Photo that has to be removed
     *
     * @return App42Response if removed successfully

     */
    public function removePhoto($userName, $albumName, $photoName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($albumName, "Album Name");
        Util::throwExceptionIfNullOrBlank($photoName, "Photo Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedAlbumName = Util::encodeParams($albumName);
        $encodedPhotoName = Util::encodeParams($photoName);

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
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['albumName'] = $albumName;
            $params['userName'] = $userName;
            $params['name'] = $photoName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedAlbumName . "/" . $encodedPhotoName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $photoResponseObj = new AlbumResponseBuilder();
            $photoObj = $photoResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($photoObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Add tags to the Photos for the user in the album.
     *
     * @params userName
     *            - Name of the User whose name has to be tagged to photo
     * @params albumName
     *            - Album name whose photo is to be tagged
     * @params photoName
     *            - Photo name to be tagged
     * @params tagList
     *            - List of tages in Photo
     *
     * @return Album object containing the Photo which has been added
     */
    function addTagToPhoto($uName, $albName, $phName, $tagList) {
        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($albName, "Album Name");
        Util::throwExceptionIfNullOrBlank($phName, "Photo Name");
        Util::throwExceptionIfNullOrBlank($tagList, "Tag");
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
            if (is_array($tagList)) {
                $body = '{"app42":{"photo":{"userName":"' . $uName . '","photoName":"' . $phName . '", "albumName":"' . $albName . '", "tags": { "tag": ' . json_encode($tagList) . '}}}}';
            } else {

                $body = '{"app42":{"photo":{"userName":"' . $uName . '","photoName":"' . $phName . '", "albumName":"' . $albName . '", "tags": { "tag": "' . $tagList . '"}}}}';
            }
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/tag";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            App42Log::debug($response);
            $photoResponseObj = new AlbumResponseBuilder();
            $photoObj = $photoResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {

            throw new App42Exception($e);
        }
        return $photoObj;
    }

    /**
     * Fetches all the Photos based on the userName and tag
     *
     * @params userName
     *            - Name of the User whose photos have to be fetched
     * @params tag
     *            - tag on which basis photos have to be fetched
     *
     * @return List of Album object containing all the Photos for the given
     *         userName
     */
    public function getTaggedPhotos($userName, $tag) {
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($tag, "Tag Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedTag = Util::encodeParams($tag);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        $albumList = array();
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
            $params['userName'] = $userName;
            $params['tag'] = $tag;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/tag/" . $encodedTag . "/userName/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $photoResponseObj = new AlbumResponseBuilder();
            $albumList = $photoResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumList;
    }

    function grantAccess($albName, $uName, $phName, $aclList) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($albName, "Album Name");
        Util::throwExceptionIfNullOrBlank($phName, "Photo Name");
        Util::throwExceptionIfNullOrBlank($aclList, "ACL List");
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
            $body = '{"app42":{"photo":{"acls": { "acl": ' . json_encode($aclArray) . '}}}}';
            print_r($body);

            $params['albumName'] = $albName;
            $params['photoName'] = $phName;
            $params['userName'] = $uName;
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/grantAccess" . "/" . $uName . "/" . $albName . "/" . $phName;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $album = new AlbumResponseBuilder();
            $albumObj = $album->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumObj;
    }

    function revokeAccess($albName, $uName, $phName, $aclList) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($albName, "Album Name");
        Util::throwExceptionIfNullOrBlank($phName, "Photo Name");
        Util::throwExceptionIfNullOrBlank($aclList, "ACL List");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;

            $sessionId = $this->sessionId;
            if ($sessionId != null) {
                print_r($this->sessionId);
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
            $body = '{"app42":{"photo":{"acls": { "acl": ' . json_encode($aclArray) . '}}}}';
            print_r($body);

            $params['albumName'] = $albName;
            $params['photoName'] = $phName;
            $params['userName'] = $uName;
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/revokeAccess" . "/" . $uName . "/" . $albName . "/" . $phName;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $album = new AlbumResponseBuilder();
            $albumObj = $album->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $albumObj;
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
