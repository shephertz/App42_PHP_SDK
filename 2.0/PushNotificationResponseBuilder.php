<?php

include_once "JSONObject.php";
include_once "App42ResponseBuilder.php";
include_once "PushNotification.php";

class PushNotificationResponseBuilder extends App42ResponseBuilder {

    function buildResponse($json) {

        $pushObj = new PushNotification();
        $channelList = array();
        $pushObj->setChannelList($channelList);
        $pushObj->setStrResponse($json);
        $jsonObj = new JSONObject($json);
        $jsonObjApp42 = $jsonObj->__get("app42");
        $jsonObjResponse = $jsonObjApp42->__get("response");
        $pushObj->setResponseSuccess($jsonObjResponse->__get("success"));
        $jsonObjPush = $jsonObjResponse->__get("push");

        $this->buildObjectFromJSONTree($pushObj, $jsonObjPush);

        if (!$jsonObjPush->has("channels"))
            return $pushObj;

        $jsonPushChannels = $jsonObjPush->__get("channels");

        if (!$jsonPushChannels->has("channel"))
            return $pushObj;

        if ($jsonPushChannels->__get("channel") instanceof JSONObject) {
            // Only One attribute is there
            $jsonObjchannel = $jsonPushChannels->__get("channel");
            $channelList = new Channel($pushObj);
            $this->buildObjectFromJSONTree($channelList, $jsonObjchannel);
        } else {
            // There is an Array of attribute
            $jsonObjChanelArray = $jsonPushChannels->getJSONArray("channel");
            for ($i = 0; $i < count($jsonObjChanelArray); $i++) {
                // Get Individual Attribute Node and set it into Object
                $jsonObjChannelLi = $jsonObjChanelArray[$i];
                $channelList1 = new Configuration($pushObj);
                $jsonObjChann = new JSONObject($jsonObjChannelLi);
                $this->buildObjectFromJSONTree($channelList1, $jsonObjChann);
            }
        }

        return $pushObj;
    }

}

?>