<?php namespace com\shephertz\app42\paas\sdk\php;
/*  File Name : ConfigurationException.php
 *  Description : To calculate charges
 *  Author : Sushil Singh  04-02-2011
 */
 use com\shephertz\app42\paas\sdk\php\App42Exception;
include_once 'App42Exception.php';

class ConfigurationException extends App42Exception{
	
	
	/**
     * Constructor which takes message
	 * @param Message
	 */
	public function ConfigurationException($message){
        parent::__construct($message);
    }

    
}

?>
