<?php

namespace com\shephertz\app42\paas\sdk\php\game;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\game\GameResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'GameResponseBuilder.php';
include_once 'App42Exception.php';

/**
 * ScoreBoard allows storing, retrieving, querying and ranking scores for users
 * and Games across Game Session. The Game service allows Game, User, Score and
 * ScoreBoard Management on the Cloud. The service allows Game Developer to
 * create a Game and then do in Game Scoring using the Score service. It also
 * allows to maintain a Scoreboard across game sessions using the ScoreBoard
 * service. One can query for average or highest score for user for a Game and
 * highest and average score across users for a Game. It also gives ranking of
 * the user against other users for a particular game. The Reward and
 * RewardPoints allows the Game Developer to assign rewards to a user and redeem
 * the rewards. E.g. One can give Swords or Energy etc. The services Game,
 * Score, ScoreBoard, Reward, RewardPoints can be used in Conjunction for
 * complete Game Scoring and Reward Management.
 * 
 * @see Game, RewardPoint, RewardPoint, Score
 *
 */
class ScoreBoardService {

    private $version = "1.0";
    private $resource = "game/scoreboard";
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
     * Saves the User score for a game
     *
     * @param gameName
     *            - Name of the game for which score has to be saved
     * @param gameUserName
     *            - The user for which score has to be saved
     * @param gameScore
     *            - The sore that has to be saved
     *
     * @return the saved score for a game
     */
    function saveUserScore($gameName, $gameUserName, $gameScore) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($gameUserName, "User Name");
        Util::throwExceptionIfNullOrBlank($gameScore, "Score");


        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"game":{"name":"' . $gameName . '", "scores":{"score":{"userName":"' . $gameUserName . '","value":"' . $gameScore . '"}}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the scores for a game for the specified name
     *
     * @param gameName
     *            - Name of the game for which score has to be fetched
     * @param userName
     *            - The user for which score has to be fetched
     *
     * @return the game score for the specified user
     */
    function getScoresByUser($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the highest game score for the specified user
     *
     * @param gameName
     *            - Name of the game for which highest score has to be fetched
     * @param userName
     *            - The user for which highest score has to be fetched
     *
     * @return the highest game score for the specified user
     */
    function getHighestScoreByUser($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName . "/" . highest;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the lowest game score for the specified user
     *
     * @param gameName
     *            - Name of the game for which lowest score has to be fetched
     * @param userName
     *            - The user for which lowest score has to be fetched
     *
     * @return the lowest game score for the specified user

     */
    function getLowestScoreByUser($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName . "/" . lowest;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the Top Rankings for the specified game
     *
     * @param gameName
     *            - Name of the game for which ranks have to be fetched
     *
     * @return the Top rankings for a game
     */
    function getTopRankings($gameName, $startDate = null, $endDate = null) {
        $argv = func_get_args();
        if (count($argv) == 1) {
            Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
            $encodedGameName = Util::encodeParams($gameName);

            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['name'] = $gameName;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedGameName . "/ranking";
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        } else {

            /**
             * Retrieves the Top Rankings for the specified game
             * 
             * @param gameName
             *            - Name of the game for which ranks have to be fetched
             * @param startDate
             *            -Start date from which the ranking have to be fetched
             * @param endDate
             *            - End date up to which the ranking have to be fetched
             * @return the Top rankings for a game
             */
            Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
            Util::throwExceptionIfNullOrBlank($startDate, "Start Date");
            Util::throwExceptionIfNullOrBlank($endDate, "End Date");
            $encodedGameName = Util::encodeParams($gameName);
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
                $params['name'] = $gameName;
                $params['startDate'] = $strStartDate;
                $params['endDate'] = $strEndDate;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedGameName . "/ranking" . "/" . $strStartDate . "/" . $strEndDate;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        }
    }

    /**
     * Retrieves the Top Rankings for the specified game
     *
     * @param gameName
     *            - Name of the game for which ranks have to be fetched
     * @param max
     *            - Maximum number of records to be fetched
     *
     * @return the Top rankings for a game
     */
    function getTopNRankings($gameName, $max) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($max, "Max");
        Util::validateMax($max);
        $encodedGameName = Util::encodeParams($gameName);
        $encodedMax = Util::encodeParams($max);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['max'] = $max;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/ranking/" . $encodedMax;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the User Ranking for the specified game
     *
     * @param gameName
     *            - Name of the game for which ranks have to be fetched
     * @param userName
     *            - Name of the user for which ranks have to be fetched
     *
     * @return the rank of the User
     *
     */
    function getUserRanking($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName . "/ranking";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the average game score for the specified user
     *
     * @param gameName
     *            - Name of the game for which average score has to be fetched
     * @param userName
     *            - The user for which average score has to be fetched
     *
     * @return the average game score for the specified user
     */
    function getAverageScoreByUser($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName . "/average";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * This function returns the top ranking based on user score
     * 
     * @param gameName
     *            - Name of the game
     * @param userList
     *            - List of the user for which ranking has to retrieve
     * @return Game oObject
     */
    function getTopRankingsByGroup($gameName, $userList) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userList, "User List");
        $encodedGameName = Util::encodeParams($gameName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            if (is_array($userList)) {
                $params['userList'] = json_encode($userList);
            } else {
                $params['userList'] = $userList;
            }
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/group";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * This function returns the score attained by the specified user in the
     * last game session.
     * 
     * @param userName
     *            - Name of the for which score has to retrieve.
     * @return Game Object
     */
    function getLastGameScore($userName) {

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
            $this->url = $this->url . "/" . $encodedUserName . "/lastgame";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * This function returns the top score attained by the specified user in the
     * game.
     * 
     * @param gameName
     *            - Name of the game
     * @param userName
     *            - Name of the user for which score has to retrieve
     * @return Game Object
     */
    function getLastScoreByUser($gameName, $userName) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName . "/lastscore";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * This function returns the specified number of top rankers in a specific
     * game.
     * 
     * @param gameName
     *            - Name of the game
     * @param max
     *            - Maximum number of records to be fetched
     * @return Game Object
     */
    function getTopNRankers($gameName, $max, $startDate = null, $endDate = null) {
        $argv = func_get_args();
        if (count($argv) == 2) {
            Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            $encodedGameName = Util::encodeParams($gameName);
            $encodedMax = Util::encodeParams($max);
            Util::validateMax($max);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['name'] = $gameName;
                $params['max'] = $max;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedGameName . "/rankers/" . $encodedMax;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        } else {
            /**
             * 
             * @param gameName
             * @param startDate
             * @param endDate
             * @param max
             * @return
             */
            Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
            Util::throwExceptionIfNullOrBlank($startDate, "Start Date");
            Util::throwExceptionIfNullOrBlank($endDate, "End Date");
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::validateMax($max);
            $encodedGameName = Util::encodeParams($gameName);
            $encodedMax = Util::encodeParams($max);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $strStartDate = (date("Y-m-d\TG:i:s", strtotime($startDate)) . substr((string) microtime(), 1, 4) . "Z");
                $strEndDate = (date("Y-m-d\TG:i:s", strtotime($endDate)) . substr((string) microtime(), 1, 4) . "Z");

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['name'] = $gameName;
                $params['startDate'] = $strStartDate;
                $params['endDate'] = $strEndDate;
                $params['max'] = $max;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/" . $encodedGameName . "/rankers/" . $strStartDate . "/" . $strEndDate . "/" . $encodedMax;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        }
    }

    /**
     * 
     * @param gameName
     * @param userList
     * @return
     */
    function getTopNRankersByGroup($gameName, $userList) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($userList, "User List");
        $encodedGameName = Util::encodeParams($gameName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $gameName;
            if (is_array($userList)) {
                $params['userList'] = json_encode($userList);
            } else {
                $params['userList'] = $userList;
            }
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/rankers/group";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj = $gameResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * 
     * @param scoreId
     * @param gameScore
     * @return
     * @throws App42Exception
     */
    function editScoreValueById($scoreId, $gameScore) {

        Util::throwExceptionIfNullOrBlank($scoreId, "Score Id");
        Util::throwExceptionIfNullOrBlank($gameScore, "Game Score");

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $body = null;
            $body = '{"app42":{"game":{"scores":{"score":{"scoreId":"' . $scoreId . '","value":"' . $gameScore . '"}}}}}';
            $params['body'] = $body;

            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/editscore";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $scoreObj = new GameResponseBuilder();
            $scoreObj1 = $scoreObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $scoreObj1;
    }

    /**
     * Retrieves the scores for a game for the specified name
     *
     * @param gameName
     *            - Name of the game for which score has to be fetched
     * @param userName
     *            - The user for which score has to be fetched
     *
     * @return the game score for the specified user
     */
    function getScoresByDateRange($startDate, $endDate, $max = null, $offset = null) {
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
                print_r($strStartDate);
                print_r($strEndDate);
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
                $this->url = $this->url . "/all/" . $strStartDate . "/" . $strEndDate;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $scoreObj = new GameResponseBuilder();
                $scoreObj1 = $scoreObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $scoreObj1;
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
                $this->url = $this->url . "/paging/" . $strStartDate . "/" . $strEndDate . "/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $scoreObj = new GameResponseBuilder();
                $scoreObj1 = $scoreObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $scoreObj1;
        }
    }

}

?>