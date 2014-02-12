<?php

namespace com\shephertz\app42\paas\sdk\php\storage;

use com\shephertz\app42\paas\sdk\php\JSONObject;

include_once "JSONObject.php";

class Query {

    private $jsonObject;
    private $jsonArray;

    public function Query($jsonQuery) {
        if ($jsonQuery instanceof JSONObject) {
            $objectArray = array();
            array_push($objectArray, $jsonQuery);
            return $this->jsonObject = $objectArray;
        } else {
            return $this->jsonArray = $jsonQuery;
        }
    }

}

?>
