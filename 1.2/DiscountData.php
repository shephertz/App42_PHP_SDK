<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

class DiscountData extends App42Response {

    public $name;
    public $description;
//    public $discountList = array();

    public function getDiscountName() {
        return $this->name;
    }

    public function setDiscountName($discountName) {
        $this->name = $name;
    }

    public function getDiscountDescription() {
        return $this->description;
    }

    public function setDiscountDescription($discountDescription) {
        $this->description = $description;
    }

//    public function getDiscountList() {
//        return $this->discountList;
//    }
//
//    public function setDiscountList($discountList) {
//        $this->discountList = $discountList;
//    }

}

class Discount {

    public $discountPercent;
    public $usage;
    public $startDate;
    public $endDate;
    
 //    public function __construct(DiscountData $discountData) {
//
//        array_push($DiscountData->discountList, $this);
//    }
    public function getDiscountPercent() {
        return $this->discountPercent;
    }

    public function setDiscountPercent($discountPercent) {
        $this->discountPercent = $discountPercent;
    }
    public function getUsage() {
        return $this->usage;
    }

    public function setUsage($usage) {
        $this->usage = $usage;
    }
     public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }
    public function getEndDate() {
        return $this->endDate;
    }

    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

}
?>
