<?php

namespace com\shephertz\app42\paas\sdk\php\social;

use com\shephertz\app42\paas\sdk\php\social\Social;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "Social.php";
include_once "App42ResponseBuilder.php";

/**
 *
 * SocialResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e Social
 *
 */
class SocialResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e Social
     *
     * @param json
     *            - response in JSON format
     *
     * @return Social object filled with json data
     *
     */
    public function buildResponse($json) {
        $slJSONObject = $this->getServiceJSONObject("social", $json);
        $sl = new Social();
        $sl->setStrResponse($json);
        $sl->setResponseSuccess($this->isRespponseSuccess($json));
        $this->buildObjectFromJSONTree($sl, $slJSONObject);
        if ($slJSONObject->has("friends")) {
            if ($slJSONObject->__get("friends") instanceof JSONObject) {
                $friendJSONObj = $slJSONObject->__get("friends");
                $friends = new Friends($sl);
                $this->buildJsonFriends($friends, $friendJSONObj);
            } else {
                // There is an Array of attribute
                $friendsJSONArray = $slJSONObject->getJSONArray("friends");
                for ($i = 0; $i < count($friendsJSONArray); $i++) {
                    $friendJSONObj = $friendsJSONArray[$i];
                    $friends = new Friends($sl);
                    $this->buildJsonFriends($friends, $friendJSONObj);
                }
            }
        }
        return $sl;
    }

    function buildJsonFriends($friends, $friendJSONObj) {
        $jsonObjFriends = new JSONObject($friendJSONObj);
        if ($jsonObjFriends->has("id") && $jsonObjFriends->__get("id") != null) {

            $friends->setId($jsonObjFriends->__get("id"));
            $friends->setName($jsonObjFriends->__get("name"));
            $friends->setPicture($jsonObjFriends->__get("picture"));
            $friends->setInstalled($jsonObjFriends->__get("installed"));
        }
    }

}

?>