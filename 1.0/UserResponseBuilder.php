<?php

namespace com\shephertz\app42\paas\sdk\php\user;

use com\shephertz\app42\paas\sdk\php\user\User;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "User.php";
include_once "App42ResponseBuilder.php";

/**
 *
 * UserResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e User
 *
 */
class UserResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e User
     *
     * @params json
     *            - response in JSON format
     *
     * @return User object filled with json data
     *
     */
    public function buildResponse($json) {
        $usersJSONObj = $this->getServiceJSONObject("users", $json);
        $userJSOnObj = $usersJSONObj->__get("user");
        $user = $this->buildUserObject($userJSOnObj);
        $user->setStrResponse($json);
        $user->setResponseSuccess($this->isRespponseSuccess($json));
        return $user;
    }

    /**
     * Converts the User JSON object to the value object i.e User
     *
     * @param userJSONObj
     *            - user data as JSONObject
     *
     * @return User object filled with json data
     *
     */
    private function buildUserObject($userJSONObj) {
        $user = new User();
        $this->buildObjectFromJSONTree($user, $userJSONObj);
        if ($userJSONObj->has("profile")) {
            $profileJSONObj = $userJSONObj->__get("profile");
            $profile = new Profile($user);
            $this->buildObjectFromJSONTree($profile, $profileJSONObj);
        }

        if ($userJSONObj->has("role")) {
            $roleList = array();
            if ($userJSONObj->__get("role") instanceof JSONObject) {
                $roleArr = $userJSONObj->getJSONArray("role");
                for ($i = 0; $i < count($roleArr); $i++) {
                    array_push($roleList, $roleArr->__get[$i]);
                }
            } else {
                array_push($roleList, $userJSONObj->__get("role"));
            }
            $user->setRoleList($roleList);
        }
        return $user;
    }

    /**
     * Converts the response in JSON format to the list of value objects i.e User
     *
     * @params json
     *            - response in JSON format
     *
     * @return List of User object filled with json data
     *
     */
    public function buildArrayResponse($json) {
        $usersJSONObj = $this->getServiceJSONObject("users", $json);
        $userJSONArray = $usersJSONObj->getJSONArray("user");
        $userList = array();

        if ($userJSONArray instanceof JSONObject) {
            $userJSONObject = new JSONObject($userJSONArray);
            $user = $this->buildUserObject($userJSONObject);
            $user->setStrResponse($json);
            $user->setResponseSuccess($this->isRespponseSuccess($json));
            array_push($userList, $user);
        } else {
            for ($i = 0; $i < count($userJSONArray); $i++) {
                $userJSONObject = $userJSONArray[$i];
                $userJSONObject = new JSONObject($userJSONObject);
                $user = $this->buildUserObject($userJSONObject);
                $this->buildObjectFromJSONTree($user, $userJSONObject);
                $user->setStrResponse($json);
                $user->setResponseSuccess($this->isRespponseSuccess($json));
                array_push($userList, $user);
            }
        }
        return $userList;
    }

}
?>