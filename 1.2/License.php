<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

/*  File Name : UploadFileType.php
 *  Author : Sushil Singh  04-04-2011
 */

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

/**
 *
 * This License object is the value object which contains the properties of
 * License along with the setter & getter for those properties.
 *
 */
class License extends App42Response {

    public $name;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;
    public $issueDate;
    public $key;
    public $valid;

    /**
     * Returns the name of the user who bought the license.
     *
     * @return the name of the user who bought the license.
     */
    public function getUser() {
        return $this->user;
    }
    /**
    * Sets the name of the user.
    *
    * @param user
    *            - name of the user
    *
    */
    public function setUser($user) {
        $this->user = $user;
    }
    /**
    * Returns the date the license was issued.
    *
    * @return the date the license was issued.
    */
    public function getIssueDate() {
        return $this->issueDate;
    }
    /**
    * Sets the date when the license has to be issued.
    *
    * @param issueDate
    *            - date the license was issued.
    *
    */
    public function setIssueDate($issueDate) {
        $this->issueDate = $issueDate;
    }
    /**
     * Returns the key
     *
     * @return the key
     */
    public function getKey() {
        return $this->key;
    }
    /**
     * Sets the key
     *
     * @param key
     *            - key
     *
     */
    public function setKey($key) {
        $this->key = $key;
    }
    /**
     * Returns the information about the license whether it's Valid or not.
     *
     * @return the information about the license whether it's Valid or not.
     */
    public function getValid() {
        return $this->valid;
    }
    /**
    * Sets the information about the license whether it's Valid or not.
    *
    * @param valid
    *            - information about the license whether it's Valid or not.
    *
    */
    public function setValid($valid) {
        $this->valid = $valid;
    }
    /**
    * Returns the name of the license.
    *
    * @return the name of the license.
    */
    public function getName() {
        return $this->name;
    }
    /**
     * Sets the name of the license.
     *
     * @param name
     *            - name of the license.
     *
     */
    public function setName($name) {
        $this->name = $name;
    }
    /**
    * Returns the price of the license.
    *
    * @return the price of the license.
    */
    public function getPrice() {
        return $this->price;
    }
    /**
     * Sets the price of the license.
     *
     * @param price
     *            - price of the license.
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }
    /**
    * Returns the type of the currency in which the bill is created.
    *
    * @return the type of the currency in which the bill is created.
    */
    public function getCurrency() {
        return $this->currency;
    }
    /**
    * Sets the type of the currency in which the bill has to be created.
    *
    * @param currency
    *            - type of the currency.
    *
    */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
    * Returns the state of the license.
    *
    * @return the state of the license.
    */
    public function getState() {
        return $this->state;
    }
    /**
     * Sets the state of the license.
     *
     * @param state
     *            - state of the license.
     *
     */
    public function setState($state) {
        $this->state = $state;
    }
    /**
     * Returns the description of the license.
     *
     * @return the description of the license.
     */
    public function getDescription() {
        return $this->description;
    }
    /**
     * Sets the description of the license.
     *
     * @param description
     *            - description of the license.
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description . " :issueDate : " . $this->issueDate . " : valid : " . $this->valid . " : key : " . $this->key;
    }

}
?>
