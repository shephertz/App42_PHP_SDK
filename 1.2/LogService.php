<?php

namespace com\shephertz\app42\paas\sdk\php\log;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\log\LogResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'LogResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * Centralize logging for your App. This service allows different levels e.g.
 * info, debug, fatal, error etc. to log a message and query the messages based
 * on different parameters. You can fetch logs based on module, level, message,
 * date range etc.
 * 
 * @see Log
 *
 */
class LogService {

    private $version = "1.0";
    private $resource = "log";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * this is a constructor that takes
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
     * Logs the info message
     *
     * @param msg
     *            - Message to be logged
     * @param module
     *            - Module name for which the message is getting logged
     *
     * @return Log object containing logged message
     */
    public function info($msg, $module) {
        return $this->buildAndSend($msg, $module, "info");
    }

    /**
     * Logs the debug message
     *
     * @param msg
     *            - Message to be logged
     * @param module
     *            - Module name for which the message is getting logged
     *
     * @return Log object containing logged message
     */
    public function debug($msg, $module) {
        return $this->buildAndSend($msg, $module, "debug");
    }

    /**
     * Logs the fatal message
     *
     * @param msg
     *            - Message to be logged
     * @param module
     *            - Module name for which the message is getting logged
     *
     * @return Log object containing logged message
     */
    public function fatal($msg, $module) {
        return $this->buildAndSend($msg, $module, "fatal");
    }

    /**
     * Logs the error message
     *
     * @param msg
     *            - Message to be logged
     * @param module
     *            - Module name for which the message is getting logged
     *
     * @return Log object containing logged message
     */
    public function error($msg, $module) {
        return $this->buildAndSend($msg, $module, "error");
    }

    /**
     * Builds and Logs the message
     *
     * @param msg
     *            - Message to be logged
     * @param module
     *            - Module name for which the message is getting logged
     * @param level
     *            - The level on which the message is getting logged
     *
     * @return Log object containing logged message
     */
    private function buildAndSend($msg, $module, $level) {
        Util::throwExceptionIfNullOrBlank($msg, "Message");
        Util::throwExceptionIfNullOrBlank($module, "Module");
        Util::throwExceptionIfNullOrBlank($level, "Level");
        $encodedLevel = Util::encodeParams($level);

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"log":{"message":"' . $msg . '","appModule":"' . $module . '"}}}';


            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedLevel;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $logResponseObj = new LogResponseBuilder();
            $logObj = $logResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $logObj;
    }

    /**
     * Fetch the log messages based on the Module
     *
     * @param moduleName
     *            - Module name for which the messages has to be fetched
     *
     * @return Log object containing fetched messages
     */
    function fetchLogsByModule($moduleName, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
            $encodedModuleName = Util::encodeParams($moduleName);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['moduleName'] = $moduleName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/module/" . $encodedModuleName;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        } else {

            /**
             * Fetch the log messages based on the Module by paging.
             *
             * @param moduleName
             *            - Module name for which the messages has to be fetched
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched messages
             */
            Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedModuleName = Util::encodeParams($moduleName);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['moduleName'] = $moduleName;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/module/" . $encodedModuleName . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        }
    }

    /**
     * Fetch the count of log messages based on the Module
     *
     * @param moduleName
     *            - Module name for which the count of messages has to be
     *            fetched
     *
     * @return App42Response object containing count of fetched messages
     */
    function fetchLogsCountByModule($moduleName) {

        Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
        $encodedModuleName = Util::encodeParams($moduleName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $logObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['moduleName'] = $moduleName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/module/" . $encodedModuleName . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $logObj->setStrResponse($response->getResponse());
            $logObj->setResponseSuccess(true);
            $logResponseObj = new LogResponseBuilder();
            $logObj->setTotalRecords($logResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $logObj;
    }

    /**
     * Fetch log messages based on the Module and Message Text
     *
     * @param moduleName
     *            - Module name for which the messages has to be fetched
     * @param text
     *            - The log message on which logs have to be searched
     *
     * @return Log object containing fetched messages
     */
    function fetchLogsByModuleAndText($moduleName, $text, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
            Util::throwExceptionIfNullOrBlank($text, "Text");
            $encodedModuleName = Util::encodeParams($moduleName);
            $encodedText = Util::encodeParams($text);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['moduleName'] = $moduleName;
                $params['text'] = $text;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/module/" . $encodedModuleName . "/text/" . $encodedText;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        } else {

            /**
             * Fetch log messages based on the Module and Message Text by paging.
             *
             * @param moduleName
             *            - Module name for which the messages has to be fetched
             * @param text
             *            - The log message on which logs have to be searched
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             * 
             * @return Log object containing fetched messages
             */
            Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
            Util::throwExceptionIfNullOrBlank($text, "Text");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedModuleName = Util::encodeParams($moduleName);
            $encodedText = Util::encodeParams($text);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['moduleName'] = $moduleName;
                $params['text'] = $text;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/module/" . $encodedModuleName . "/text/" . $encodedText . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        }
    }

    /**
     * Fetch count of log messages based on the Module and Message Text
     *
     * @param moduleName
     *            - Module name for which the count of messages has to be
     *            fetched
     * @param text
     *            - The log message on which count of logs have to be searched
     *
     * @return App42Response object containing count of fetched messages
     *
     */
    function fetchLogsCountByModuleAndText($moduleName, $text) {

        Util::throwExceptionIfNullOrBlank($moduleName, "Module Name");
        Util::throwExceptionIfNullOrBlank($text, "Text");
        $encodedModuleName = Util::encodeParams($moduleName);
        $encodedText = Util::encodeParams($text);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $logObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['moduleName'] = $moduleName;
            $params['text'] = $text;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/module/" . $encodedModuleName . "/text/" . $encodedText . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $logObj->setStrResponse($response->getResponse());
            $logObj->setResponseSuccess(true);
            $logResponseObj = new LogResponseBuilder();
            $logObj->setTotalRecords($logResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $logObj;
    }

    /**
     * Fetch the log messages based on the Level
     *
     * @param level
     *            - The level on which logs have to be searched
     *
     * @return Log object containing fetched messages
     */
    private function fetchLogsByLevel($level, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($level, "Level");
            $encodedLevel = Util::encodeParams($level);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['type'] = $level;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/type/" . $encodedLevel;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        } else {

            /**
             * Fetch the log messages based on the Level by paging.
             *
             * @param level
             *            - The level on which logs have to be searched
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched messages
             */
            Util::throwExceptionIfNullOrBlank($level, "Level");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
            $encodedLevel = Util::encodeParams($level);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['type'] = $level;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/type/" . $encodedLevel . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        }
    }

    /**
     * Fetch count of log messages based on Info Level
     *
     * @return App42Response object containing count of fetched info messages
     */
    private function fetchLogsCountByLevel($level) {

        Util::throwExceptionIfNullOrBlank($level, "Level");
        $encodedLevel = Util::encodeParams($level);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $logObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['type'] = $level;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/type/" . $encodedLevel . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $logObj->setStrResponse($response->getResponse());
            $logObj->setResponseSuccess(true);
            $logResponseObj = new LogResponseBuilder();
            $logObj->setTotalRecords($logResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $logObj;
    }

    /**
     * Fetch count of log messages based on Info Level
     *
     * @return App42Response object containing count of fetched info messages
     */
    public function fetchLogsCountByInfo() {
        return $this->fetchLogsCountByLevel("INFO");
    }

    /**
     * Fetch count of log messages based on Debug Level
     *
     * @return App42Response object containing count of fetched debug messages
     */
    public function fetchLogsCountByDebug() {
        return $this->fetchLogsCountByLevel("DEBUG");
    }

    /**
     * Fetch count of log messages based on Error Level
     *
     * @return App42Response object containing count of fetched error messages
     */
    public function fetchLogsCountByError() {
        return $this->fetchLogsCountByLevel("ERROR");
    }

    /**
     * Fetch count of log messages based on Fatal Level
     *
     * @return App42Response object containing count of fetched Fatal messages
     */
    public function fetchLogsCountByFatal() {
        return $this->fetchLogsCountByLevel("FATAL");
    }

    /**
     * Fetch log messages based on Info Level
     *
     * @return Log object containing fetched info messages
     */
    public function fetchLogsByInfo($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            return $this->fetchLogsByLevel("INFO");
        } else {
            /**
             * Fetch log messages based on Info Level by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched info messages
             */
            return $this->fetchLogsByLevel("INFO", $max, $offset);
        }
    }

    /**
     * Fetch log messages based on Debug Level
     *
     * @return Log object containing fetched debug messages
     */
    public function fetchLogsByDebug($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            return $this->fetchLogsByLevel("DEBUG");
        } else {

            /**
             * Fetch log messages based on Debug Level by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             * 
             * @return Log object containing fetched debug messages
             */
            return $this->fetchLogsByLevel("DEBUG", $max, $offset);
        }
    }

    /**
     * Fetch log messages based on Error Level
     *
     * @return Log object containing fetched error messages
     */
    public function fetchLogsByError($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            return $this->fetchLogsByLevel("ERROR");
        } else {

            /**
             * Fetch log messages based on Error Level by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched error messages
             */
            return $this->fetchLogsByLevel("ERROR", $max, $offset);
        }
    }

    /**
     * Fetch log messages based on Fatal Level
     *
     * @return Log object containing fetched Fatal messages
     */
    public function fetchLogsByFatal($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            return $this->fetchLogsByLevel("FATAL");
        } else {

            /**
             * Fetch log messages based on Fatal Level by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched Fatal messages
             *
             * @throws App42Exception
             *
             */
            return $this->fetchLogsByLevel("FATAL", $max, $offset);
        }
    }

    /**
     * Fetch log messages based on Date range
     *
     * @param startDate
     *            - Start date from which the log messages have to be fetched
     * @param endDate
     *            - End date upto which the log messages have to be fetched
     *
     * @return Log object containing fetched messages
     */
    function fetchLogByDateRange($startDate, $endDate, $max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($startDate, "Start Date");
            Util::throwExceptionIfNullOrBlank($endDate, "End Date");

            $validateStartDate = Util::validateDate($startDate);
            $validateEndDate = Util::validateDate($endDate);
            $encodedStartDate = Util::encodeParams($startDate);
            $encodedEndDate = Util::encodeParams($endDate);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {

                $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
                $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['startDate'] = $strStartDate;
                $params['endDate'] = $strEndDate;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/startDate/" . $strStartDate . "/endDate/" . $strEndDate;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        } else {

            /**
             * Fetch log messages based on Date range by paging.
             *
             * @param startDate
             *            - Start date from which the log messages have to be fetched
             * @param endDate
             *            - End date upto which the log messages have to be fetched
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return Log object containing fetched messages
             *
             */
            Util::throwExceptionIfNullOrBlank($startDate, "Start Date");
            Util::throwExceptionIfNullOrBlank($endDate, "End Date");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);

            $validateStartDate = Util::validateDate($startDate);
            $validateEndDate = Util::validateDate($endDate);
            $encodedStartDate = Util::encodeParams($startDate);
            $encodedEndDate = Util::encodeParams($endDate);
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {

                $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
                $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['startDate'] = $strStartDate;
                $params['endDate'] = $strEndDate;
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/startDate/" . $strStartDate . "/endDate/" . $strEndDate . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $logResponseObj = new LogResponseBuilder();
                $logObj = $logResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $logObj;
        }
    }

    /**
     * Fetch count of log messages based on Date range
     *
     * @param startDate
     *            - Start date from which the count of log messages have to be
     *            fetched
     * @param endDate
     *            - End date upto which the count of log messages have to be
     *            fetched
     *
     * @return App42Response object containing count of fetched messages
     */
    function fetchLogCountByDateRange($startDate, $endDate) {

        Util::throwExceptionIfNullOrBlank($startDate, "Start Date");
        Util::throwExceptionIfNullOrBlank($endDate, "End Date");

        $validateStartDate = Util::validateDate($startDate);
        $validateEndDate = Util::validateDate($endDate);
        $encodedStartDate = Util::encodeParams($startDate);
        $encodedEndDate = Util::encodeParams($endDate);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
            $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");
            $logObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['startDate'] = $strStartDate;
            $params['endDate'] = $strEndDate;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/startDate/" . $strStartDate . "/endDate/" . $strEndDate . "/count";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $logObj->setStrResponse($response->getResponse());
            $logObj->setResponseSuccess(true);
            $logResponseObj = new LogResponseBuilder();
            $logObj->setTotalRecords($logResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $logObj;
    }

}

?>