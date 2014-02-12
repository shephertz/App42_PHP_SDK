<?php
namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\SchemeData;

include_once "JSONObject.php";
include_once "App42ResponseBuilder.php";
include_once "SchemeData.php";

class SchemeResponseBuilder extends App42ResponseBuilder {

    public function buildResponse($json) {
        $schemeJSONObj = $this->getServiceJSONObject("schemes", $json);
        $discountData = $schemeJSONObj->__get("scheme");
        $scheme = $this->buildSchemeObject($schemeJSONObj);
        $scheme->setStrResponse($json);
        $scheme->setResponseSuccess($this->isRespponseSuccess($json));
        return $scheme;
    }
     private function buildSchemeObject($schemeJSONObj) {
        $scheme = new SchemeData();
        if ($schemeJSONObj->__get("scheme") instanceof JSONArray) {
            $schemeJSONArray = $schemeJSONObj->getJSONArray("scheme");
            for ($i = 0; $i < count($schemeJSONArray); $i++) {

                $jsonObjConfig = $schemeJSONArray->getJSONObject(i);
                $this->buildObjectFromJSONTree($scheme, $jsonObjConfig);
            }
        } else {
            $jsonObjConfig = $schemeJSONObj->__get("scheme");
            $this->buildObjectFromJSONTree($scheme, $jsonObjConfig);
        }
        return $scheme;
    }
}
?>
