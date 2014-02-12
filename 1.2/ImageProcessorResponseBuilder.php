<?php

namespace com\shephertz\app42\paas\sdk\php\imageProcessor;

use com\shephertz\app42\paas\sdk\php\imageProcessor\Image;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "Image.php";
include_once "App42ResponseBuilder.php";

/**
 *
 * ImageProcessResponseBuilder class converts the JSON response retrieved from
 * the server to the value object i.e Image
 *
 */
class ImageProcessorResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e Image
     *
     * @param json
     *            - response in JSON format
     *
     * @return Image object filled with json data
     *
     */
    public function buildResponse($json) {
        $imageJSONObj = $this->getServiceJSONObject("image", $json);
        $imageObj = new Image();
        $imageObj->setStrResponse($json);
        $imageObj->setResponseSuccess($this->isRespponseSuccess($json));
        $this->buildObjectFromJSONTree($imageObj, $imageJSONObj);
        return $imageObj;
    }

}
?>