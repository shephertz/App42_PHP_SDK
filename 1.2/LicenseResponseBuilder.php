<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\appTab\License;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "License.php";
include_once "App42ResponseBuilder.php";

/**
 *
 * LicenseResponseBuilder class converts the JSON response retrieved from the
 * server to the value object i.e License
 *
 */
class LicenseResponseBuilder extends App42ResponseBuilder {

    /**
     * Converts the response in JSON format to the value object i.e License
     *
     * @param json
     *            - response in JSON format
     *
     * @return License object filled with json data
     */
    function buildResponse($json) {
        //	$appTabJSONObj = new JSONObject();
        $appTabJSONObj = $this->getServiceJSONObject("appTab", $json);
        $licenceJSONObj = $appTabJSONObj->__get("licenses")->__get("license");
        $license = new License();
        $license = $this->buildLicenseObject($licenceJSONObj);
        $license->setStrResponse($json);
        $license->setResponseSuccess($this->isRespponseSuccess($json));
        return $license;
    }

    private function buildLicenseObject($licenceJSONObj) {
        $license = new License();
        $licenceJSONObj = new JSONObject($licenceJSONObj);
        $this->buildObjectFromJSONTree($license, $licenceJSONObj);
        return $license;
    }

    /**
     * Converts the response in JSON format to the list of value objects i.e
     * License
     *
     * @param json
     *            - response in JSON format
     *
     * @return List of License object filled with json data
     *
     */
    public function buildArrayResponse($json) {
        $appTabJSONObj = $this->getServiceJSONObject("appTab", $json);
        $licenseList = array();

        if ($appTabJSONObj->__get("licenses")->__get("license") instanceof JSONObject) {
            $licenseJSONObj = $appTabJSONObj->__get("licenses")->__get("license");
            $license = $this->buildLicenseObject($licenseJSONObj);
            array_push($licenseList, $license);
        } else {
            $licenceJSONArray = $appTabJSONObj->__get("licenses")->getJSONArray("license");
            for ($i = 0; $i < count($licenceJSONArray); $i++) {
                $licenseJSONObj = $licenceJSONArray[$i];
                $license = $this->buildLicenseObject($licenseJSONObj);
                array_push($licenseList, $license);
            }
        }
        return $licenseList;
    }

}
?>