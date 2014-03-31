<?php

namespace com\shephertz\app42\paas\sdk\php\review;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\review\ReviewResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'ReviewResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * The service is a Review & Rating manager for any item. The item can be
 * anything which has an id e.g. App on a AppStore/Marketplace, items in a
 * catalogue, articles, blogs etc. It manages the comments and its associated
 * rating. It also provides methods to fetch average, highest etc. Reviews.
 * Reviews can be also be muted or unmuted if it has any objectionable content.
 * 
 * @see Review
 *
 */
class ReviewService {

    protected $resource = "review";
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
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Creates review for the specified item on the cloud
     *
     * @param userID
     *            - The user who has created the review
     * @param itemID
     *            - The item for which the review has to be created
     * @param reviewComment
     *            - The review comment
     * @param reviewRating
     *            - Review rating in double
     *
     * @return Review object containing the review which has been created

     */
    function createReview($userID, $itemID, $reviewComment, $reviewRating) {

        Util::throwExceptionIfNullOrBlank($userID, "User ID");
        Util::throwExceptionIfNullOrBlank($itemID, "Item ID");
        Util::throwExceptionIfNullOrBlank($reviewComment, "Review Comment");
        Util::throwExceptionIfNullOrBlank($reviewRating, "Review Rating");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"review":{"userId":"' . $userID . '","itemId":"' . $itemID . '","comment":"' . $reviewComment . '","rating":"' . $reviewRating . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Fetches all reviews for the App
     *
     * @return list of Review object containing all the reviews for the App
     */
    function getAllReviews($max = null, $offset = null) {
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
                $reviewResponseObj = new ReviewResponseBuilder();
                $reviewObj = $reviewResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $reviewObj;
        } else {

            /**
             * Fetches all reviews for the App by Paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return list of Review object containing all the reviews for the App
             */
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);

            Util::validateMax($max);
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
                $reviewResponseObj = new ReviewResponseBuilder();
                $reviewObj = $reviewResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $reviewObj;
        }
    }

    /**
     * Fetches count of all reviews for the App
     *
     * @return App42Response containing count of all the reviews for the App
     */
    function getAllReviewsCount() {

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $reviewObj = new App42Response();
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
            $reviewObj->setStrResponse($response->getResponse());
            $reviewObj->setResponseSuccess(true);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj->setTotalRecords($reviewResponseObj->getTotalRecords($response->getResponse()));
            //$reviewObj = $reviewResponseObj->getTotalRecords($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Fetches All Reviews based on the itemId
     *
     * @param itemId
     *            - The item for which reviews have to be fetched
     *
     * @return list of Review object containing all the reviews for a item
     */
    function getReviewByItem($itemId, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
            $encodedItemId = Util::encodeParams($itemId);

            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['itemId'] = $itemId;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/item/" . $encodedItemId;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $reviewResponseObj = new ReviewResponseBuilder();
                $reviewObj = $reviewResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $reviewObj;
        } else {

            /**
             * Fetches All Reviews based on the itemId by Paging.
             *
             * @param itemId
             *            - The item for which reviews have to be fetched
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return list of Review object containing all the reviews for a item
             */
            Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedItemId = Util::encodeParams($itemId);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            Util::validateMax($max);

            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['itemId'] = $itemId;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/item/" . $encodedItemId . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $reviewResponseObj = new ReviewResponseBuilder();
                $reviewObj = $reviewResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $reviewObj;
        }
    }

    /**
     * Fetches count of All Reviews based on the itemId
     *
     * @param itemId
     *            - The item for which count of reviews have to be fetched
     *
     * @return App42Response containing count of all the reviews for a item
     */
    function getReviewsCountByItem($itemId) {

        Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $reviewObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/item/" . $encodedItemId . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $reviewObj->setStrResponse($response->getResponse());
            $reviewObj->setResponseSuccess(true);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj->setTotalRecords($reviewResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Fetches the average review for the specified itemId
     * 
     * @param itemId
     *            - The item for which the average review has to be fetched
     *
     * @return Review object containing the average review for a item
     */
    function getAverageReviewByItem($itemId) {

        Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedItemId . "/average";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Fetches the highest review for the specified itemId
     *
     * @param itemId
     *            - The item for which the highest review has to be fetched
     *
     * @return Review object containing the highest review for a item
     */
    function getHighestReviewByItem($itemId) {
        Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedItemId . "/highest";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Fetches the lowest review for the specified itemId
     *
     * @param itemId
     *            - The item for which the lowest review has to be fetched
     *
     * @return Review object containing the lowest review for a item
     */
    function getLowestReviewByItem($itemId) {

        Util::throwExceptionIfNullOrBlank($itemId, "Item ID");
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedItemId . "/lowest";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * Mutes the specified review
     *
     * @param reviewId
     *            - The Id of the review which has to be muted
     *
     * @return App42Response if muted successfully
     */
    function mute($reviewId) {

        Util::throwExceptionIfNullOrBlank($reviewId, "Review ID");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"review":{"id":"' . $reviewId . '"}}}';


            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/mute";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

    /**
     * UnMutes the specified review
     *
     * @param reviewId
     *            - The Id of the review which has to be unmuted
     *
     * @return App42Response if unmuted successfully
     */
    function unmute($reviewId) {

        Util::throwExceptionIfNullOrBlank($reviewId, "Review ID");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"review":{"id":"' . $reviewId . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/unmute";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $reviewResponseObj = new ReviewResponseBuilder();
            $reviewObj = $reviewResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $reviewObj;
    }

}

?>
