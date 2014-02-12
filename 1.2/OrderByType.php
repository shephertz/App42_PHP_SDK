<?php

namespace com\shephertz\app42\paas\sdk\php\storage;

class OrderByType {

    const ASCENDING = "ASCENDING";
    const DESCENDING = "DESCENDING";

    public function enum($string) {
        return constant('com\shephertz\app42\paas\sdk\php\storage\OrderByType::' . $string);
    }

    public function isAvailable($string) {
        if ($string == "ASCENDING")
            return "ASCENDING";
        else if ($string == "DESCENDING")
            return "DESCENDING";
        else
            return "null";
    }

}

?>
