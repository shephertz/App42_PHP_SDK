<?php

use com\shephertz\app42\paas\sdk\php\ServiceAPI;
use com\shephertz\app42\paas\sdk\php\App42BadParameterException;
use com\shephertz\app42\paas\sdk\php\App42NotFoundException;
use com\shephertz\app42\paas\sdk\php\App42SecurityException;
use com\shephertz\app42\paas\sdk\php\App42Exception;


include_once '../1.1.1/ServiceAPI.php';
include_once '../1.1.1/App42BadParameterException.php';
include_once '../1.1.1/App42NotFoundException.php';
include_once '../1.1.1/App42SecurityException.php';
include_once '../1.1.1/App42Exception.php';


/**
 * This class basically is a factory class which builds the service for use.
 * All services can be instantiated using this class
 * 
 */

class SampleApp{ 
	
    /**
     * Test Method for creating the User in App42 Cloud. 
     */
	
    public function createUser()
    {
        $api = new ServiceAPI("API KEY", "SECRET KEY");
		$response = null;

        // FOR  Test Create USER
        $objUser = $api->buildUserService();

        try {
            print(" Starting User Creation test");
            $response = $objUser->createUser("admin", "test", "nick@shephertz.co.in");
        } catch (App42BadParameterException $ex) {
            // Exception Caught
			// Check if User already Exist by checking app error code
            if ($ex->getAppErrorCode() == 2001) {
                // Do exception Handling for Already created User.
				
            }
        } catch (App42SecurityException $ex) {
            // Exception Caught
            // Check for authorization Error due to invalid Public/Private Key
            if ($ex->getAppErrorCode() == 1401) {
                // Do exception Handling here
            }
        } catch (App42Exception $ex) {
            // Exception Caught due to other Validation
        }
        // Render the JSON response. This will return the Successful created
        // User response
       
    }
	
}
$SampleAppObj = new SampleApp();
// Call to create User
$SampleAppObj->createUser();
	
?>