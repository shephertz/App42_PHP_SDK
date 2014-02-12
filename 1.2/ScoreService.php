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
 * Allows game scoring. It has to be used for scoring for a particular Game
 * Session. If scores have to be stored across Game sessions then the service
 * ScoreBoard has to be used. It is especially useful for Multiplayer online or
 * mobile games. The Game service allows Game, User, Score and ScoreBoard
 * Management on the Cloud. The service allows Game Developer to create a Game
 * and then do in Game Scoring using the Score service. It also allows to
 * maintain a Scoreboard across game sessions using the ScoreBoard service. One
 * can query for average or highest score for user for a Game and highest and
 * average score across users for a Game. It also gives ranking of the user
 * against other users for a particular game. The Reward and RewardPoints allows
 * the Game Developer to assign rewards to a user and redeem the rewards. E.g.
 * One can give Swords or Energy etc. The services Game, Score, ScoreBoard,
 * Reward, RewardPoints can be used in Conjunction for complete Game Scoring and
 * Reward Management.
 *
 * @see Game, RewardPoint, RewardPoint, ScoreBoard
 *
 */
class ScoreService {

    private $version = "1.0";
    private $resource = "game/score";
    private $apiKey;
    private $secretKey;
    protected $content_type = "application/json";
    protected $accept = "application/json";

    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Adds game score for the specified user.
     *
     * @param gameName
     *            - Name of the game for which scores have to be added
     * @param gameUserName
     *            - The user for whom scores have to be added
     * @param gameScore
     *            - The scores that have to be added
     *
     * @return Game object containing the scores that has been added
     */
    function addScore($gameName, $gameUserName, $gameScore) {

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

            $body = '{"app42":{"game":{"name":"' . $gameName . '","scores":{"score":{"userName":"' . $gameUserName . '","value":"' . $gameScore . '"}}}}}';


            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/add";
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
     * Deducts the score from users account for a particular Game
     *
     * @param gameName
     *            - Name of the game for which scores have to be deducted
     * @param gameUserName
     *            - The user for whom scores have to be deducted
     * @param gameScore
     *            - The scores that have to be deducted
     *
     * @return Game object containing the scores that has been deducted
     */
    function deductScore($gameName, $gameUserName, $gameScore) {

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
            $this->url = $this->url . "/deduct";
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

}
?>