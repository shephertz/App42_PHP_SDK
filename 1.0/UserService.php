<?php

namespace com\shephertz\app42\paas\sdk\php\user;

use stdClass;
/**
 * Creates User for the App. App42 Cloud API's provides a complete User Management for any
 * Mobile or Web App. It supports User registration, retrieval, state management e.g. lock, delete
 * and Authentication.
 * Along with User management the platform provides API's for persistent SessionManagement
 * @see SessionManager
 */
use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\connection\RestClient;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\App42Response;
use com\shephertz\app42\paas\sdk\php\user\UserResponseBuilder;

include_once 'RestClient.class.php';
include_once 'Util.php';
include_once 'Config.php';
include_once 'UserResponseBuilder.php';
include_once 'App42Exception.php';
include_once 'App42Response.php';

class UserService {

    protected $resource = "user";
    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $version = "1.0";
    protected $content_type = "application/json";
    protected $accept = "application/json";
    public static $MALE = "Male";
    public static $FEMALE = "Female";

    /**
     * This is a constructor that takes
     *
     * @param apiKey
     * @param secretKey
     * @param baseURL
     *
     */
    public function __construct($apiKey, $secretKey, $baseURL) {
        //$this->resource = "charge";
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = $baseURL . $this->version . "/" . $this->resource;
    }

    /**
     * Create a User with userName, password & emailAddress
     *
     * @param uName
     *            - Name of the User
     * @param pwd
     *            - Password for the User
     * @param emailAddress
     *            - Email address of the user
     *
     * @return The created User object.
     *
     */
    function createUser($uName, $pwd, $emailAddress, $roleList = null) {
        $argv = func_get_args();

        if (count($argv) == 3) {
            Util::throwExceptionIfNullOrBlank($uName, "User Name");
            Util::throwExceptionIfNullOrBlank($pwd, "Password");
            Util::throwExceptionIfNullOrBlank($emailAddress, "Email Address");
            Util::throwExceptionIfEmailNotValid($emailAddress, "Email Address");
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                $body = '{"app42":{"user":{"userName":"' . $uName . '","password":"' . $pwd . '","email":"' . $emailAddress . '"}}}';

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params));
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;

                $this->url = $this->url;

                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }

            return $userObj;
        } else {
            /**
             * Create a User with userName, password & emailAddress and assigns the
             * roles to the created User
             *
             * @param uName
             *            - Name of the User
             * @param pwd
             *            - Password for the User
             * @param emailAddress
             *            - Email address of the user
             * @param roleList
             *            - List of roles to be assigned to User
             *
             * @return The created User object with role list.
             */
            Util::throwExceptionIfNullOrBlank($uName, "User Name");
            Util::throwExceptionIfNullOrBlank($pwd, "Password");
            Util::throwExceptionIfNullOrBlank($emailAddress, "Email Address");
            Util::throwExceptionIfNullOrBlank($roleList, "RoleList");
            Util::throwExceptionIfEmailNotValid($emailAddress, "Email Address");
            $objUtil = new Util($this->apiKey, $this->secretKey);

            try {

                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $body = null;
                if (is_array($roleList)) {
                    $body = '{"app42" : {"user" : {"userName":"' . $uName . '","password":"' . $pwd . '","email":"' . $emailAddress . '","roles": { "role": ' . json_encode($roleList) . '}}}}';
                } else {
                    $body = '{"app42" : {"user" : {"userName":"' . $uName . '","password":"' . $pwd . '","email":"' . $emailAddress . '","roles": { "role": "' . $roleList . '"}}}}';
                }

                $params['body'] = $body;
                $signature = urlencode($objUtil->sign($params));
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/role";
                $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }

            return $userObj;
        }
    }

    /**
     * Assign Roles to the existing User
     *
     * @param uName
     *            - Name of the User to whom the roles have to be assigned
     * @param roleList
     *            - List of roles to be added to User
     *
     * @return The created User object with assigned roles.
     */
    function assignRoles($uName, $roleList) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($roleList, "RoleList");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            if (is_array($roleList)) {
                $body = '{"app42":{"user":{"userName":"' . $uName . '","roles": { "role": ' . json_encode($roleList) . '}}}}';
            } else {
                $body = '{"app42":{"user":{"userName":"' . $uName . '","roles": { "role": "' . $roleList . '"}}}}';
            }
            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params));
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/assignrole";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }

        return $userObj;
    }

    /**
     * Get the assigned roles to the specified User
     *
     * @param userName
     *            - Name of the User for whom roles have to be retrieved
     *
     * @return User Object containing the role information
     */
    function getRolesByUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/roles";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }

        return $userObj;
    }

    /**
     * Get all the Users who have the specified role assigned
     *
     * @param role
     *            - Role for which Users needs to be retrieved
     *
     * @return List of User Object for that particular role
     */
    function getUsersByRole($role) {

        Util::throwExceptionIfNullOrBlank($role, "Role");
        $encodedRole = Util::encodeParams($role);
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {

            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['role'] = $role;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/role/" . $encodedRole;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildArrayResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }

        return $userObj;
    }

    /**
     * Creates or Updates User Profile. First time the Profile for the user is
     * created and in future calls user profile will be updated. This will
     * always update the profile with new value passed in profile object. Call
     * to this method should have all the values you want to retain in user
     * profile object, otherwise old values of profile will get updated with
     * null. Method only updates the profile of user, passing email/password in
     * user object does not have any significance for this method call.
     *
     * @param user
     *            - User for which profile has to be updated, this should
     *            contain the userName and profile object in it.
     *
     * @returns User Object with updated Profile information
     *
     * @see Profile
     */
    function createOrUpdateProfile(User $user) {
        Util::throwExceptionIfNullOrBlank($user, "User");
        Util::throwExceptionIfNullOrBlank($user->getUserName(), "User Name");
        Util::throwExceptionIfNullOrBlank($user->getProfile(), "Profile Data");

        $objUtil = new Util($this->apiKey, $this->secretKey);

        $profile = $user->getProfile();

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            //echo date("Y-m-d\TG:i:s",strtotime($profile->dateOfBirth)).substr((string)microtime(), 1, 4)."Z";
            $body = '{"app42":{"user":{"userName":"' . $user->getUserName() . '", "profileData":{"firstName":"' . $profile->getFirstName() . '","lastName":"' . $profile->getLastName() . '","sex":"' . $profile->getSex() . '","mobile":"' . $profile->getMobile() . '","line1":"' . $profile->getLine1() . '","line2":"' . $profile->getLine2() . '","city":"' . $profile->getCity() . '","state":"' . $profile->getState() . '","country":"' . $profile->getCountry() . '","pincode":"' . $profile->getPincode() . '","homeLandLine":"' . $profile->getHomeLandLine() . '","officeLandLine":"' . $profile->getOfficeLandLine() . '","dateOfBirth":"' . (date("Y-m-d\TG:i:s", strtotime($profile->getDateOfBirth())) . substr((string) microtime(), 1, 4) . "Z") . '"}}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type; //CONTENT_TYPE;
            $accept = $this->accept; //ACCEPT;
            $this->url = $this->url . "/profile";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Authenticate user based on userName and password
     *
     * @param uName
     *            - UserName which should be unique for the App
     * @param pwd
     *            - Password for the User
     *
     * @returns App42Response Object if authenticated successfully.

     *             if authentication fails or username/password is blank or null
     */
    function authenticate($uName, $pwd) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($pwd, "Password");

        $responseObj = new App42Response();

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '","password":"' . $pwd . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/authenticate";
            $response = RestClient::post($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($userObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Updates the User's Email Address based on userName. Note: Only email can
     * be updated. Username cannot be updated.
     *
     * @param uName
     *            - UserName which should be unique for the App
     * @param emailAddress
     *            - Email address of the user
     *
     * @returns updated User Object
     */
    function updateEmail($uName, $emailAddress) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($emailAddress, "Email Address");
        Util::throwExceptionIfEmailNotValid($emailAddress, "Email Address");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '","email":"' . $emailAddress . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url;
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Gets the User details based on userName
     *
     * @param userName
     *            - Name of the User to retrieve
     *
     * @return User Object containing the profile information
     */
    function getUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;

            $this->url = $this->url . "/" . $encodedUserName;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Gets user details based on emailId
     *
     * @param emailId
     *            - EmailId of the user to be retrieved
     *
     * @return User Object
     */
    function getUserByEmailId($emailId) {

        Util::throwExceptionIfNullOrBlank($emailId, "Email Id");
        Util::throwExceptionIfEmailNotValid($emailId, "Email Address");
        $encodedEmail = Util::encodeParams($emailId);
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['emailId'] = $emailId;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/email/" . $encodedEmail;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Gets the details of all users
     *
     * @return the List that contains all User Object
     *
     */
    function getAllUsers($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $userObj;
        } else {
            /**
             * Gets all users by Paging
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return the List that contains all User Object
             */
            Util::validateMax($max);
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);
            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/paging/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $userObj;
        }
    }

    /**
     * Gets the count of all the users
     *
     * @return the count of all User exists
     */
    function getAllUsersCount() {

        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $userObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/count/all";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userObj->setStrResponse($response->getResponse());
            $userObj->setResponseSuccess(true);
            $userResponseObj = new UserResponseBuilder();
            $userObj->setTotalRecords($userResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Gets the details of all the locked users
     * 
     * @return the list containing locked User Objects
     */
    function getLockedUsers($max = null, $offset = null) {
        $argv = func_get_args();
        if (count($argv) == 0) {
            $objUtil = new Util($this->apiKey, $this->secretKey);

            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/locked";
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $userObj;
        } else {
            /**
             * Gets the details of all the locked users By paging.
             *
             * @param max
             *            - Maximum number of records to be fetched
             * @param offset
             *            - From where the records are to be fetched
             *
             * @return the List containing locked User Objects
             */
            Util::validateMax($max);
            Util::throwExceptionIfNullOrBlank($max, "Max");
            Util::throwExceptionIfNullOrBlank($offset, "Offset");
            $encodedMax = Util::encodeParams($max);
            $encodedOffset = Util::encodeParams($offset);
            $objUtil = new Util($this->apiKey, $this->secretKey);

            try {
                $params = array();
                $params['apiKey'] = $this->apiKey;
                $params['version'] = $this->version;
                date_default_timezone_set('UTC');
                $params['max'] = $max;
                $params['offset'] = $offset;
                $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
                $signature = urlencode($objUtil->sign($params)); //die();
                $params['signature'] = $signature;
                $contentType = $this->content_type;
                $accept = $this->accept;
                $this->url = $this->url . "/locked/" . $encodedMax . "/" . $encodedOffset;
                $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
                $userResponseObj = new UserResponseBuilder();
                $userObj = $userResponseObj->buildArrayResponse($response->getResponse());
            } catch (App42Exception $e) {
                throw $e;
            } catch (Exception $e) {
                throw new App42Exception($e);
            }
            return $userObj;
        }
    }

    /**
     * Gets the count of all the locked users
     *
     * @return the count of locked User exists
     */
    function getLockedUsersCount() {

        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $userObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/count/locked";
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userObj->setStrResponse($response->getResponse());
            $userObj->setResponseSuccess(true);
            $userResponseObj = new UserResponseBuilder();
            $userObj->setTotalRecords($userResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Deletes a particular user based on userName.
     *
     * @param userName
     *            - UserName which should be unique for the App
     *
     * @returns App42Response Object if user deleted successfully
     */
    function deleteUser($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
            $responseObj->setStrResponse($userObj);
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Unlocks the user based on the userName. App developers can use this
     * feature to unlock a user because of reasons specific to their usercase
     * e.g. When payment received, the App developer wants the user to be
     * active.
     *
     * @param uName
     *            - UserName which should be unique for the App
     *
     * @returns the unlocked User Object

     */
    function unlockUser($uName) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/unlock";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Locks the user based on the userName. Apps can use these feature to lock
     * a user because of reasons specific to their usercase e.g. If payment not
     * received and the App wants the user to be inactive
     *
     * @param uName
     *            - UserName which should be unique for the App
     * 
     * @returns the locked User Object
     */
    function lockUser($uName) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/lock";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $userResponseObj = new UserResponseBuilder();
            $userObj = $userResponseObj->buildResponse($response->getResponse());
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Changes the password for user based on the userName.
     *
     * @param uName
     *            - UserName which should be unique for the App
     * @param oldPwd
     *            - Old Password for the user for authentication
     * @param newPwd
     *            - New Password for the user to change
     *
     * @returns App42Response Object if updated successfully
     */
    function changeUserPassword($uName, $oldPwd, $newPwd) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        Util::throwExceptionIfNullOrBlank($oldPwd, "Old Password");
        Util::throwExceptionIfNullOrBlank($newPwd, "New Password");
        $responseObj = new App42Response();

        if ($oldPwd == $newPwd) {
            throw new App42Exception("Old password and new password are same");
        }
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '","oldPassword":"' . $oldPwd . '","newPassword":"' . $newPwd . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/changeUserPassword";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Revokes the specified role from the user.
     *
     * @param userName
     *            - UserName from whom the role has to be revoked
     * @param role
     *            - Role that has to be revoked
     *
     * @returns App42Response of the object that contains the information about
     *          User with its role
     */
    function revokeRole($userName, $role) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        Util::throwExceptionIfNullOrBlank($role, "Role");
        $encodedUserName = Util::encodeParams($userName);
        $encodedRole = Util::encodeParams($role);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['role'] = $role;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/revoke/" . $encodedRole;
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Revokes all the roles from the user.
     *
     * @param userName
     *            - Name of the User from whom Roles have to be revoked
     *
     * @returns App42Response of the object that contains the User information
     */
    function revokeAllRoles($userName) {

        Util::throwExceptionIfNullOrBlank($userName, "User Name");
        $encodedUserName = Util::encodeParams($userName);
        $responseObj = new App42Response();
        $objUtil = new Util($this->apiKey, $this->secretKey);
        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['userName'] = $userName;
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/" . $encodedUserName . "/revoke";
            $response = RestClient::delete($this->url, $params, null, null, $contentType, $accept);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Updates the User password based on userName. Username cannot be updated.
     *
     * @param uName
     *            - UserName which should be unique for the App
     * @param pwd
     *            - Password to be reset
     *
     * @returns App42Response Object
     */
    function resetUserPassword($uName, $pwd) {

        Util::throwExceptionIfNullOrBlank($uName, "User Name");
        $responseObj = new App42Response();
        Util::throwExceptionIfNullOrBlank($pwd, "Password");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        try {
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $body = null;
            $body = '{"app42":{"user":{"userName":"' . $uName . '","password":"' . $pwd . '"}}}';

            $params['body'] = $body;
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/resetUserPassword";
            $response = RestClient::put($this->url, $params, null, null, $contentType, $accept, $body);
            $responseObj->setStrResponse($response->getResponse());
            $responseObj->setResponseSuccess(true);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $responseObj;
    }

    /**
     * Gets the list of Users based on Profile Data
     *
     * @param profileData
     *            - Profile Data key/value for which Users need to be retrieved
     *
     * @return List of User Object for the specified profile data
     */
    function getUsersByProfileData(Profile $profileData) {
        Util::throwExceptionIfNullOrBlank($profileData, "profile Data");
        $objUtil = new Util($this->apiKey, $this->secretKey);

        $parameters = $this->fillParamsWithProfileData($profileData);
        $encodedParameters = Util::encodeParams($parameters);
        try {
            $userObj = new App42Response();
            $params = array();
            $params['apiKey'] = $this->apiKey;
            $params['version'] = $this->version;
            date_default_timezone_set('UTC');
            $params['timeStamp'] = date("Y-m-d\TG:i:s") . substr((string) microtime(), 1, 4) . "Z";
            $signature = urlencode($objUtil->sign($params)); //die();
            $params['signature'] = $signature;
            $contentType = $this->content_type;
            $accept = $this->accept;
            $this->url = $this->url . "/profile/" . $encodedParameters;
            $response = RestClient::get($this->url, $params, null, null, $contentType, $accept);
            $userObj->setStrResponse($response->getResponse());
            $userObj->setResponseSuccess(true);
            $userResponseObj = new UserResponseBuilder();
            $userObj->setTotalRecords($userResponseObj->getTotalRecords($response->getResponse()));
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $userObj;
    }

    /**
     * Builds a Parameter string for the profileData.
     *
     * @param profileData
     *            - User.Profile object that contains profile information
     *
     * @returns String Object which contains the parameter string.
     *
     */
    function fillParamsWithProfileData(Profile $profileData) {
        $profileDataCond = "";


        $city = $profileData->getCity();
        $country = $profileData->getCountry();
        $toBeformettedDate = $profileData->getDateOfBirth();
        $dateOfBirth = date("Y-m-d\TG:i:s", strtotime($toBeformettedDate));
        $firstName = $profileData->getFirstName();
        $lastName = $profileData->getLastName();
        $homeLandLine = $profileData->getHomeLandLine();
        $line1 = $profileData->getLine1();
        $line2 = $profileData->getLine2();
        $mobile = $profileData->getMobile();
        $officeLandLine = $profileData->getOfficeLandLine();
        $pincode = $profileData->getPincode();
        $sex = $profileData->getSex();
        $state = $profileData->getState();

        if ($city != null && $city != "") {
            $profileDataCond .= "city:$city!";
        }
        if ($country != null && $country != "") {
            $profileDataCond .= "country:$country!";
        }
        if ($dateOfBirth != null && $dateOfBirth != "") {
            $profileDataCond .= "date_of_birth:$dateOfBirth!";
        }

        if ($firstName != null && $firstName != "") {

            $profileDataCond.= "first_name:$firstName!";
        }
        if ($lastName != null && $lastName != "") {
            $profileDataCond .= "last_name:$lastName!";
        }
        if ($homeLandLine != null && $homeLandLine != "") {
            $profileDataCond .= "home_land_line:$homeLandLine!";
        }
        if ($line1 != null && $line1 != "") {
            $profileDataCond .= "line1:$line1!";
        }
        if ($line2 != null && $line2 != "") {
            $profileDataCond .= "line2:$line2!";
        }
        if ($mobile != null && $mobile != "") {
            $profileDataCond .= "mobile:$mobile!";
        }
        if ($officeLandLine != null && $officeLandLine != "") {
            $profileDataCond .= "office_land_line:$officeLandLine!";
        }
        if ($pincode != null && $pincode != "") {
            $profileDataCond .= "pincode:$pincode!";
        }
        if ($sex != null && $sex != "") {
            $profileDataCond .= "sex:$sex!";
        }
        if ($state != null && $state != "") {
            $profileDataCond .= "state:$state!";
        }
        return $profileDataCond;
    }

}

?>
