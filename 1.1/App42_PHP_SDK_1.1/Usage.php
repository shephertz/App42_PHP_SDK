<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

/*  File Name : UploadFileType.php
 *  Author : Sushil Singh  04-04-2011
 */

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

/**
 *
 * This Usage object is the value object which contains the properties of Usage
 * along with the setter & getter for those properties.
 *
 */
class Usage extends App42Response {

    //public $user;
    //public $transactionId ;
    //public $serviceName ;
    public $levelList = array();
    public $oneTimeList = array();
    public $featureList = array();
    public $bandwidthList = array();
    public $storageList = array();
    public $timeList = array();

    /**
     * Returns the list of all levels in an app.
     *
     * @return the list of all levels in an app.
     */
    public function getLevelList() {
        return $this->levelList;
    }

    /**
     * Sets the list of all levels in an app.
     *
     * @param levelList
     *            - list of all levels in an app.
     *
     */
    public function setLevelList($levelList) {
        $this->levelList = $levelList;
    }

    /**
     * Returns the list of all the one time users.
     *
     * @return the list of all the one time users.
     */
    public function getOneTimeList() {
        return $this->oneTimeList;
    }

    /**
     * Sets the list of all the one time users.
     *
     * @param oneTimeList
     *            - list of all the one time users
     *
     */
    public function setOneTimeList($oneTimeList) {
        $this->oneTimeList = $oneTimeList;
    }

    /**
     * Returns the list of all the features in an app.
     *
     * @return the list of all the features in an app.
     */
    public function getFeatureList() {
        return $this->featureList;
    }

    /**
     * Sets the list of all the features in an app.
     *
     * @param featureList
     *            - list of all the features in an app.
     *
     */
    public function setFeatureList($featureList) {
        $this->featureList = $featureList;
    }

    /**
     * Returns the list of all the bandwidth values and its charges for an app.
     *
     * @return the list of all the bandwidth values and its charges for an app.
     */
    public function getBandwidthList() {
        return $this->bandwidthList;
    }

    /**
     * Sets the list of all the bandwidth values and its charges for an app.
     *
     * @param bandwidthList
     *            - list of all the bandwidth values and its charges
     *
     */
    public function setBandwidthList($bandwidthList) {
        $this->bandwidthList = $bandwidthList;
    }

    /**
     * Returns the list of all the storage values and its charges for an app.
     *
     * @return the list of all the storage values and its charges for an app.
     */
    public function getStorageList() {
        return $this->storageList;
    }

    /**
     * Sets the list of all the storage values and its charges for an app.
     *
     * @param storageList
     *            - list of all the storage values and its charges
     *
     */
    public function setStorageList($storageList) {
        $this->storageList = $storageList;
    }

    /**
     * Returns the list of all the time values and its charges for an app.
     *
     * @return the list of all the time values and its charges for an app.
     */
    public function getTimeList() {
        return $this->timeList;
    }

    /**
     * Sets the list of all the time values and its charges for an app.
     *
     * @param timeList
     *            - list of all the time values and its charges
     *
     */
    public function setTimeList($timeList) {
        $this->timeList = $timeList;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class Level {

    public function __construct(Usage $usage) {
        array_push($usage->levelList, $this);
    }

    public $name;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param user
     *            - name of the user
     *
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Returns the name of the level for the bill to be generated.
     *
     * @return the name of the level.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the level for the bill to be generated.
     *
     * @param name
     *            - name of the level
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the price computed as per the levels of an app.
     *
     * @return the price computed as per the levels
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the levels
     *
     * @param price
     *            - price as per the levels
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the levels.
     *
     * @return the state of the levels.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the levels.
     *
     * @param state
     *            - state of the levels
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the levels.
     *
     * @return the description of the levels.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the levels.
     *
     * @param description
     *            - description of the levels
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class OneTime {

    public $name;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    public function __construct(Usage $usage) {
        array_push($usage->oneTimeList, $this);
    }

    /**
     * Returns the name of the One Time users for whom the bill will be
     * generated.
     *
     * @return the name of the One Time users.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the One Time users for whom the bill will be
     * generated.
     *
     * @param user
     *            - name of the One Time users
     *
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Returns the name of the One Time users for whom the bill to be
     * generated.
     *
     * @return the name of the One Time users.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the One Time users for whom the bill to be
     * generated.
     *
     * @param name
     *            - name of the One Time users
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the price computed as per the One Time users of an app.
     *
     * @return the price computed as per the One Time users
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the One Time users
     *
     * @param price
     *            - price as per the One Time users
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the One Time users.
     *
     * @return the state of the One Time users.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the One Time users.
     *
     * @param state
     *            - state of the One Time users
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the One Time users.
     *
     * @return the description of the One Time users.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the One Time users.
     *
     * @param description
     *            - description of the One Time users
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " price : " . $this->price . " currency : " . $this->currency . " state : " . $this->state . " description  : " . $this->description;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class Feature {

    public function __construct(Usage $usage) {
        array_push($usage->featureList, $this);
    }

    public $name;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param user
     *            - name of the user
     *
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Returns the name of the feature.
     *
     * @return the name of the feature.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the feature for the bill to be generated.
     *
     * @param name
     *            - name of the feature
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the price computed as per the features of an app.
     *
     * @return the price computed as per the features
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the features
     *
     * @param price
     *            - price as per the features
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the features.
     *
     * @return the state of the features.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the features.
     *
     * @param state
     *            - state of the features
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the features.
     *
     * @return the description of the features.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the features.
     *
     * @param description
     *            - description of the features
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class Bandwidth {

    public function __construct(Usage $usage) {
        array_push($usage->bandwidthList, $this);
    }

    public $name;
    public $bandwidth;
    public $unit;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param user
     *            - name of the user
     *
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Returns the name of the bandwidth unit for the bill to be generated.
     *
     * @return the name of the bandwidth unit.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the bandwidth unit for the bill to be generated.
     *
     * @param name
     *            - name of the bandwidth unit
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the bandwidth values and its charges for an app.
     *
     * @return the bandwidth values and its charges for an app.
     */
    public function getBandwidth() {
        return $this->bandwidth;
    }

    /**
     * Sets the bandwidth values and its charges for an app.
     *
     * @param bandwidth
     *            - the bandwidth values and its charges
     *
     */
    public function setBandwidth($bandwidth) {
        $this->bandwidth = $bandwidth;
    }

    /**
     * Returns the bandwidth unit taken by an app whether its KB, MB, GB or
     * TB.
     *
     * @return the bandwidth unit taken by an app
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * Sets the bandwidth unit for an app
     *
     * @param unit
     *            - bandwidth unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /**
     * Returns the price computed as per the bandwidth unit of an app.
     *
     * @return the price computed as per the bandwidth unit
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the bandwidth unit
     *
     * @param price
     *            - price as per the bandwidth unit
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the bandwidth.
     *
     * @return the state of the bandwidth.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the bandwidth.
     *
     * @param state
     *            - state of the bandwidth
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the bandwidth.
     *
     * @return the description of the bandwidth.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the bandwidth.
     *
     * @param description
     *            - description of the bandwidth
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description . " : bandwidth : " . $this->bandwidth;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class Storage {

    public function __construct(Usage $usage) {
        array_push($usage->storageList, $this);
    }

    public $name;
    public $space;
    public $unit;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param user
     *            - name of the user
     *
     */
    public function setUser($name) {
        $this->user = $user;
    }

    /**
     * Returns the name of the storage unit for the bill to be generated.
     *
     * @return the name of the storage unit.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the storage unit for the bill to be generated.
     *
     * @param name
     *            - name of the storage unit
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the space taken for an app.
     *
     * @return the space taken for an app.
     */
    public function getSpace() {
        return $this->space;
    }

    /**
     * Sets the space for an app.
     *
     * @param space
     *            - space for an app.
     *
     */
    public function setSpace($space) {
        $this->space = $space;
    }

    /**
     * Returns the storage unit taken by an app whether its KB, MB, GB or
     * TB.
     *
     * @return the storage unit taken by an app
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * Sets the storage unit for an app
     *
     * @param unit
     *            - storage unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /**
     * Returns the price computed as per the storage of an app.
     * 
     * @return the price computed as per the storage
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the storage
     *
     * @param price
     *            - price as per the storage
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the storage.
     *
     * @return the state of the storage.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the storage.
     *
     * @param state
     *            - state of the storage
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the storage.
     *
     * @return the description of the storage.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the storage.
     *
     * @param description
     *            - description of the storage
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description . " : space : " . $this->space;
    }

}

/**
 * An inner class that contains the remaining properties of the Usage.
 *
 */
class Time {

    public function __construct(Usage $usage) {
        array_push($usage->timeList, $this);
    }

    public $name;
    public $time;
    public $unit;
    public $price;
    public $currency;
    public $state;
    public $description;
    public $user;

    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param user
     *            - name of the user
     *
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Returns the name of the time unit for the bill to be generated.
     *
     * @return the name of the time unit.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the time unit for the bill to be generated.
     *
     * @param name
     *            - name of the time unit
     *
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the time unit value recorded at the time of execution.
     *
     * @return the time unit value recorded at the time of execution.
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * Sets the time unit value for an app.
     *
     * @param time
     *            - action performed on the app.
     *
     */
    public function setTime($time) {
        $this->time = $time;
    }

    /**
     * Returns the time unit taken by an app whether its SECONDS, MINUTES or
     * HOURS.
     *
     * @return the time unit taken by an app
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * Sets the time unit for an app
     *
     * @param unit
     *            - time unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /**
     * Returns the price computed as per the time of an app.
     *
     * @return the price computed as per the time
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets the price as per the time
     *
     * @param price
     *            - price as per the time
     *
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Returns the state of the time.
     *
     * @return the state of the time.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of the time.
     *
     * @param state
     *            - state of the time
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the description of the time.
     *
     * @return the description of the time.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the time.
     *
     * @param description
     *            - description of the time
     *
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Returns the Usage Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " name : " . $this->name . " : price : " . $this->price . " : currency : " . $this->currency . " : state : " . $this->state . " : description  : " . $this->description . " : time : " . $this->time;
    }

}
?>
