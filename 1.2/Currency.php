<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

/**
 * An class that contains the currencies to be mentioned in the Usage.
 *
 */
class Currency {

    const USD = "USD";
    const EUR = "EUR";
    const INR = "INR";
    const YEN = "YEN";
    const GBP = "GBP";
    const ASD = "ASD";
    const CAD = "CAD";
    const NZD = "NZD";
    const CNY = "CNY";

    public function enum($string) {
        return constant('com\shephertz\app42\paas\sdk\php\appTab\Currency::' . $string);
    }

    public function isAvailable($string) {
        if ($string == "USD")
            return "USD";
        else if ($string == "EUR")
            return "EUR";
        else if ($string == "INR")
            return "INR";
        else if ($string == "YEN")
            return "YEN";
        else if ($string == "GBP")
            return "GBP";
        else if ($string == "ASD")
            return "ASD";
        else if ($string == "CAD")
            return "CAD";
        else if ($string == "NZD")
            return "NZD";
        else if ($string == "CNY")
            return "CNY";
        else
            return "null";
    }

}
?>

