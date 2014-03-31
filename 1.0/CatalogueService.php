<?php

namespace com\shephertz\app42\paas\sdk\php\shopping;

/**
 * This Service provides a complete cloud based catalogue management. An app can
 * keep all its items based on category on the Cloud. This service provides
 * several utility methods to manage catalogue on the cloud. One can add items
 * with its related information in a particular category. And there can be
 * several categories in a catalogue. The App developer can create several
 * catalogues if needed.
 *
 * The Cart service can be used along with Catalogue service to create an end to
 * end Shopping feature for a Mobile and Web App
 *
 * @see Cart, ItemData
 * 
 */
use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\shopping\ItemData;
use com\shephertz\app42\paas\sdk\php\shopping\CatalogueResponseBuilder;
use com\shephertz\app42\paas\sdk\php\App42Log;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'ItemData.php';
include_once 'CatalogueResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Log.php';
include_once 'App42Response.php';

class CatalogueService {

    private $version = "1.0";
    private $resource = "catalogue";
    private $apiKey;
    private $secretKey;
    private $baseURL;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * Constructor that takes
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
     * Creates a Catalogue for a particular App. Categories can be added to the
     * Catalogue
     *
     * @params catalogueName
     *            - Name of the Catalogue to be created
     * @params catalogueDescription
     *            - Description of the catalogue to be created
     *
     * @returns Catalogue object
     */
    function createCatalogue($catalogueName, $catalogueDescription) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($catalogueDescription, "Catalogue Description");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"catalogue":{"name":"' . $catalogueName . '","description":"' . $catalogueDescription . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Creates a Category for a particular Catalogue e.g. Books, Music etc.
     *
     * @params catalogueName
     *            - Name of the Catalogue for which Category has to be created
     * @params categoryName
     *            - Name of the Category that has to be created
     * @params categoryDescription
     *            - Description of the category to be created
     *
     * @returns Catalogue object containing created category information
     */
    function createCategory($catalogueName, $categoryName, $categoryDescription) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($categoryDescription, "Catagory Description");
        Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
        $encodedCatName = Util::encodeParams($catalogueName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"catalogue":{"categories":{"category":{"name":"' . $categoryName . '","description":"' . $categoryDescription . '"}}}}}';

            $params['body'] = $body;
            $params['catalogueName'] = $catalogueName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/category";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Creates a Item in a Category for a particular Catelogue
     *
     * @params catalogueName
     *            - Name of the Catalogue to which item has to be added
     * @params categoryName
     *            - Name of the Category to which item has to be added
     * @params itemData
     *            - Item Information that has to be added
     *
     * @returns Catalogue object containing added item.
     * @see ItemData
     *
     */
    function addItem($catalogueName, $categoryName, ItemData $itemData) {
        $imagePath = $itemData->getImage();
        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($itemData, "Item Data");
        Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
        Util::throwExceptionIfNotValidImageExtension($imagePath, "imagePath");
        $encodedCatName = Util::encodeParams($catalogueName);
        $encodedCategoryName = Util::encodeParams($categoryName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        //$file = fopen($filePath, r);
        if (!file_exists($itemData->image)) {
            throw new App42Exception("File Not Found");
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
            $params['catalogueName'] = $catalogueName;
            $params['categoryName'] = $categoryName;
            $params['itemId'] = $itemData->itemId;
            $params['name'] = $itemData->name;
            $params['description'] = $itemData->description;
            $params['price'] = $itemData->price;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $params['imageFile'] = "@" . $itemData->image;
            $contentType = "multipart/form-data";
            $body = null;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName . "/item";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Fetches all items for a Catalogue
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be fetched
     *
     * @returns Catalogue object containing all Items
     */
    function getItems($catalogueName) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        $encodedCatName = Util::encodeParams($catalogueName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['catalogueName'] = $catalogueName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Fetches all items for a Catalogue and Category
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be fetched
     * @params categoryName
     *            - Name of the Category from which item has to be fetched
     *
     * @returns Catalogue object
     */
    function getItemsByCategory($catalogueName, $categoryName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
            Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
            $encodedCatName = Util::encodeParams($catalogueName);
            $encodedCategoryName = Util::encodeParams($categoryName);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['catalogueName'] = $catalogueName;
                $params['categoryName'] = $categoryName;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                App42Log::debug($response);
                $catalogueResponseObj = new CatalogueResponseBuilder();
                $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $catalogueObj;
        } else {

            /**
             * Fetches all items for a Catalogue and Category by paging.
             *
             * @params catalogueName
             *            - Name of the Catalogue from which item has to be fetched
             * @params categoryName
             *            - Name of the Category from which item has to be fetched
             * @params max
             *            - Maximum number of records to be fetched
             * @params offset
             *            - From where the records are to be fetched
             *
             * @returns Catalogue object
             */
            Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
            Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedCatName = Util::encodeParams($catalogueName);
            $encodedCategoryName = Util::encodeParams($categoryName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $params['catalogueName'] = $catalogueName;
                $params['categoryName'] = $categoryName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/" . $encodedCatName . "/" . $encodedCategoryName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                App42Log::debug($response);
                $catalogueResponseObj = new CatalogueResponseBuilder();
                $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $catalogueObj;
        }
    }

    /**
     * Fetches count of all items for a Catalogue and Category
     *
     * @params catalogueName
     *            - Name of the Catalogue from which count of item has to be
     *            fetched
     * @params categoryName
     *            - Name of the Category from which count of item has to be
     *            fetched
     *
     * @returns App42Response object
     */
    function getItemsCountByCategory($catalogueName, $categoryName) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
        $encodedCatName = Util::encodeParams($catalogueName);
        $encodedCategoryName = Util::encodeParams($categoryName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $catalogueObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['catalogueName'] = $catalogueName;
            $params['categoryName'] = $categoryName;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            App42Log::debug($response);
            $catalogueObj->setStrResponse($response->getResponse());
            $catalogueObj->setResponseSuccess(true);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj->setTotalRecords($catalogueResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Fetches Item by id for a Catalogue and Category
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be fetched
     * @params categoryName
     *            - Name of the Category from which item has to be fetched
     * @params itemId
     *            - Item id for which information has to be fetched.
     *
     * @returns Catalogue object
     */
    function getItemById($catalogueName, $categoryName, $itemId) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($categoryName, "Catagory Name");
        Util::throwExceptionIfNullOrBlank($itemId, "Item Id");
        $encodedCatName = Util::encodeParams($catalogueName);
        $encodedCategoryName = Util::encodeParams($categoryName);
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['catalogueName'] = $catalogueName;
            $params['categoryName'] = $categoryName;
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName . "/" . $encodedItemId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $catalogueObj;
    }

    /**
     * Removes all the Items of the given Catalogue.
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be removed
     *
     * @returns App42Response object containing all removed items
     */
    function removeAllItems($catalogueName) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "catalogueName");
        $encodedCatName = Util::encodeParams($catalogueName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['catalogueName'] = $catalogueName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($catalogueObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes all the Items from a given Catalogue and Category
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be removed
     * @params categoryName
     *            - Name of the Category from which item has to be removed
     *            returns App42Response object containing removed items
     *
     * @returns App42Response object containing removed items by category
     */
    function removeItemsByCategory($catalogueName, $categoryName) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($categoryName, "Category Name");
        $encodedCatName = Util::encodeParams($catalogueName);
        $encodedCategoryName = Util::encodeParams($categoryName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['catalogueName'] = $catalogueName;
            $params['categoryName'] = $categoryName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($catalogueObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes the Item for the given Id
     *
     * @params catalogueName
     *            - Name of the Catalogue from which item has to be removed
     * @params categoryName
     *            - Name of the Category from which item has to be removed
     * @params itemId
     *            - Item id which has to be removed returns App42Response object
     *            containing removed items
     *
     * @returns App42Response object containing removed items by ID
     */
    function removeItemById($catalogueName, $categoryName, $itemId) {

        Util::throwExceptionIfNullOrBlank($catalogueName, "Catalogue Name");
        Util::throwExceptionIfNullOrBlank($categoryName, "Category Name");
        Util::throwExceptionIfNullOrBlank($itemId, "Item Id");
        $encodedCatName = Util::encodeParams($catalogueName);
        $encodedCategoryName = Util::encodeParams($categoryName);
        $encodedItemId = Util::encodeParams($itemId);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['catalogueName'] = $catalogueName;
            $params['categoryName'] = $categoryName;
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCatName . "/" . $encodedCategoryName . "/" . $encodedItemId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $catalogueResponseObj = new CatalogueResponseBuilder();
            $catalogueObj = $catalogueResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($catalogueObj);
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