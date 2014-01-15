<?php namespace com\shephertz\app42\paas\sdk\php\appTab;
/**
 * A class that contains the Time Unit to be mentioned in the Bill.
 *
 */
	
class TimeUnit {

	const SECONDS  = "SECONDS";
	const MINUTES = "MINUTES";
	const HOURS = "HOURS";
	
	
	public function enum($string){
        return constant('com\shephertz\app42\paas\sdk\php\appTab\TimeUnit::'.$string);
    }
    
	public function isAvailable($string){
        if($string == "SECONDS")
		return "SECONDS";
		else if($string == "MINUTES")
		return "MINUTES";
		else if($string == "HOURS")
		return "HOURS";
		else
		return "null";
    }
	
}

?>
