<?php

namespace com\shephertz\app42\paas\sdk\php\email;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\email\EmailResponseBuilder;
use com\shephertz\app42\paas\sdk\php\email\EmailMIME;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'EmailResponseBuilder.php';
include_once 'EmailMIME.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * This Service is used to send Emails.
 *  This service can be used by app to send mail to one or multiple recipients.
 */
class EmailService {

    private $version = "1.0";
    private $resource = "email";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * this is a constructor that takes
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
     * Creates Email Configuration using which in future the App developer can
     * send mail
     *
     * @params emailHost
     *            - Email Host to be used for sending mail
     * @params emailPort
     *            - Email Port to be used for sending mail
     * @params mailId
     *            - Email id to be used for sending mail
     * @params emailPassword
     *            - Email Password to be used for sending mail
     * @params isSSL
     *            - Should be send using SSL or not
     *
     * @return Email object containing the email configuration which has been
     *         created
     */
    function createMailConfiguration($emailHost, $emailPort, $mailId, $emailPassword, $isSSL) {

        Util::throwExceptionIfNullOrBlank($emailHost, "Host");
        Util::throwExceptionIfNullOrBlank($emailPort, "Port");
        Util::throwExceptionIfNullOrBlank($mailId, "Email Id");
        Util::throwExceptionIfNullOrBlank($emailPassword, "Password");
        Util::throwExceptionIfNullOrBlank($isSSL, "isSSL");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"email":{"host":"' . $emailHost . '","port":"' . $emailPort . '","emailId":"' . $mailId . '","password":"' . $emailPassword . '","ssl":"' . $isSSL . '"}}}';


            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/configuration";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $emailResponseObj = new EmailResponseBuilder();
            $emailObj = $emailResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $emailObj;
    }

    /**
     * Removes email configuration for the given email id. Note: In future the
     * developer wont be able to send mails through this id
     *
     * @params emailId
     *            - The email id for which the configuration has to be removed
     *
     * @return App42Response object containing the email id which has been
     *         removed
     */
    function removeEmailConfiguration($emailId) {

        Util::throwExceptionIfNullOrBlank($emailId, "Email Id");
        $encodedEmailId = Util::encodeParams($emailId);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['emailId'] = $emailId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/configuration/" . $encodedEmailId;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $emailResponseObj = new EmailResponseBuilder();
            $emailObj = $emailResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($emailObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Gets all Email Configurations for the app
     *
     * @return Email object containing all Email Configurations
     */
    function getEmailConfigurations() {


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
            $this->url = $this->url . "/configuration";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $emailResponseObj = new EmailResponseBuilder();
            $emailObj = $emailResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $emailObj;
    }

    /**
     * This Service is used to send Emails. This service can be used by app to send mail to one or multiple recipients.
     *
     * @params sendTo
     *            - The email ids to which the email has to be sent. Email can
     *            be sent to multiple email ids. Multiple email ids can be
     *            passed using comma as the separator e.g. sid@shephertz.com,
     *            info@shephertz.com
     * @params sendSubject
     *            - Subject of the Email which to be sent
     * @params sendMsg
     *            - Email body which has to be sent
     * @params fromEmail
     *            - The Email Id using which the mail(s) has to be sent
     * @params emailMime
     *            - MIME Type to be used for sending mail. EmailMIME available
     *            options are PLAIN_TEXT_MIME_TYPE or HTML_TEXT_MIME_TYPE
     *
     * @return Email object containing all the details used for sending mail
     */
    function sendMail($fromEmail, $sendTo, $sendSubject, $sendMsg, $emailMIME) {
        Util::throwExceptionIfNullOrBlank($fromEmail, "Email Id");
        Util::throwExceptionIfNullOrBlank($sendTo, "Send To");
        Util::throwExceptionIfNullOrBlank($sendSubject, "Send Subject");
        Util::throwExceptionIfNullOrBlank($sendMsg, "Send Message");
        Util::throwExceptionIfNullOrBlank($emailMIME, "EmailMIME");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $emailMIMETypeObj = new EmailMIME();
            if ($emailMIMETypeObj->isAvailable($emailMIME) == "null") {
                throw new App42Exception("The EmailMIME with  type '$emailMIME' does not Exist ");
            }

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"email":{"emailId":"' . $fromEmail . '","to":"' . $sendTo . '","subject":"' . $sendSubject . '","msg":"' . $sendMsg . '","mimeType":"' . $emailMIME . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $emailResponseObj = new EmailResponseBuilder();
            $emailObj = $emailResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $emailObj;
    }

}

?>