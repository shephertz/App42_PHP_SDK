<?php

namespace com\shephertz\app42\paas\sdk\php\imageProcessor;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\ImageProcessor\ImageProcessorResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'App42Exception.php';
include_once "ImageProcessorResponseBuilder.php";

/**
 * The ImageProcessor service is a Image utility service on the Cloud. Developers can 
 * upload files on the cloud and perform various Image Manipulation operations on the Uploaded
 * Images e.g. resize, scale, thumbnail, crop etc. It is especially useful for Mobile Apps when 
 * they dont want to store Images locally and dont want to perform processor intensive operations.
 * It is also useful for web applications who want to perform complex Image Operations
 *
 *
 */
class ImageProcessorService {

    private $version = "1.0";
    private $resource = "image";
    private $apiKey;
    private $secretKey;
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
     * Resize image. Returns the original image url and converted image url.
     * Images are stored on the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image to resize
     * @params imagePath Path of the local file to resize
     * @params width Width of the image to resize
     * @params height Height of the image to resize
     * 
     * @returns Image object containing urls for the original and converted images
    */
    function resize($name, $imagePath, $width, $height) {
        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($width, "Width");
        Util::throwExceptionIfNullOrBlank($height, "Height");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");
            header('Content-type: image/jpg');
        $objUtil = new Util($this->apiKey, $this->secretKey);
         $homepage = file_get_contents($imagePath);
       // echo"ffffffffffffff";
       // print_r($homepage);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['width'] = $width . "";
            $params['height'] = $height . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $homepage;

           //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/resize";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Creates a thumbnail of the image. There is a difference between thumbnail and resize
     * The thumbnail operation is optimized for speed, it removes information of the image which is not necessary for a
     * thumbnail e.g hearder information. Returns the original image url and converted image url.
     * Images are stored on the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image file for which thumbnail has to be created
     * @params imagePath Path of the local file whose thumbnail has to be created
     * @params width Width of the image for thumbnail
     * @params height Height of the image for thumbnail
     * 
     * @returns Image object containing urls for the original and converted images
     */
    function thumbnail($name, $imagePath, $width, $height) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($width, "Width");
        Util::throwExceptionIfNullOrBlank($height, "Height");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['width'] = $width . "";
            $params['height'] = $height . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . thumbnail;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Scales the image based on width and height. Returns the original image url and converted image url.
     * Images are stored in the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image to scale
     * @params imagePath Path of the local file to scale
     * @params width Width of the image to scale
     * @params height Height of the image to scale
     * 
     * @returns Image object containing urls for the original and converted images
     */
    function scale($name, $imagePath, $width, $height) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($width, "Width");
        Util::throwExceptionIfNullOrBlank($height, "Height");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['width'] = $width . "";
            $params['height'] = $height . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . scale;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Crops image based on width, height and x, y coordinates. Returns the original image url and converted image url.
     * Images are stored in the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image to crop
     * @params imagePath Path of the local file to crop
     * @params width Width of the image to crop
     * @params height Height of the image to crop
     * @params x Coordinate X
     * @params y Coordinate Y
     *
     * @returns Image object containing urls for the original and converted images
      */
    function crop($name, $imagePath, $width, $height, $x, $y) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($width, "Width");
        Util::throwExceptionIfNullOrBlank($height, "Height");
        Util::throwExceptionIfNullOrBlank($x, "X Coordinate");
        Util::throwExceptionIfNullOrBlank($y, "Y Coordinate");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['width'] = $width . "";
            $params['height'] = $height . "";
            $params['x'] = $x . "";
            $params['y'] = $y . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . crop;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Resize image by Percentage. Returns the original image url and converted image url.
     * Images are stored in the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image to resize
     * @params imagePath Path of the local file to resize
     * @params percentage Percentage to which image has to be resized
     * @returns Image object containing urls for the original and converted images
    */
    function resizeByPercentage($name, $imagePath, $percentage) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($percentage, "Percentage");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['percentage'] = $percentage . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . resizePercentage;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Creates a thumbnail of the image by Percentage. There is a difference between thumbnail and resize
     * The thumbnail operation is optimized for speed removes information of the image which is not necessary for a 
     * thumbnail to reduce size e.g hearder information. Returns the original image url and converted image url.
     * Images are stored in the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image file for which thumbnail has to be created
     * @params imagePath Path of the local file whose thumbnail has to be created
     * @params percentage Percentage for thumbnail
     * @returns Image object containing  urls for the original and converted images
     */
    function thumbnailByPercentage($name, $imagePath, $percentage) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($percentage, "Percentage");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['percentage'] = $percentage . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . thumbnailPercentage;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

    /**
     * Scales the image  by Percentage. Returns the original image url and converted image url.
     * Images are stored in the cloud and can be accessed through the urls
     * Resizing is done based on the width and height provided
     * @params name Name of the image file to scale
     * @params imagePath Path of the local file to scale
     * @params percentage Percentage to which image has to be scaled
     * 
     * @returns Image object containing  urls for the original and converted images
     */
    function scaleByPercentage($name, $imagePath, $percentage) {

        Util::throwExceptionIfNullOrBlank($name, "Name");
        Util::throwExceptionIfNullOrBlank($imagePath, "Image Path");
        Util::throwExceptionIfNullOrBlank($percentage, "Percentage");
        Util::throwExceptionIfNotValidImageExtension($name,"Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        //$file = fopen($filePath, r);
        if (!file_exists($imagePath)) {
            throw new App42Exception(" File " . $imagePath . " does not exist");
        }

        //$file = new File($filePath);
        //if(!file_exists($file)){
        //throw Exception
        //}
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['name'] = $name;
            $params['percentage'] = $percentage . "";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $imagePath;
            //CONTENT_TYPE == "multipart/form-data"
            $contentType = "multipart/form-data";
            $accept = $this->accept;
            $this->url = $this->url . "/" . scalePercentage;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $imageResponseObj = new ImageProcessorResponseBuilder();
            $imageObj = $imageResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $imageObj;
    }

}
?>