<?php

use com\shephertz\app42\paas\sdk\php\ServiceAPI;
use com\shephertz\app42\paas\sdk\php\App42Log;
include_once 'ServiceAPI.php'; 
include_once 'App42Log.php';


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
        $api = new ServiceAPI("7de296485ea5387959661c093e5c12123722c0d0e158b5abc6a524d0bf97aab5", "ff2eb9f669e97b1b5b4d788a87f9460700b243763b19258482292bb83d2e5c28");
		$response = null;

        // FOR  Test Create USER
        $objUser = $api->buildUser();

        try {
            print(" Starting User Creation test");
            $response = $objUser->createUser("admin", "test", "sushil.bhadouria@shephertz.co.in");
        } catch (App42BadParameterException $ex) {
            // Exception Caught
			// Check if User already Exist by checking app error code
            if ($ex->getAppErrorCode() == 2001) {
                // Do exception Handling for Already created User.
				
            }
        } catch (App42SecurityException $ex) {
            // Exception Caught
            // Check for authorization Error due to invalid Public/Private Key
            if ($ex.getAppErrorCode() == 1401) {
                // Do exception Handling here
            }
        } catch (App42Exception $ex) {
            // Exception Caught due to other Validation
        }
        // Render the JSON response. This will return the Successful created
        // User response
        App42Log::debug($response);
    }
	
}
$SampleAppObj = new SampleApp();
// Call to create User
$SampleAppObj->createUser();
	
?>