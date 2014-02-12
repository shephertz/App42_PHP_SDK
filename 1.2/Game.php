<?php

namespace com\shephertz\app42\paas\sdk\php\game;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

/**
 *
 * This Game object is the value object which contains the properties of Game
 * along with the setter & getter for those properties.
 * 
 */
class Game extends App42Response {

    public $name;
    public $description;
    public $scoreList = array();

    /**
     * Returns the name of the game.
     *
     * @return the name of the game.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the Game which has to be created.
     *
     * @params name
     *            - Name of the Game that has to be created
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the description of the game.
     *
     * @return the description of the game.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the Game.
     *
     * @param description
     *            - Description of the game to be created
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the score list for the Game.
     *
     * @return the score list for the Game.
     */
    public function getScoreList() {
        return $this->scoreList;
    }

    /**
     * Sets the Score list for the Game.
     *
     * @params scoreList
     *            - List of score for the game
     *
     */
    public function setScoreList($scoreList) {
        $this->scoreList = $scoreList;
    }

}

/**
 * An inner class that contains the remaining properties of the Game.
 *
 */
class Score {

    public function __construct(Game $game) {
        array_push($game->scoreList, $this);
    }

    public $userName;
    public $rank;
    public $value;
    public $createdOn;
    public $scoreId;

    /**
     * Returns the game user name.
     *
     * @return the game user name.
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Sets the game user name for which scores have to be added
     *
     * @params scoreList
     *            - List of score for the game
     *
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    /**
     * Returns the game rank.
     *
     * @return the game rank.
     */
    public function getRank() {
        return $this->rank;
    }

    /**
     * Sets the rank for the game.
     *
     * @params rank
     *            - rank for the game.
     *
     */
    public function setRank($rank) {
        $this->rank = $rank;
    }

    /**
     * Returns the game value.
     *
     * @return the game value.
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the value for the game.
     *
     * @params value
     *            - value for the game.
     *
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Returns the scoreId.
     *
     * @return the scoreId.
     */
    public function getScoreId() {
        return $this->scoreId;
    }

    /**
     * Sets the value for the scoreId.
     *
     * @params scoreId
     *            - value for the scoreId.
     *
     */
    public function setScoreId($scoreId) {
        $this->scoreId = $scoreId;
    }

    /**
     * Returns the time when the game was created.
     *
     * @return the time when the game was created.
     */
    public function getCreatedOn() {
        return $this->createdOn;
    }

    /**
     * Sets the time when the game was created..
     *
     * @params createdOn
     *            - time when the game was created.
     *
     */
    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
    }

}

?>