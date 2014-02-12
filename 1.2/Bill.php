<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

/**
 *
 * This Bill object is the value object which contains the properties of Bill
 * along with the setter & getter for those properties.
 *
 */
class Bill extends App42Response {

    public $userName;
    public $usageName;
    public $month;
    public $totalUsage;
    public $totalCost;
    public $currency;
    public $licenseTransaction;
    public $storageTransaction;
    public $levelTransaction;
    public $featureTransaction;
    public $bandwidthTransaction;
    public $timeTransaction;

    /**
     * Returns the transactions made for license.
     * 
     * @return the transactions made for license.
     */
    public function getLicenseTransaction() {
        return $this->licenseTransaction;
    }

    /**
     * Sets the transactions for license.
     *
     * @param licenseTransaction
     *            - transactions for license.
     *
     */
    public function setLicenseTransaction($licenseTransaction) {
        $this->licenseTransaction = $licenseTransaction;
    }

    /**
     * Returns the transactions made for storage capacity.
     *
     * @return the transactions made for storage capacity.
     */
    public function getStorageTransaction() {
        return $this->storageTransaction;
    }
    /**
     * Sets the transactions for storage capacity.
     *
     * @param storageTransaction
     *            - transactions for storage.
     *
     */
    public function setStorageTransaction($storageTransaction) {
        $this->storageTransaction = $storageTransaction;
    }
    /**
     * Returns the transactions made as per levels.
     *
     * @return the transactions made as per levels.
     */
    public function getLevelTransaction() {
        return $this->levelTransaction;
    }
    /**
     * Sets the transactions as per levels.
     *
     * @param levelTransaction
     *            - transactions as per levels
     *
     */
    public function setLevelTransaction($levelTransaction) {
        $this->levelTransaction = $levelTransaction;
    }
    /**
     * Returns the transactions made as per the features.
     *
     * @return the transactions made as per the features.
     */
    public function getFeatureTransaction() {
        return $this->featureTransaction;
    }
    /**
     * Sets the transactions as per the features.
     *
     * @param featureTransaction
     *            - transactions as per the features.
     *
     */
    public function setFeatureTransaction($featureTransaction) {
        $this->featureTransaction = $featureTransaction;
    }
    /**
     * Returns the transactions made as per the bandwidth usage.
     *
     * @return the transactions made as per the bandwidth usage.
     */
    public function getBandwidthTransaction() {
        return $this->bandwidthTransaction;
    }
    /**
     * Sets the transactions as per the bandwidth usage.
     *
     * @param bandwidthTransaction
     *            - transactions as per the bandwidth usage.
     *
     */
    public function setBandwidthTransaction($bandwidthTransaction) {
        $this->bandwidthTransaction = $bandwidthTransaction;
    }
    /**
     * Returns the transactions made as per the time.
     *
     * @return the transactions made as per the time.
     */
    public function getTimeTransaction() {
        return $this->timeTransaction;
    }
    /**
     * Sets the transactions as per the time.
     *
     * @param timeTransaction
     *            - transactions as per the time.
     *
     */
    public function setTimeTransaction($timeTransaction) {
        $this->timeTransaction = $timeTransaction;
    }
    /**
     * Returns the name of the user for whom the bill will be generated.
     *
     * @return the name of the user.
     */
    public function getUserName() {
        return $this->userName;
    }
    /**
     * Sets the name of the user for whom the bill will be generated.
     *
     * @param userName
     *            - name of the user
     *
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }
    /**
     * Returns the name of the Usage type whether its Level, Time, Storage,
     * Bandwidth, Feature or License.
     *
     * @return the name of the Usage type
     */
    public function getUsageName() {
        return $this->usageName;
    }
    /**
     * Sets the name of the Usage type.
     *
     * @param usageName
     *            - name of the Usage type
     *
     */
    public function setUsageName($usageName) {
        $this->usageName = $usageName;
    }
    /**
     * Returns the name of month the bill was generated.
     *
     * @return the name of month the bill was generated.
     */
    public function getMonth() {
        return $this->month;
    }
    /**
     * Sets the name of month for bill to be generated.
     *
     * @param month
     *            - name of month
     *
     */
    public function setMonth($month) {
        $this->month = $month;
    }
    /**
     * Returns the total amount usage for a particular app.
     *
     * @return the total amount usage for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the total amount of usage for a particular app..
     *
     * @param totalUsage
     *            - total amount of usage.
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost for an app.
     *
     * @return the total cost for an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the the total cost for an app.
     *
     * @param totalCost
     *            - total cost for an app.
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
     * Returns the type of the currency in which the total cost is computed.
     *
     * @return the type of the currency in which the total cost is computed.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which the total cost has to be computed.
     *
     * @param currency
     *            - type of the currency.
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

}
/**
* An inner class that contains the remaining properties of the Bill.
*
*/
class LicenseTransaction {

    public $cost;
    public $currency;
    public $transactionList = array();
    public $totalUsage;
    public $totalCost;

    public function __construct(Bill $bill) {

        $bill->licenseTransaction = $this;
    }
    /**
     * Returns the cost computed as per the levels of an app.
     *
     * @return the cost computed as per the levels
     */
    public function getCost() {
        return $this->$cost;
    }
    /**
     * Sets the cost as per the levels
     *
     * @param cost
     *            - cost as per the levels
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
     * Returns the list of all the transactions done.
     *
     * @return the list of all the transactions done.
     */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
     * Sets the list of all the transactions done.
     *
     * @param transactionList
     *            - list of all the transactions done.
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the total amount of usage for an app
     *
     * @param totalUsage
     *            - total amount of usage
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost computed as per the levels of an app.
     *
     * @return the total cost computed as per the levels of an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the total cost as per the levels of an app.
     *
     * @param totalCost
     *            - total cost computed as per the levels of an app.
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }

}
/**
* An inner class that contains the remaining properties of the License
* Transaction.
*
*/
class TransactionLic {

    public $serviceName;
    public $transactionId;
    public $issueDate;
    public $valid;
    public $key;

    public function __construct(LicenseTransaction $licenseTransaction) {
        array_push($licenseTransaction->transactionList, $this);
    }
    /**
     * Returns the name of the service.
     *
     * @return the name of the service.
     */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
     * Sets the name of the service
     *
     * @param serviceName
     *            - name of the service
     *
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
     * Returns the date when the license was issued.
     *
     * @return the date when the license was issued.
     */
    public function getIssueDate() {
        return $this->issueDate;
    }
    /**
     * Sets the date for the license to be issued.
     *
     * @param issueDate
     *            - date when the license was issued.
     *
     */
    public function setIssueDate($issueDate) {
        $this->issueDate = $issueDate;
    }
    /**
     * Returns the valid information of the license.
     *
     * @return the valid information of the license.
     */
    public function getValid() {
        return $this->valid;
    }
    /**
     * Sets the valid information of the license.
     *
     * @param valid
     *            - valid information of the license.
     *
     */
    public function setValid($valid) {
        $this->valid = $valid;
    }
    /**
     * Returns the key
     *
     * @return the key
     */
    public function getKey() {
        return $this->key;
    }
    /**
     * Sets the key
     *
     * @param key
     *            - key
     *
     */
    public function setKey($key) {
        $this->key = $key;
    }

}
/**
* An inner class that contains the remaining properties of the Bill.
*
*/
class StorageTransaction {

    public $cost;
    public $currency;
    public $unit;
    public $space;
    public $totalUsage;
    public $totalCost;
    public $transactionList = array();

    public function __construct(Bill $bill) {

        $bill->storageTransaction = $this;
    }
    /**
     * Returns the cost computed as per the storage for an app.
     *
     * @return the cost computed as per the storage
     */
    public function getCost() {
        return $this->cost;
    }
    /**
     * Sets the cost as per the storage
     *
     * @param cost
     *            - cost as per the storage
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
     * Returns the storage unit taken by an app whether its KB, MB, GB or
     * TB.
     *
     * @return the storage unit taken by an app
     */
    public function getUnit() {
        return $this->unit;
    }
    /**
     * Sets the storage unit for an app
     *
     * @param unit
     *            - storage unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }
    /**
     * Returns the space taken for an app.
     *
     * @return the space taken for an app.
     */
    public function getSpace() {
        return $this->space;
    }
    /**
     * Sets the space for an app.
     *
     * @param space
     *            - space for an app.
     *
     */
    public function setSpace($space) {
        $this->space = $space;
    }
    /**
     * Returns the list of all the transactions made.
     *
     * @return the list of all the transactions
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the action performed on the app.
     *
     * @param action
     *            - action performed on the app.
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the total amount of usage for an app
     *
     * @param totalUsage
     *            - total amount of usage
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
     * Returns the list of all the transactions made.
     *
     * @return the list of all the transactions
     */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
     * Sets the list of all the transactions.
     *
     * @param transactionList
     *            - list of all the transactions
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}
/**
 * An inner class that contains the remaining properties of the Level
 * Transaction.
 *
 */
class TransactionStor {

    public $serviceName;
    public $usage;
    public $transactionId;
    public $usageDate;

    public function __construct(StorageTransaction $storageTransaction) {
        array_push($storageTransaction->transactionList, $this);
    }
    /**
     * Returns the name of the service.
     *
     * @return the name of the service.
     */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
     * Sets the name of the service
     *
     * @param serviceName
     *            - name of the service
     *
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the amount of usage done by an app.
     *
     * @return the amount of usage done by an app.
     */
    public function getUsage() {
        return $this->usage;
    }
    /**
     * Sets the amount of usage by an app.
     *
     * @param usage
     *            - amount of usage by an app.
     *
     */
    public function setUsage($usage) {
        $this->usage = $usage;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
     * Returns the date of usage by an app.
     *
     * @return the date of usage by an app.
     */
    public function getUsageDate() {
        return $this->usageDate;
    }
    /**
     * Sets the date of usage by an app.
     *
     * @param usageDate
     *            - date of usage by an app.
     *
     */
    public function setUsageDate($usageDate) {
        $this->usageDate = $usageDate;
    }

}
/**
 * An inner class that contains the remaining properties of the Bill.
 *
 */
class LevelTransaction {

    public $cost;
    public $currency;
    public $transactionList = array();
    public $totalUsage;
    public $totalCost;

    public function __construct(Bill $bill) {

        $bill->levelTransaction = $this;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the total amount of usage for an app
     *
     * @param totalUsage
     *            - total amount of usage
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost computed as per the levels of an app.
     *
     * @return the total cost computed as per the levels of an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the total cost as per the levels of an app.
     *
     * @param totalCost
     *            - total cost computed as per the levels of an app.
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
     * Returns the cost computed as per the storage for an app.
     *
     * @return the cost computed as per the storage
     */
    public function getCost() {
        return $this->cost;
    }
    /**
     * Sets the cost as per the storage
     *
     * @param cost
     *            - cost as per the storage
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
     * Returns the list of all the transactions made.
     *
     * @return the list of all the transactions
     */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
     * Sets the list of all the transactions.
     *
     * @param transactionList
     *            - list of all the transactions
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}
/**
 * An inner class that contains the remaining properties of the Level
 * Transaction.
 *
 */
class TransactionLev {

    public function __construct(LevelTransaction $levelTransaction) {
        array_push($levelTransaction->transactionList, $this);
    }
    /**
     * Returns the name of the service.
     *
     * @return the name of the service.
     */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
     * Sets the name of the service
     *
     * @param serviceName
     *            - name of the service
     *
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
    * Returns the date of usage by an app.
    *
    * @return the date of usage by an app.
    *
    */
    public function getUsageDate() {
        return $this->usageDate;
    }
    /**
     * Sets the date of usage by an app.
     *
     * @param usageDate
     *            - date of usage by an app.
     *
     */
    public function setUsageDate($usageDate) {
        $this->usageDate = $usageDate;
    }

    public $serviceName;
    public $transactionId;
    public $usageDate;

}
/**
 * An inner class that contains the remaining properties of the Bill.
 *
 */
class FeatureTransaction {

    public $cost;
    public $currency;
    public $transactionList = array();
    public $totalUsage;
    public $totalCost;

    public function __construct(Bill $bill) {

        $bill->featureTransaction = $this;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the total amount of usage for an app
     *
     * @param totalUsage
     *            - total amount of usage
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost computed as per the features of an app.
     *
     * @return the total cost computed as per the festures of an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the total cost as per the features of an app.
     *
     * @param totalCost
     *            - total cost computed as per the features of an app.
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
     * Returns the cost computed as per the features for an app.
     *
     * @return the cost computed as per the features
     */
    public function getCost() {
        return $this->cost;
    }
    /**
     * Sets the cost as per the features
     *
     * @param cost
     *            - cost as per the features
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
     * Returns the list of all the transactions made.
     *
     * @return the list of all the transactions
     */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
     * Sets the list of all the transactions.
     *
     * @param transactionList
     *            - list of all the transactions
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}
/**
 * An inner class that contains the remaining properties of the Feature
 * Transaction.
 *
 */
class TransactionFeat {

    public $serviceName;
    public $transactionId;
    public $usageDate;

    public function __construct(FeatureTransaction $featureTransaction) {
        array_push($featureTransaction->transactionList, $this);
    }
    /**
    * Returns the name of the service.
    *
    * @return the name of the service.
    */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
     * Sets the name of the service
     *
     * @param serviceName
     *            - name of the service
     *
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
     * Returns the date of usage by an app.
     *
     * @return the date of usage by an app.
     */
    public function getUsageDate() {
        return $this->usageDate;
    }
    /**
     * Sets the date of usage by an app.
     *
     * @param usageDate
     *            - date of usage by an app.
     *
     */
    public function setUsageDate($usageDate) {
        $this->usageDate = $usageDate;
    }

}

class BandwidthTransaction {

    public $cost;
    public $currency;
    public $unit;
    public $bandwidth;
    public $totalUsage;
    public $totalCost;
    public $transactionList = array();

    public function __construct(Bill $bill) {

        $bill->bandwidthTransaction = $this;
    }
    /**
     * Returns the cost computed as per the storage for an app.
     *
     * @return the cost computed as per the storage
     */
    public function getCost() {
        return $this->cost;
    }
    /**
     * Sets the cost as per the storage
     *
     * @param cost
     *            - cost as per the storage
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
     * Returns the type of the currency in which bill is generated.
     *
     * @return the type of the currency in which bill is generated.
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
     * Returns the bandwidth unit taken by an app whether its KB, MB, GB or
     * TB.
     *
     * @return the bandwidth unit taken by an app
     */
    public function getUnit() {
        return $this->unit;
    }
    /**
     * Sets the bandwidth unit for an app
     *
     * @param unit
     *            - bandwidth unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }
    /**
     * Returns the bandwidth value of an app.
     *
     * @return the bandwidth value of an app.
     */
    public function getBandwidth() {
        return $this->bandwidth;
    }
    /**
     * Sets the bandwidth value of an app.
     *
     * @param bandwidth
     *            - bandwidth value of an app.
     *
     */
    public function setBandwidth($bandwidth) {
        $this->bandwidth = $bandwidth;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
    * Sets the total amount of usage for an app
    *
    * @param totalUsage
    *            - total amount of usage
    *
    */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost computed as per the bandwidth of an app.
     *
     * @return the total cost computed as per the bandwidth of an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
     * Sets the total cost as per the bandwidth of an app.
     *
     * @param totalCost
     *            - total cost computed as per the bandwidth of an app.
     *
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
     * Returns the list of all the transactions made.
     *
     * @return the list of all the transactions
     */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
     * Sets the list of all the transactions.
     *
     * @param transactionList
     *            - list of all the transactions
     *
     */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}
/**
 * An inner class that contains the remaining properties of the
 * Bandwidth Transaction.
 *
 */
class TransactionBand {

    public $serviceName;
    public $usage;
    public $transactionId;
    public $usageDate;

    public function __construct(BandwidthTransaction $bandwidthTransaction) {
        array_push($bandwidthTransaction->transactionList, $this);
    }
    /**
    * Returns the name of the service.
    *
    * @return the name of the service.
    */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
    * Sets the name of the service
    *
    * @param serviceName
    *            - name of the service
    *
    */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the amount of usage done by an app.
     *
     * @return the amount of usage done by an app.
     */
    public function getUsage() {
        return $this->usage;
    }
    /**
    * Sets the amount of usage by an app.
    *
    * @param usage
    *            - amount of usage by an app.
    *
    */
    public function setUsage($usage) {
        $this->usage = $usage;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
    * Returns the date of usage by an app.
    *
    * @return the date of usage by an app.
    */
    public function getUsageDate() {
        return $this->usageDate;
    }
    /**
     * Sets the date of usage by an app.
     *
     * @param usageDate
     *            - date of usage by an app.
     *
     */
    public function setUsageDate($usageDate) {
        $this->usageDate = $usageDate;
    }

}
/**
* An inner class that contains the remaining properties of the Bill.
*
*/
class TimeTransaction {

    public $cost;
    public $currency;
    public $unit;
    public $time;
    public $totalUsage;
    public $totalCost;
    public $transactionList = array();

    public function __construct(Bill $bill) {

        $bill->timeTransaction = $this;
    }
    /**
     * Returns the cost computed as per the time.
     *
     * @return the cost computed as per the time
     */
    public function getCost() {
        return $this->cost;
    }
    /**
     * Sets the cost as per the time
     *
     * @param cost
     *            - cost as per the time
     *
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }
    /**
    * Returns the type of the currency in which bill is generated.
    *
    * @return the type of the currency in which bill is generated.
    */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Sets the type of the currency in which bill has to be generated.
     *
     * @param currency
     *            - type of the currency
     *
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    /**
    * Returns the time unit taken by an app whether its SECONDS, MINUTES or
    * HOURS.
    *
    * @return the time unit taken by an app
    */
    public function getUnit() {
        return $this->unit;
    }
    /**
     * Sets the time unit for an app
     *
     * @param unit
     *            - time unit for an app
     *
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }
    /**
     * Returns the time unit value recorded at the time of execution.
     *
     * @return the time unit value recorded at the time of execution.
     */
    public function getTime() {
        return $this->time;
    }
    /**
     * Sets the time unit value for an app.
     *
     * @param time
     *            - action performed on the app.
     *
     */
    public function setTime($time) {
        $this->time = $time;
    }
    /**
     * Returns the total amount of usage done for a particular app.
     *
     * @return the total amount of usage done for a particular app.
     */
    public function getTotalUsage() {
        return $this->totalUsage;
    }
    /**
     * Sets the total amount of usage for an app
     *
     * @param totalUsage
     *            - total amount of usage
     *
     */
    public function setTotalUsage($totalUsage) {
        $this->totalUsage = $totalUsage;
    }
    /**
     * Returns the total cost computed as per the time of an app.
     *
     * @return the total cost computed as per the time of an app.
     */
    public function getTotalCost() {
        return $this->totalCost;
    }
    /**
    * Sets the total cost as per the time of an app.
    *
    * @param totalCost
    *            - total cost computed as per the time of an app.
    *
    */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }
    /**
    * Returns the list of all the transactions made.
    *
    * @return the list of all the transactions
    */
    public function getTransactionList() {
        return $this->transactionList;
    }
    /**
    * Sets the list of all the transactions.
    *
    * @param transactionList
    *            - list of all the transactions
    *
    */
    public function setTransactionList($transactionList) {
        $this->transactionList = $transactionList;
    }

}
/**
* An inner class that contains the remaining properties of the Time
* Transaction.
*
*/
class TransactionTim {

    public $serviceName;
    public $usage;
    public $transactionId;
    public $usageDate;

    public function __construct(TimeTransaction $timeTransaction) {
        array_push($timeTransaction->transactionList, $this);
    }
    /**
     * Returns the name of the service.
     *
     * @return the name of the service.
     */
    public function getServiceName() {
        return $this->serviceName;
    }
    /**
     * Sets the name of the service
     *
     * @param serviceName
     *            - name of the service
     *
     */
    public function setServiceName($erviceName) {
        $this->serviceName = $serviceName;
    }
    /**
     * Returns the amount of usage done by an app.
     *
     * @return the amount of usage done by an app.
     */
    public function getUsage() {
        return $this->usage;
    }
    /**
     * Sets the amount of usage by an app.
     *
     * @param usage
     *            - amount of usage by an app.
     *
     */
    public function setUsage($usage) {
        $this->usage = $usage;
    }
    /**
     * Returns the transaction Id that is created when an app is
     * complete.
     *
     * @return the transaction Id that is created when an app is
     *         complete.
     */
    public function getTransactionId() {
        return $this->transactionId;
    }
    /**
     * Sets the transaction Id for an app.
     *
     * @param transactionId
     *            - transaction Id of an app
     *
     */
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
    }
    /**
     * Returns the date of usage by an app.
     *
     * @return the date of usage by an app.
     */
    public function getUsageDate() {
        return $this->$this->usageDate;
    }
    /**
     * Sets the date of usage by an app.
     *
     * @param usageDate
     *            - date of usage by an app.
     *
     */
    public function setUsageDate($usageDate) {
        $this->usageDate = $usageDate;
    }

}
?>