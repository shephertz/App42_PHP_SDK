<?php

namespace com\shephertz\app42\paas\sdk\php\shopping;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\shopping\CartResponseBuilder;
use com\shephertz\app42\paas\sdk\php\shopping\PaymentStatus;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'CartResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';
include_once 'PaymentStatus.php';

/**
 * This is Cloud Persistent Shopping Cart Service. App Developers can use this
 * to create a Shopping Cart. Add Items and Check Out items. It also maintains
 * the transactions and the corresponding Payment Status. The Payment Gateway
 * interface is not provided by the Platform. It is left to the App developer
 * how he wants to do the Payment Integration. This can be used along with
 * Catalogue or used independently
 * 
 * @see Catalgoue
 * @see Cart
 * @see App42Response
 * @see ItemData
 * @see PaymentStatus
 *
 */
class CartService {

    protected $resource = "cart";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
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
     * Creates a Cart Session for the specified User
     *
     * @params user
     *            - User for whom Cart Session has to be created
     *
     * @returns Cart Object containing Cart Id with Creation Time. The id has to
     *          be used in subsequent calls for adding and checking out
     */
    function createCart($user) {

        Util::throwExceptionIfNullOrBlank($user, "User Name");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"cart":{"userName":"' . $user . '"}}}';
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetch Cart details. Can be used by the App developer to display Cart
     * Details i.e. Items in a Cart.
     *
     * @params cartId
     *            - The Cart Id that has to be fetched
     *
     * @returns Cart object containing cart details with all the items which are
     *          in it. It also tells the state of the Cart
     */
    function getCartDetails($cartId) {
        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        $encodedCartId = Util::encodeParams($cartId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $params['cartId'] = $cartId;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId . "/details";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Adds an Item in the Cart with quantity and price. This method does not
     * take currency. Its the bonus of the App developer to maintainthe
     * currency. It takes only the price.
     *
     * @params cartID
     *            - The Cart Id into which item has to be added
     * @params itemID
     *            - The Item id which has to be added in the cart. If the
     *            Catalogue Service is used along with the Cart Service then the
     *            Item ids should be same.
     * @params itemQuantity
     *            - Quantity of the Item to be purchased
     * @params price
     *            - Price of the item
     *
     * @returns Cart object containing added item.
     *
     */
    function addItem($cartID, $itemID, $itemQuantity, $price) {

        Util::throwExceptionIfNullOrBlank($cartID, "Cart ID");
        Util::throwExceptionIfNullOrBlank($itemID, "Item ID");
        Util::throwExceptionIfNullOrBlank($itemQuantity, "Item Quantity");
        Util::throwExceptionIfNullOrBlank($price, "Price");
        $encodedItemID = Util::encodeParams($itemID);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            $params['itemId'] = $itemID;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"cart":{"cartId":"' . $cartID . '", "item":{"quantity":"' . $itemQuantity . '","amount":"' . $price . '"}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/item/" . $encodedItemID;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetches the Items from the specified Cart
     * 
     * @params cartId
     *            - The cart id from which items have to be fetched
     *
     * @returns Cart object which contains all items in the cart
     */
    function getItems($cartId) {

        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        $encodedCartId = Util::encodeParams($cartId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetches the specified Item from the specified Cart
     *
     * @params cartId
     *            - The cart id from which item has to be fetched
     * @params itemId
     *            - The item for which the information has to be fetched
     *
     * @returns Cart Object
     */
    function getItem($cartId, $itemId) {
        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        Util::throwExceptionIfNullOrBlank($itemId, "Item Id");
        $encodedCartId = Util::encodeParams($cartId);
        $encodedItemId = Util::encodeParams($itemId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId . "/" . $encodedItemId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Removes the specified item from the specified Cart
     *
     * @params cartId
     *            - The cart id from which the item has to be removed
     * @params itemId
     *            - Id of the Item which has to be removed
     *
     * @returns App42Response if removed successfully
     */
    function removeItem($cartId, $itemId) {
        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        Util::throwExceptionIfNullOrBlank($itemId, "Item Id");
        $encodedCartId = Util::encodeParams($cartId);
        $encodedItemId = Util::encodeParams($itemId);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['itemId'] = $itemId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId . "/" . $encodedItemId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($cartObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Removes all Items from the specified Cart
     *
     * @params cartId
     *            - The cart id from which items have to be removed
     *
     * @returns App42Response if removed successfully
     */
    function removeAllItems($cartId) {
        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        $encodedCartId = Util::encodeParams($cartId);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($cartObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Checks whether the Cart is Empty or not
     *
     * @params cartId
     *            - The cart id to check for empty
     *
     * @returns Cart object (isEmpty method on Cart object can be used to check
     *          status)
     */
    function isEmpty($cartId) {

        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        $encodedCartId = Util::encodeParams($cartId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedCartId . "/isEmpty";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Checks out the Cart and put it in CheckOut Stage and returns the
     * Transaction Id The transaction id has to be used in future to update the
     * Payment Status.
     *
     * @params cartID
     *            - The cart id that has to be checkedOut.
     *
     * @returns Cart object containing Checked Out Cart Information with the
     *          Transaction Id
     */
    function checkOut($cartID) {

        Util::throwExceptionIfNullOrBlank($cartID, "Cart ID");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"cart":{"cartId":"' . $cartID . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/checkOut";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Update Payment Status of the Cart. When a Cart is checkout, it is in
     * Checkout state. The payment status has to be updated based on the Payment
     * Gateway interaction
     *
     * @params cartID
     *            - The cart id for which the payment status has to be updated
     * @params transactionID
     *            - Transaction id for which the payment status has to be
     *            updated
     * @params paymentStatus
     *            - Payment Status to be updated. The probable values are
     *            PaymentStatus.DECLINED, PaymentStatus.AUTHORIZED,
     *            PaymentStatus.PENDING
     *
     * @returns Cart object which contains Payment Status
     */
    function payment($cartID, $transactionID, $paymentStatus) {

        Util::throwExceptionIfNullOrBlank($cartID, "Cart ID");
        Util::throwExceptionIfNullOrBlank($transactionID, "Transaction ID");
        Util::throwExceptionIfNullOrBlank($paymentStatus, "Payment Status");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $paymentObj = new PaymentStatus();
            if ($paymentObj->isAvailable($paymentStatus) == "null") {
                throw new App42Exception("Payment Status '$paymentStatus' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"cart":{"cartId":"' . $cartID . '","transactionId":"' . $transactionID . '","status":"' . $paymentStatus . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payment";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * To increase quantity of existing item in the cart.
     *
     * @params cartID
     *            - The Cart Id into which item has to be added
     * @params itemID
     *            - The Item id which has to be added in the cart. If the
     *            Catalogue Service is used along with the Cart Service then the
     *            Item ids should be same.
     * @params itemQuantity
     *            - Quantity of the Item to be purchased
     *
     * @returns Cart object containing updated item.
     */
    function increaseQuantity($cartID, $itemID, $quant) {

        Util::throwExceptionIfNullOrBlank($cartID, "Cart ID");
        Util::throwExceptionIfNullOrBlank($itemID, "Item ID");
        Util::throwExceptionIfNullOrBlank($quant, "Quantity");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"cart":{"cartId":"' . $cartID . '","itemId":"' . $itemID . '","quantity":"' . $quant . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/increaseQuantity";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * To decrease quantity of existing item in the cart..
     *
     * @params cartID
     *            - The Cart Id from where item quantity has to be reduced
     * @params itemID
     *            - The Item id from where item quantity has to be reduced. If
     *            the Catalogue Service is used along with the Cart Service then
     *            the Item ids should be same.
     * @params itemQuantity
     *            - Quantity of the Item has to be reduced
     *
     * @returns Cart object containing updated item.
     */
    function decreaseQuantity($cartID, $itemID, $quant) {

        Util::throwExceptionIfNullOrBlank($cartID, "Cart ID");
        Util::throwExceptionIfNullOrBlank($itemID, "Item ID");
        Util::throwExceptionIfNullOrBlank($quant, "Quantity");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"cart":{"cartId":"' . $cartID . '","itemId":"' . $itemID . '","quantity":"' . $quant . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/decreaseQuantity";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetches Payment information for a User. This can be used to display Order
     * and Payment History
     *
     * @params userId
     *            - User Id for whom payment information has to be fetched
     *
     * @returns List containing Cart objects. Payment history can be retrieved
     *          from individual Cart object.
     */
    function getPaymentsByUser($userId) {

        Util::throwExceptionIfNullOrBlank($userId, "User Id");
        $encodedUserId = Util::encodeParams($userId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userId'] = $userId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payments/user/" . $encodedUserId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetches Payment information for the specified Cart Id
     *
     * @params cartID
     *            - Cart Id for which the payment information has to be fetched
     *
     * @returns Cart object which contains Payment History for the specified
     *          Cart
     */
    function getPaymentsByCart($cartId) {

        Util::throwExceptionIfNullOrBlank($cartId, "Cart Id");
        $encodedCartId = Util::encodeParams($cartId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['cartId'] = $cartId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payments/cart/" . $encodedCartId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * Fetches Payment information based on User Id and Status
     *
     * @params userId
     *            - User Id for whom payment information has to be fetched
     * @params paymentStatus
     *            - Status of type which payment information has to be fetched
     *
     * @returns List containing Cart objects. Payment history can be retrieved
     *          from individual Cart object.
     *
     * @returns Payment History
     */
    function getPaymentsByUserAndStatus($userId, $paymentStatus) {

        Util::throwExceptionIfNullOrBlank($userId, "User Id");
        Util::throwExceptionIfNullOrBlank($paymentStatus, "Payment Status");
        $encodedUserId = Util::encodeParams($userId);
        $encodedPaymentStatus = Util::encodeParams($paymentStatus);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $paymentObj = new PaymentStatus();
            if ($paymentObj->isAvailable($paymentStatus) == "null") {
                throw new App42Exception("Payment Status '$paymentStatus' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userId'] = $userId;
            $params['status'] = $paymentStatus;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payments/user/" . $encodedUserId . "/" . $encodedPaymentStatus;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     *
     * Fetches Payment information based on Status
     * 
     * @params paymentStatus
     *            - Status of type which payment information has to be fetched
     *
     * @returns List containing Cart objects. Payment history can be retrieved
     *          from individual Cart object.
     */
    function getPaymentsByStatus($paymentStatus) {

        Util::throwExceptionIfNullOrBlank($paymentStatus, "Payment Status");
        $encodedPaymentStatus = Util::encodeParams($paymentStatus);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $paymentObj = new PaymentStatus();
            if ($paymentObj->isAvailable($paymentStatus) == "null") {
                throw new App42Exception("Payment Status '$paymentStatus' does not Exist ");
            }
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['status'] = $paymentStatus;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payments/status/" . $encodedPaymentStatus;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * History of Carts and Payments for a User. It gives all the carts which
     * are in AUTHORIZED, DECLINED, PENDING state.
     *
     * @params userId
     *            - User Id for whom payment history has to be fetched
     *
     * @returns List containing Cart objects. Payment history can be retrieved
     *          from individual Cart object.
     */
    function getPaymentHistoryByUser($userId) {

        Util::throwExceptionIfNullOrBlank($userId, "User Id");
        $encodedUserId = Util::encodeParams($userId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userId'] = $userId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/payment/history/" . $encodedUserId;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

    /**
     * History of all carts. It gives all the carts which are in AUTHORIZED,
     * DECLINED, PENDING state.
     *
     * @returns List containing Cart objects. Payment history can be retrieved
     *          from individual Cart object.
     */
    function getPaymentHistoryAll() {

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
            $this->url = $this->url . "/payment/history";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $cartResponseObj = new CartResponseBuilder();
            $cartObj = $cartResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $cartObj;
    }

}

?>
