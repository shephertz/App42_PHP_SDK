<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

class PackageData extends App42Response {

    public $name;
    public $description;
    public $duration;
    public $price;
    public $currency;
    
 public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function setDuration($duration) {
        $this->duration = $duration;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function getStorage() {
        return $this->storage;
    }

    public function setStorage($storage) {
        $this->storage = $storage;
    }

    public function getBandwidth() {
        return $this->bandwidth;
    }

    public function setBandwidth($bandwidth) {
        $this->bandwidth = $bandwidth;
    }

    public function getFeature() {
        return $this->feature;
    }

    public function setFeature($feature) {
        $this->feature = $feature;
    }

}

class FeatureData {

    public $description;
    public $price;

    public function __construct(PackageData $packageData) {

        array_push($packageData->Feature, $this);
    }

    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
}
class BandwidthData {

    public $usageBandwidth;
    public $unit;
    public $price;
    public $description;

public function __construct(PackageData $packageData) {

        array_push($packageData->Bandwidth, $this);
    }

    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function getUsageBandwidth() {
        return $this->usageBandwidth;
    }
     public function setUsageBandwidth($usageBandwidth) {
        $this->usageBandwidth = $usageBandwidth;
    }
 public function getUnit() {
       return $this->unit;
    }
 public function setUnit($unit) {
        $this->unit = $unit;
    }

}
class StorageData {

    public $space;
    public $unit;
    public $price;
    public $description;
   

    public function __construct(PackageData $packageData) {

        array_push($packageData->Storage, $this);
    }

    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
    }
    public function getSpace() {
        return $this->space;
    }
    public function setSpace($space) {
        $this->space = $space;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
     public function getUnit() {
        return $this->unit;
    }
    public function setUnit($unit) {
        $this->unit = $unit;
    }
}
?>
