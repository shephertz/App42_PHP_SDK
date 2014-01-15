<?php namespace com\shephertz\app42\paas\sdk\php\appTab;
/**
 * Class that contains the months to be mentioned in the Bill.
 *
 */
class BillMonth {
    
    
	const JANUARY   = "JANUARY";
	const FEBRUARY  = "FEBRUARY";
	const MARCH     = "MARCH";
	const APRIL     = "APRIL";
	const MAY       = "MAY";
	const JUNE      = "JUNE";
	const JULY      = "JULY";
	const AUGUST    = "AUGUST";
	const SEPTEMBER = "SEPTEMBER";
	const OCTOBER   = "OCTOBER";
	const NOVEMBER  = "NOVEMBER";
	const DECEMBER  = "DECEMBER";
	
	
	
  	public function enum($string){
        return constant('com\shephertz\app42\paas\sdk\php\appTab\BillMonth::'.$string);
    }
    
	public function isAvailable($string){
        if($string == "JANUARY")
		return "JANUARY";
		else if($string == "FEBRUARY")
		return "FEBRUARY";
		else if($string == "MARCH")
		return "MARCH";
		else if($string == "APRIL")
		return "APRIL";
		else if($string == "MAY")
		return "MAY";
		else if($string == "JUNE")
		return "JUNE";
		else if($string == "JULY")
		return "JULY";
		else if($string == "AUGUST")
		return "AUGUST";
		else if($string == "SEPTEMBER")
		return "SEPTEMBER";
		else if($string == "OCTOBER")
		return "OCTOBER";
		else if($string == "NOVEMBER")
		return "NOVEMBER";
		else if($string == "DECEMBER")
		return "DECEMBER";
		else
		return "null";
    }
	
}

?>
