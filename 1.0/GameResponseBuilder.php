<?php

namespace com\shephertz\app42\paas\sdk\php\game;

use com\shephertz\app42\paas\sdk\php\game\Game;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "session.php";
include_once "App42ResponseBuilder.php";
include_once "Game.php";

/**
 *
 * GameResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e Game
 *
 */
class GameResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e Game
     *
     * @params json
     *            - response in JSON format
     *
     * @return Game object filled with json data
     *
     */
    function buildResponse($json) {

        $gamesJSONObj = $this->getServiceJSONObject("games", $json);
        $gameJSONObj = $gamesJSONObj->__get("game");
        $game = new Game();
        $game = $this->buildGameObject($gameJSONObj);
        $game->setResponseSuccess($this->isRespponseSuccess($json));
        $game->setStrResponse($json);
        return $game;
    }

    /**
     * Converts the response in JSON format to the list of value objects i.e
     * Game
     *
     * @params json
     *            - response in JSON format
     *
     * @return List of Game object filled with json data
     *
     */
    public function buildArrayResponse($json) {
        $gamesJSONObj = $this->getServiceJSONObject("games", $json);
        $gameList = array();

        if ($gamesJSONObj->__get("game") instanceof JSONObject) {

            $gameJSONObj = $gamesJSONObj->__get("game");
            $game = new Game();
            $game = $this->buildGameObject($gameJSONObj);
            $game->setResponseSuccess($this->isRespponseSuccess($json));
            $game->setStrResponse($json);
            array_push($gameList, $game);
        } else {
            $gameJSONArray = $gamesJSONObj->getJSONArray("game");
            for ($i = 0; $i < count($gameJSONArray); $i++) {
                $gameJSONObj = $gameJSONArray[$i];
                $game = new Game();
                $game = $this->buildGameObject($gameJSONObj);
                $game->setResponseSuccess($this->isRespponseSuccess($json));
                $game->setStrResponse($json);
                array_push($gameList, $game);
            }
        }
        return $gameList;
    }

    /**
     * Converts the Game JSON object to the value object i.e Game
     * 
     * @params gameJSONObject
     *            - Game data as JSONObject
     *
     * @return Game object filled with json data
     *
     */
    public function buildGameObject($gameJSONObject) {
        $game = new Game();
        $gameJSONObject = new JSONObject($gameJSONObject);
        $this->buildObjectFromJSONTree($game, $gameJSONObject);
        if ($gameJSONObject->has("scores") && $gameJSONObject->__get("scores")->has("score")) {
            if ($gameJSONObject->__get("scores")->__get("score") instanceof JSONObject) {
                $scoreJSONObj = $gameJSONObject->__get("scores")->__get("score");
                $score = new Score($game);
                $this->buildObjectFromJSONTree($score, $scoreJSONObj);
            } else {
                //Fetch Array of Game
                $scoreJSONArray = $gameJSONObject->__get("scores")->getJSONArray("score");
                for ($i = 0; $i < count($scoreJSONArray); $i++) {
                    $scoreJSONObj = $scoreJSONArray[$i];
                    $score = new Score($game);
                    $scoreJSONObj = new JSONObject($scoreJSONObj);
                    $this->buildObjectFromJSONTree($score, $scoreJSONObj);
                }
            }
        }
        return $game;
    }

}
?>