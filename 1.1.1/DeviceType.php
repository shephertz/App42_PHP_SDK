<?php
namespace com\shephertz\app42\paas\sdk\php\push;

/*  File Name : UploadFileType.php
 *  Author : Shashank Shukla  05-10-2012
 * This define the type of the device that can be stored.
 */
class DeviceType {
    
    
	const ANDROID = "ANDROID";
	const IOS = "iOS";
	const WP7 = "WP7";
	
        public function enum($string){
        return constant('com\shephertz\app42\paas\sdk\php\push\DeviceType::'.$string);
    }
    
	public function isAvailable($string){
        if($string == "ANDROID")
		return "ANDROID";
		else if($string == "IOS")
		return "IOS";
                else if($string == "WP7")
		return "WP7";
		else
		return "null";
    }
}
?>
