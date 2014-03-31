<?php

namespace com\shephertz\app42\paas\sdk\php\upload;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\upload\UploadResponseBuilder;
use com\shephertz\app42\paas\sdk\php\App42Response;

include_once 'RestClient.class.php';
include_once 'App42Response.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'UploadResponseBuilder.php';
include_once 'App42Exception.php';

/**
 * Uploads file on the cloud. Allows access to the files through url.
 * Its especially useful for Mobile/Device apps. It minimizes the App footprint
 * on the device.
 * 
 */
class UploadService {

    protected $resource = "upload";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * This is a constructor that takes
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
     * Uploads file on the cloud for given user.
     *
     * @params name
     *            - The name of the file which has to be saved. It is used to
     *            retrieve the file
     * @params userName
     *            - The name of the user for which file has to be saved.
     * @params filePath
     *            - The local path for the file
     * @params fileType
     *            - The type of the file. File can be either Audio, Video,
     *            Image, Binary, Txt, xml, json, csv or other Use the static
     *            constants e.g. Upload.AUDIO, Upload.XML etc.
     * @params description
     *            - Description of the file to be uploaded.
     *
     * @return Upload object
     */
    function uploadFileForUser($fileName, $userName, $filePath, $uploadFileType, $description) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($filePath, "FilePath");
        Util::throwExceptionIfNullOrBlank($uploadFileType, "UploadFileType");
        Util::throwExceptionIfNullOrBlank($description, "Description");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($filePath)) {
            throw new App42Exception("The file with the name '$FilePath' not found ");
        }

        $body = null;
        try {
            $uploadTypeObj = new UploadFileType();
            if ($uploadTypeObj->isAvailable($uploadFileType) == "null") {
                throw new App42Exception("The file with  type '$uploadFileType' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $fileName;
            $params['userName'] = $userName;
            $params['type'] = $uploadFileType;
            $params['description'] = $description;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['uploadFile'] = "@" . $filePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $uploadObj;
    }

    /**
     * Gets all the files for the App
     *
     * @return Upload object
     */
    function getAllFiles($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {

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
                $this->url = $this->url;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        } else {

            /**
             * Gets all the files By Paging for the App
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Upload object
             */
            Util::validateMax($max);
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        }
    }

    /**
     * Gets count of all the files for the App
     *
     * @return App42Response object
     */
    function getAllFilesCount() {
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $responseObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
            $uploadResponseObj = new UploadResponseBuilder();
            $responseObj->setTotalRecords($uploadResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Gets the file based on user and file name.
     *
     * @param name
     *            - The name of the file which has to be retrieved
     * @param userName
     *            - The name of the user for which file has to be retrieved
     *
     * @return Upload object
     */
    function getFileByUser($fileName, $userName) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedFileName = Util::encodeParams($fileName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $fileName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedFileName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $uploadObj;
    }

    /**
     * Get all the file based on user name.
     *
     * @param userName
     *            - The name of the user for which file has to be retrieved
     *
     * @return Upload object
     */
    function getAllFilesByUser($userName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            $encodedUserName = Util::encodeParams($userName);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['userName'] = $userName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/user/" . $encodedUserName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        } else {

            /**
             * Get all the files based on user name by Paging.
             *
             * @params userName
             *            - The name of the user for which file has to be retrieved
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return Upload object
             */
            Util::validateMax($max);
            Util::throwExceptionIfNullOrBlank($userName, "User Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedUserName = Util::encodeParams($userName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['userName'] = $userName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/user/" . $encodedUserName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        }
    }

    /**
     * Gets the count of file based on user name.
     *
     * @params userName
     *            - The name of the user for which count of the file has to be
     *            retrieved
     *
     * @return App42Response object
     */
    function getAllFilesCountByUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $responseObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/user/" . $encodedUserName . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
            $uploadResponseObj = new UploadResponseBuilder();
            $responseObj->setTotalRecords($uploadResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes the file based on file name and user name.
     *
     * @params name
     *            - The name of the file which has to be removed
     * @params userName
     *            - The name of the user for which file has to be removed
     *
     * @return App42Response if deleted successfully
     */
    function removeFileByUser($fileName, $userName) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedFileName = Util::encodeParams($fileName);
        $encodedUserName = Util::encodeParams($userName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $fileName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/" . $encodedFileName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($uploadObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes the files based on user name.
     *
     * @param userName
     *            - The name of the user for which files has to be removed
     *
     * @return App42Response if deleted successfully
     */
    function removeAllFilesByUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/user/" . $encodedUserName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($uploadObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes all the files for the App
     *
     * @return App42Response if deleted successfully
     *
     */
    function removeAllFiles() {
        $objUtil = new Util($this->apiKey, $this->secretKey);
        $responseObj = new App42Response();
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
            $this->url = $this->url;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Get the files based on file type.
     *
     * @param uploadFileType
     *            - Type of the file e.g. Upload.AUDIO, Upload.XML etc.
     *
     * @return Upload object
     */
    function getFilesByType($uploadFileType, $max = null, $offset = null) {

        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($uploadFileType, "UploadFileType");
            $encodedUploadFileType = Util::encodeParams($uploadFileType);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $uploadTypeObj = new UploadFileType();
                if ($uploadTypeObj->isAvailable($uploadFileType) == "null") {
                    throw new App42Exception("The file with  type '$uploadFileType' does not Exist ");
                }
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['type'] = $uploadFileType;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/type/" . $encodedUploadFileType;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        } else {

            /**
             * Get the files based on file type by Paging.
             *
             * @params uploadFileType
             *            - Type of the file e.g. Upload.AUDIO, Upload.XML etc.
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @return Upload object
             */
            Util::validateMax($max);
            Util::throwExceptionIfNullOrBlank($uploadFileType, "UploadFileType");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedUploadFileType = Util::encodeParams($uploadFileType);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $uploadTypeObj = new UploadFileType();
                if ($uploadTypeObj->isAvailable($uploadFileType) == "null") {
                    throw new App42Exception("The file with  type '$uploadFileType' does not Exist ");
                }
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['type'] = $uploadFileType;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/type/" . $encodedUploadFileType . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $uploadResponseObj = new UploadResponseBuilder();
                $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $uploadObj;
        }
    }

    /**
     * Get the count of files based on file type.
     *
     * @params uploadFileType
     *            - Type of the file e.g. Upload.AUDIO, Upload.XML etc.
     *
     * @return App42Response object
     */
    function getFilesCountByType($uploadFileType) {
        Util::throwExceptionIfNullOrBlank($uploadFileType, "UploadFileType");
        $encodedUploadFileType = Util::encodeParams($uploadFileType);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $responseObj = new App42Response();
            $uploadTypeObj = new UploadFileType();
            if ($uploadTypeObj->isAvailable($uploadFileType) == "null") {
                throw new App42Exception("The file with  type '$uploadFileType' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['type'] = $uploadFileType;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/type/" . $encodedUploadFileType . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
            $uploadResponseObj = new UploadResponseBuilder();
            $responseObj->setTotalRecords($uploadResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Uploads file on the cloud via Stream.
     *
     * @params name
     *            - The name of the file which has to be saved. It is used to
     *            retrieve the file
     * @params inputStream
     *            - InputStream of the file to be uploaded.
     * @params fileType
     *            - The type of the file. File can be either Audio, Video,
     *            Image, Binary, Txt, xml, json, csv or other Use the static
     *            constants e.g. Upload.AUDIO, Upload.XML etc.
     * @params description
     *            - Description of the file to be uploaded.
     *
     * @return Upload object
     */
    function uploadFile($fileName, $filePath, $uploadFileType, $description) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        Util::throwExceptionIfNullOrBlank($filePath, "FilePath");
        Util::throwExceptionIfNullOrBlank($uploadFileType, "UploadFileType");
        Util::throwExceptionIfNullOrBlank($description, "Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($filePath)) {
            throw new App42Exception("File Not Found");
        }
        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $uploadTypeObj = new UploadFileType();
            if ($uploadTypeObj->isAvailable($uploadFileType) == "null") {
                throw new App42Exception("The file with  type '$uploadFileType' does not Exist ");
            }

            $body = null;
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $fileName;
            $params['type'] = $uploadFileType;
            $params['description'] = $description;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['uploadFile'] = "@" . $filePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $uploadObj;
    }

    /**
     * Gets the file based on file name.
     *
     * @params name
     *            - The name of the file which has to be retrieved
     *
     * @return Upload object
     */
    function getFileByName($fileName) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        $encodedFileName = Util::encodeParams($fileName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $fileName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedFileName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $uploadResponseObj = new UploadResponseBuilder();
            $uploadObj = $uploadResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $uploadObj;
    }

    /**
     * Removes the file based on file name.
     *
     * @params name
     *            - The name of the file which has to be removed
     *
     * @return App42Response if deleted successfully
     */
    function removeFileByName($fileName) {

        Util::throwExceptionIfNullOrBlank($fileName, "File Name");
        $encodedFileName = Util::encodeParams($fileName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $fileName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedFileName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

}

?>