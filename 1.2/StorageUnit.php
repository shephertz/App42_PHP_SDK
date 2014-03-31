<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

/**
 * A class that contains the Storage Unit to be mentioned in the Bill.
 *
 */
class StorageUnit {
    const KB = "KB";
    const MB = "MB";
    const GB = "GB";
    const TB = "TB";

    public function enum($string) {
        return constant('com\shephertz\app42\paas\sdk\php\appTab\StorageUnit::' . $string);
    }

    public function isAvailable($string) {
        if ($string == "KB")
            return "KB";
        else if ($string == "MB")
            return "MB";
        else if ($string == "GB")
            return "GB";
        else if ($string == "TB")
            return "TB";
        else
            return "null";
    }

}
?>
