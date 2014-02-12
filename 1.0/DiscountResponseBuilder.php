<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\DiscountData;

//use com\shephertz\app42\paas\sdk\php\appTab\DiscountData\Discount;
include_once "JSONObject.php";
include_once "App42ResponseBuilder.php";
include_once "DiscountData.php";

//include_once "DiscountData.php";

class DiscountResponseBuilder extends App42ResponseBuilder {

    public function buildResponse($json) {
        $discountJSONObj = $this->getServiceJSONObject("discounts", $json);
        $discountData = $discountJSONObj->__get("discount");
        $discount = $this->buildDiscountObject($discountJSONObj);
        $discount->setStrResponse($json);
        $discount->setResponseSuccess($this->isRespponseSuccess($json));
        print_r($discount);
        
        return $discount;
    }

    private function buildDiscountObject($discountJSONObj) {
        $discount = new DiscountData();
        if ($discountJSONObj->__get("discount") instanceof JSONArray) {
            $discountsJSONArray = $discountJSONObj->getJSONArray("discount");
            for ($i = 0; $i < count($discountsJSONArray); $i++) {

                $jsonObjConfig = $discountsJSONArray->getJSONObject(i);
                $this->buildObjectFromJSONTree($discount, $jsonObjConfig);
            }
        } else {
            $jsonObjConfig = $discountJSONObj->__get("discount");
            $this->buildObjectFromJSONTree($discount, $jsonObjConfig);
        }
        return $discount;
    }

}
?>
