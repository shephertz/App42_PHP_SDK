<?php
namespace com\shephertz\app42\paas\sdk\php\charge;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";
/**
 *
 * This Charge object is the value object which contains the properties of
 * Charge along with the setter & getter for those properties.
 *
 */
class Charge extends App42Response {

    public $transactionList = array();

    /**
     * Returns the list of all the transactions made.
     *
     * @@return the list of all the transactions
     */
    public function getTransactionList() {
        return $this->transactionList;
    }

    /**
     * Sets the list of all the transactions.
     *
     * @params transactionList
     *            - list of all the transactions
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}

/**
 * An inner class that contains the remaining properties of the Charge.
 *
 */
class Transaction {

    /**
     * This is a constructor that takes no parameter
     *
     */
    public function __construct(Charge $charge) {
        array_push($charge->transactionList, $this);
    }

    public $action;
    public $login;
    public $transactionId;
    public $state;
    public $startTime;
    public $endTime;
    public $totalTime;
    public $serviceName;
    public $apiKey;

    /**
     * Returns the action performed on the app i.e. OPEN or CLOSE.
     * In case of chargeStart it will be OPEN.
     * In case of chargeStop it will be CLOSE.
     *
     * @return the action performed on the app.
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Sets the action performed on the app.
     *
     * @params action
     *            - action performed on the app.
     *
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * Returns the login name by which app was used.
     *
     * @return the login name by which app was used.
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * Sets the login name by which app was used.
     *
     * @param login
     *            - login name by which app was used.
     *
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * Returns the transaction Id.
     *
     * @return the transaction Id.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }

    /**
     * Sets the transaction Id.
     *
     * @param transactionId
     *            - transaction Id.
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }

    /**
     * Returns the state of an app on which charge will be applied.
     *
     * @return the state of an app on which charge will be applied.
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state of an app.
     *
     * @param state
     *            - state of an app
     *
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Returns the time the app was started.
     *
     * @return the time the app was started.
     */
    public function getStartTime() {
        return $this->startTime;
    }

    /**
     * Sets the time the app was started.
     *
     * @param startTime
     *            - time the app was started.
     *
     */
    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

    /**
     * Returns the time the app was stopped.
     *
     * @return the time the app was stopped.
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * Sets the time the app was stopped.
     *
     * @param endTime
     *            - time the app was stopped.
     *
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * Returns the total time the app was used.
     *
     * @return the total time the app was used.
     */
    public function getTotalTime() {
        return $this->totalTime;
    }

    /**
     * Sets the total time the app was used.
     *
     * @param totalTime
     *            - total time the app was used.
     *
     */
    public function setTotalTime($totalTime) {
        $this->totalTime = $totalTime;
    }

    /**
     * Returns the service name.
     *
     * @return the service name.
     */
    public function getServiceName() {
        return $this->serviceName;
    }

    /**
     * Sets the service name.
     *
     * @param serviceName
     *            - service name.
     *
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }

    /**
     * Returns the apiKey
     *
     * @return the apiKey
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * Sets the service name.
     *
     * @param serviceName
     *            - service name.
     *
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }
    /**
     * Returns the Charge Response in JSON format.
     *
     * @return the response in JSON format.
     *
     */
    public function toString() {
        return " login : " . $this->login . " : transactionId : " . $this->transactionId . " : state : " . $this->state . " : startTime : " . $this->startTime . " : endTime : " . $this->endTime ." : totalTime : " . $this->totalTime. " : serviceName : " . $this->serviceName . " : apiKey : " . $this->apiKey;
    }
}

?>
