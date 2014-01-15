<?php

namespace com\shephertz\app42\paas\sdk\php\game;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\game\RewardResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'RewardResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

/**
 * Define a Reward e.g. Sword, Energy etc. Is needed for Reward Points
 *
 * The Game service allows Game, User, Score and ScoreBoard Management on the
 * Cloud. The service allows Game Developer to create a Game and then do in Game
 * Scoring using the Score service. It also allows to maintain a Scoreboard
 * across game sessions using the ScoreBoard service. One can query for average
 * or highest score for user for a Game and highest and average score across
 * users for a Game. It also gives ranking of the user against other users for a
 * particular game. The Reward and RewardPoints allows the Game Developer to
 * assign rewards to a user and redeem the rewards. E.g. One can give Swords or
 * Energy etc. The services Game, Score, ScoreBoard, Reward, RewardPoints can be
 * used in Conjunction for complete Game Scoring and Reward Management.
 * 
 * @see Game, RewardPoint, Score, ScoreBoard
 */
class RewardService {

    private $version = "1.0";
    private $resource = "game/reward";
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
     * Creates Reward. Reward can be Sword, Energy etc. When Reward Points have
     * to be added the Reward name created using this method has to be
     * specified.
     *
     * @param rewardName
     *            - The reward that has to be created
     * @param rewardDescription
     *            - The description of the reward to be created
     *
     * @return Reward object containing the reward that has been created
     */
    function createReward($rewardName, $rewardDescription) {

        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        Util::throwExceptionIfNullOrBlank($rewardDescription, "Reward Description");


        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"rewards":{"reward":{"name":"' . $rewardName . '","description":"' . $rewardDescription . '"}}}}';


            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * Fetches all the Rewards
     *
     * @return List of Reward objects containing all the rewards of the App
     */
    function getAllRewards($max = null, $offset = null) {
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
                $rewardResponseObj = new RewardResponseBuilder();
                $rewardObj = $rewardResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $rewardObj;
        } else {

            /**
             * Fetches all the Rewards by paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return List of Reward objects containing all the rewards of the App
             *
             * @throws App42Exception
             *
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
                $rewardResponseObj = new RewardResponseBuilder();
                $rewardObj = $rewardResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $rewardObj;
        }
    }

    /**
     * Fetches the count of all the Rewards
     *
     * @return App42Response objects containing count of all the rewards of the
     *         App
     */
    function getAllRewardsCount() {

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $rewardObj = new App42Response();
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
            $rewardObj->setStrResponse($response->getResponse());
            $rewardObj->setResponseSuccess(true);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj->setTotalRecords($rewardResponseObj->getTotalRecords($response->getResponse()));
            //$rewardObj = $rewardResponseObj->getTotalRecords($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * Retrieves the reward for the specified name
     *
     * @param rewardName
     *            - Name of the reward that has to be fetched
     *
     * @return Reward object containing the reward based on the rewardName
     */
    function getRewardByName($rewardName) {

        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        $encodedRewardName = Util::encodeParams($rewardName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['name'] = $rewardName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedRewardName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * Adds the reward points to an users account. Reward Points can be earned
     * by the user which can be redeemed later.
     *
     * @param gameName
     *            - Name of the game for which reward points have to be added
     * @param gameUserName
     *            - The user for whom reward points have to be added
     * @param rewardName
     *            - The rewards for which reward points have to be added
     * @param rewardPoints
     *            - The points that have to be added
     *
     * @return Reward object containing the reward points that has been added
     */
    function earnRewards($gName, $gUserName, $rewardName, $rewardPoints) {

        Util::throwExceptionIfNullOrBlank($gName, "Game Name");
        Util::throwExceptionIfNullOrBlank($gUserName, "User Name");
        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        Util::throwExceptionIfNullOrBlank($rewardPoints, "Reward Point");


        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"rewards":{"reward":{"gameName":"' . $gName . '","userName":"' . $gUserName . '","name":"' . $rewardName . '","points":"' . $rewardPoints . '"}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/earn";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * Deducts the reward points from the earned rewards by a user.
     *
     * @param gameName
     *            - Name of the game for which reward points have to be deducted
     * @param gameUserName
     *            - The user for whom reward points have to be deducted
     * @param rewardName
     *            - The rewards for which reward points have to be deducted
     * @param rewardPoints
     *            - The points that have to be deducted
     *
     * @return Reward object containing the reward points that has been deducted
     */
    function redeemRewards($gName, $gUserName, $rewardName, $rewardPoints) {

        Util::throwExceptionIfNullOrBlank($gName, "Game Name");
        Util::throwExceptionIfNullOrBlank($gUserName, "User Name");
        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        Util::throwExceptionIfNullOrBlank($rewardPoints, "Reward Point");


        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;

            $body = '{"app42":{"rewards":{"reward":{"gameName":"' . $gName . '","userName":"' . $gUserName . '","name":"' . $rewardName . '","points":"' . $rewardPoints . '"}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/redeem";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * Fetches the reward points for a particular user
     *
     * @param gameName
     *            - Name of the game for which reward points have to be fetched
     * @param userName
     *            - The user for whom reward points have to be fetched
     *
     * @return Reward object containing the reward points for the specified user
     */
    function getGameRewardPointsForUser($gameName, $userName) {

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
            $params['gameName'] = $gameName;
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * This function provides a list of specified number of top reward earners
     * for a specific game.
     * 
     * @param gameName
     *            - Name of the game for which reward earners are to be fetched
     * @param rewardName
     *            - Name of the reward for which list of earners is to be
     *            fetched
     * @param max
     *            - Specifies the number of top earners to be fetched
     * @return List of Reward object
     */
    function getTopNRewardEarners($gameName, $rewardName, $max) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        Util::throwExceptionIfNullOrBlank($max, "Max");
        Util::validateMax($max);
        $encodedGameName = Util::encodeParams($gameName);
        $encodedRewardName = Util::encodeParams($rewardName);
        $encodedMax = Util::encodeParams($max);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['gameName'] = $gameName;
            $params['rewardName'] = $rewardName;
            $params['max'] = $max;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedRewardName . "/" . $encodedMax;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * This function returns you the details of all the specific rewards earned
     * by the specified user.
     * 
     * @param userName
     *            - Name of the user whose rewards are to be fetched
     * @param rewardName
     *            - Name of the reward for which details are to be fetched
     * @return List of Reward object

     */
    function getAllRewardsByUser($userName, $rewardName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        $encodedUserName = Util::encodeParams($userName);
        $encodedRewardName = Util::encodeParams($rewardName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['rewardName'] = $rewardName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/points/" . $encodedRewardName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

    /**
     * This function returns you a list of group wise users who earned the top
     * rewards in the specified game .
     * 
     * @param gameName
     *            - Name of the game for which top reward earners are to be
     *            fetched
     * @param rewardName
     *            - Name of the reward for which top earners are to be listed
     * @param userList
     *            - List of group wise users earning specified rewards
     * @return List of Reward object
     */
    function getTopNRewardEarnersByGroup($gameName, $rewardName, $userList) {

        Util::throwExceptionIfNullOrBlank($gameName, "Game Name");
        Util::throwExceptionIfNullOrBlank($rewardName, "Reward Name");
        Util::throwExceptionIfNullOrBlank($userList, "User List");
        $encodedGameName = Util::encodeParams($gameName);
        $encodedRewardName = Util::encodeParams($rewardName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['gameName'] = $gameName;
            $params['rewardName'] = $rewardName;
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
            $this->url = $this->url . "/" . $encodedGameName . "/" . $encodedRewardName . "/group/points";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $rewardResponseObj = new RewardResponseBuilder();
            $rewardObj = $rewardResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $rewardObj;
    }

}

?>