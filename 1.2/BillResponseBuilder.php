<?php namespace com\shephertz\app42\paas\sdk\php\appTab;
use com\shephertz\app42\paas\sdk\php\appTab\Bill;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;

include_once "JSONObject.php";
include_once "Bill.php";
include_once "App42ResponseBuilder.php";
/**
*
* BillResponseBuilder class converts the JSON response retrieved from the
* server to the value object i.e Bill
*
*/
class BillResponseBuilder extends App42ResponseBuilder {
/**
 * Converts the response in JSON format to the value object i.e Bill
 *
 * @param json
 *            - response in JSON format
 *
 * @return Bill object filled with json data
 */
	public function buildResponse($json) {
		$billsJsonObj =  $this->getServiceJSONObject("bills", $json);	
		$billJsonObj = $billsJsonObj->__get("bill");
		$bill = new Bill();
		$bill = $this->buildBillObject($billJsonObj);
		$bill->setStrResponse($json);
		$bill->setResponseSuccess($this->isRespponseSuccess($json));
		
		return $bill;
	}
        /**
	 * Converts the Bill JSON object to the value object i.e Bill
	 *
	 * @param billJSONObj
	 *            - Bill data as JSONObject
	 *
	 * @return Bill object filled with json data
	 *
	 */

	private function buildBillObject($billJSONObj)  {
		
		$billJSONObj = new JSONObject($billJSONObj);
		$bill = new Bill();
		$this->buildObjectFromJSONTree($bill, $billJSONObj);
		if($billJSONObj->has("storageTransaction")) {
			$storageTransJson  = $billJSONObj->__get("storageTransaction");
			$storageTrans = new StorageTransaction($bill);
			$this->buildObjectFromJSONTree($storageTrans,$storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction =  new TransactionStor($storageTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction =  new TransactionStor($storageTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		if($billJSONObj->has("timeTransaction")) {
			$storageTransJson  = $billJSONObj->__get("timeTransaction");
			$timeTrans = new TimeTransaction($bill);
			$this->buildObjectFromJSONTree($timeTrans, $storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction =  new TransactionTim($timeTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction = new TransactionTim($timeTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		if($billJSONObj->has("bandWidthTransaction")) {
			$storageTransJson  = $billJSONObj->__get("bandWidthTransaction");
			$bwTrans = new BandwidthTransaction($bill);
			$this->buildObjectFromJSONTree($bwTrans, $storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction = new TransactionBand($bwTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction =  new TransactionBand($bwTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		
		if($billJSONObj->has("levelTransaction")) {
			$storageTransJson  = $billJSONObj->__get("levelTransaction");
			$levelTrans = new LevelTransaction($bill);
			$this->buildObjectFromJSONTree($levelTrans, $storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction =  new TransactionLev($levelTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction =  new TransactionLev($levelTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		if($billJSONObj->has("featureTransaction")) {
			$storageTransJson  = $billJSONObj->__get("featureTransaction");
			$featureTrans = new FeatureTransaction($bill);
			$this->buildObjectFromJSONTree($featureTrans, $storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction =  new TransactionFeat($featureTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction =  new TransactionFeat($featureTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		if($billJSONObj->has("licenseTransaction")) {
			$storageTransJson  = $billJSONObj->__get("licenseTransaction");
			$licenseTrans = new LicenseTransaction($bill);
			$this->buildObjectFromJSONTree($licenseTrans, $storageTransJson);
			if($storageTransJson->has("transactions") && $storageTransJson->__get("transactions")->has("transaction")) {
				if($storageTransJson->__get("transactions")->__get("transaction") instanceof JSONObject) {
					$transactionJsonObj = $storageTransJson->__get("transactions")->__get("transaction");
					$transaction =  new TransactionLic($licenseTrans);
					$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					
				}else {
					$transactionJsonArray = $storageTransJson->__get("transactions")->getJSONArray("transaction");
					for($i=0; $i< count($transactionJsonArray); $i++) {
						$transactionJsonObj =  $transactionJsonArray[$i];
						$transaction = new TransactionLic($licenseTrans);
						$transactionJsonObj = new JSONObject($transactionJsonObj);
						$this->buildObjectFromJSONTree($transaction, $transactionJsonObj);
					}
				}
			}
			
		}
		return $bill;
	}
	/**
	 * Converts the response in JSON format to the list of value objects i.e
	 * Bill
	 *
	 * @param json
	 *            - response in JSON format
	 *
	 * @return List of Bill object filled with json data
	 *
	 */
	public function buildArrayResponse($json)  {
		$billsJsonObj =  $this->getServiceJSONObject("bills", $json);	
		$billList = array();
		
		if($billsJsonObj->__get("bill") instanceof JSONObject){
		
			$billJsonObj = $billsJsonObj->__get("bill");
			$bill = new Bill();
			$bill = $this->buildBillObject($billJsonObj);
			$bill->setStrResponse($json);
			$bill->setResponseSuccess($this->isRespponseSuccess(json));
			array_push($billList, $bill);
		
		}
		
		else{
		
		$billJsonArray = $billsJsonObj->getJSONArray("bill");
		
		for($i=0; $i< count($billJsonArray); $i++) {
			$billJsonObj = $billJsonArray[$i];
			$bill = new Bill();
			$bill = $this->buildBillObject($billJsonObj);
			$bill->setStrResponse($json);
			$bill->setResponseSuccess($this->isRespponseSuccess(json));
			array_push($billList, $bill);
		}
	}
	
		return $billList;
	}

}
		
?>