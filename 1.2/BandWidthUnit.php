<?php namespace com\shephertz\app42\paas\sdk\php\appTab;

/**
 * Class that contains 4 types of the BandwidthUnit either KB or MB or GB or
 * TB.
 *
 */
	
class BandwidthUnit {

	const KB  = "KB";
	const MB = "MB";
	const GB = "GB";
	const TB = "TB";
	
	public function enum($string){
        return constant('com\shephertz\app42\paas\sdk\php\appTab\UsageBandWidth::'.$string);
    }
    
	public function isAvailable($string){
        if($string == "KB")
		return "KB";
		else if($string == "MB")
		return "MB";
		else if($string == "GB")
		return "GB";
		else if($string == "TB")
		return "TB";
		else
		return "null";
    }
}

?>
