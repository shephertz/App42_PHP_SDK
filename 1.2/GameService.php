<?php

namespace com\shephertz\app42\paas\sdk\php\game;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\game\GameResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'GameResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * The Game service allows Game, User, Score and ScoreBoard Management on the
 * Cloud. The service allows Game Developer to create a Game and then do in Game
 * Scoring using the Score service. It also allows to maintain a Score board
 * across game sessions using the ScoreBoard service. One can query for average
 * or highest score for user for a Game and highest and average score across
 * users for a Game. It also gives ranking of the user against other users for a
 * particular game. The Reward and RewardPoints allows the Game Developer to
 * assign rewards to a user and redeem the rewards. E.g. One can give Swords or
 * Energy etc. The services Game, Score, ScoreBoard, Reward, RewardPoints can be
 * used in Conjunction for complete Game Scoring and Reward Management.
 *
 * @see Reward, RewardPoint, Score, ScoreBoard
 */
class GameService {

    private $version = "1.0";
    private $resource = "game";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    /**
     * Constructor that takes
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
     * Creates a game on the cloud
     *
     * @param gameName
     *            - Name of the game that has to be created
     * @param gameDescription
     *            - Description of the game to be created
     *
     * @return Game object containing the game which has been created
     */
    function createGame($gameName, $gameDescription) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($gameDescription, "Game Description");


        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"game":{"name":"' . $gameName . '","description":"' . $gameDescription . '"}}}';

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
     * Fetches all games for the App
     *
     * @return List of Game object containing all the games for the App
     */
    function getAllGames($max = null, $offset = null) {
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
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        } else {


            /**
             * Fetches all games for the App by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return List of Game object containing all the games for the App
             */
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            Util::validateMax($max);
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
                $gameResponseObj = new GameResponseBuilder();
                $gameObj = $gameResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $gameObj;
        }
    }

    /**
     * Fetches the count of all games for the App
     *
     * @return App42Response object containing count of all the Game for the App
     */
    function getAllGamesCount() {

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $gameObj = new App42Response();
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
            $gameObj->setStrResponse($response->getResponse());
            $gameObj->setResponseSuccess(true);
            $gameResponseObj = new GameResponseBuilder();
            $gameObj->setTotalRecords($gameResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $gameObj;
    }

    /**
     * Retrieves the game by the specified name
     *
     * @param gameName
     *            - Name of the game that has to be fetched
     *
     * @return Game object containing the game which has been created
     */
    function getGameByName($gameName) {

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
            $this->url = $this->url . "/" . $encodedGameName;
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

?>