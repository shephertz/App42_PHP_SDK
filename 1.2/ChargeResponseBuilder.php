<?php

namespace com\shephertz\app42\paas\sdk\php\charge;

use com\shephertz\app42\paas\sdk\php\charge\Charge;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "session.php";
include_once "App42ResponseBuilder.php";
include_once "Charge.php";

/**
 *
 * EmailResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e Email
 *
 */
class ChargeResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e Email
     *
     * @params json
     *            - response in JSON format
     *
     * @return Email object filled with json data
     *
     */
    function buildResponse($json) {

        $chargeObj = new Charge();
        //$transactionList = array();
       // $chargeObj->setTransactionList($transactionList);

        $chargeObj->setStrResponse($json);
        $jsonObj = new JSONObject($json);
        $jsonObjApp42 = $jsonObj->__get("app42");
        $jsonObjResponse = $jsonObjApp42->__get("response");
        $chargeObj->setResponseSuccess($jsonObjResponse->__get("success"));
        $jsonObjCharge = $jsonObjResponse->__get("transactions");
        if (!$jsonObjCharge->has("transaction"))
            return $chargeObj;

        if ($jsonObjCharge->__get("transaction") instanceof JSONObject) {
            // Only One attribute is there
            $jsonObjConfig = $jsonObjCharge->__get("transaction");
            $configItem = new Configuration($chargeObj);
            $this->buildObjectFromJSONTree($configItem, $jsonObjConfig);
        } else {
            // There is an Array of attribute
            $jsonObjConfigArray = $jsonObjCharge->getJSONArray("transaction");
            for ($i = 0; $i < count($jsonObjConfigArray); $i++) {
                // Get Individual Attribute Node and set it into Object
                $jsonObjConfig = $jsonObjConfigArray[$i];
                $configItem = new Configuration($chargeObj);
                $jsonObjConfig = new JSONObject($jsonObjConfig);
                $this->buildObjectFromJSONTree($configItem, $jsonObjConfig);
            }
        }

        return $chargeObj;
    }

}
?>