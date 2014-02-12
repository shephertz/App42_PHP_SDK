<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\appTab\Usage;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "session.php";
include_once "App42ResponseBuilder.php";

/**
 *
 * UsageResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e Usage
 *
 */
class UsageResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e Usage
     *
     * @param json
     *            - response in JSON format
     *
     * @return Usage object filled with json data
     */
    function buildResponse($json) {
        $appTabJSONObj = $this->getServiceJSONObject("appTab", $json);
        $usageJsonObj = $appTabJSONObj->__get("usages")->__get("usage");
        $usage = new Usage();
        $attributeList = array();

        //$usage->setAttributeList($attributeList);
        $usage->setStrResponse($json);
        $usage->setResponseSuccess($this->isRespponseSuccess($json));
        //print_r($usage);

        $this->buildObjectFromJSONTree($usage, $usageJsonObj);


        if ($usageJsonObj->has("level")) {
            if ($usageJsonObj->__get("level") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("level");
                $level = new Level($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("level");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new Level($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        } else if ($usageJsonObj->has("oneTime")) {

            if ($usageJsonObj->__get("oneTime") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("oneTime");
                $level = new OneTime($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("oneTime");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new OneTime($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        } else if ($usageJsonObj->has("feature")) {

            if ($usageJsonObj->__get("feature") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("feature");
                $level = new Feature($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("feature");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new Feature($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        } else if ($usageJsonObj->has("bandwidth")) {

            if ($usageJsonObj->__get("bandwidth") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("bandwidth");
                $level = new Bandwidth($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("bandwidth");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new Bandwidth($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        } else if ($usageJsonObj->has("storage")) {

            if ($usageJsonObj->__get("storage") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("storage");
                $level = new Storage($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("storage");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new Storage($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        } else if ($usageJsonObj->has("time")) {

            if ($usageJsonObj->__get("time") instanceof JSONObject) {
                $levelJSONObj = $usageJsonObj->__get("time");
                $level = new Time($usage);
                $this->buildObjectFromJSONTree($level, $levelJSONObj);
            } else {
                $levelJSONArray = $usageJsonObj->getJSONArray("time");
                for ($i = 0; $i < count($levelJSONArray); $i++) {
                    $levelJSONObj = $levelJSONArray[$i];
                    $level = new Time($usage);
                    $levelJSONObj = new JSONObject($levelJSONObj);
                    $this->buildObjectFromJSONTree($level, $levelJSONObj);
                }
            }
        }

        return $usage;
    }

}
?>