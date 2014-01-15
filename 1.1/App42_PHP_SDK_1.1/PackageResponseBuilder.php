<?php
namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\App42ResponseBuilder;
use com\shephertz\app42\paas\sdk\php\appTab\PackageData;

//use com\shephertz\app42\paas\sdk\php\appTab\DiscountData\Discount;
include_once "JSONObject.php";
include_once "App42ResponseBuilder.php";
include_once "PackageData.php";

class PackageResponseBuilder extends App42ResponseBuilder {
//   public function buildResponse($json) {
//		$schemesJSONObj = getServiceJSONObject("schemes", $json);
//		$schemeJSONObj = schemesJSONObj->getJSONObject("scheme");
//		$packageData = buildPackageDataObject($schemeJSONObj);
//		packageData->setStrResponse($json);
//		packageData->setResponseSuccess(isResponseSuccess(json));
//		return packageData;
//	}
//
//	private PackageData buildPackageDataObject(JSONObject schemeJSONObj)
//			throws Exception {
//		PackageData packageObj = new PackageData();
//		buildObjectFromJSONTree(packageObj, schemeJSONObj);
//		if (schemeJSONObj.has("packages")
//				&& schemeJSONObj.getJSONObject("packages").has("bandwidth")) {
//			if (schemeJSONObj.getJSONObject("packages").get("bandwidth") instanceof JSONObject) {
//				JSONObject bandwidthJSONObj = schemeJSONObj.getJSONObject(
//						"packages").getJSONObject("bandwidth");
//				PackageData.Bandwidth bandwidth = packageObj.new Bandwidth();
//				buildObjectFromJSONTree(bandwidth, bandwidthJSONObj);
//			} else {
//				JSONArray packagesJSONArray = schemeJSONObj.getJSONObject(
//						"packages").getJSONArray("bandwidth");
//				for (int i = 0; i < packagesJSONArray.length(); i++) {
//					JSONObject bandwidthJSONObj = packagesJSONArray
//							.getJSONObject(i);
//					PackageData.Bandwidth bandwidth = packageObj.new Bandwidth();
//					buildObjectFromJSONTree(bandwidth, bandwidthJSONObj);
//				}
//
//			}
//		}
//		if (schemeJSONObj.has("packages")
//				&& schemeJSONObj.getJSONObject("packages").has("feature")) {
//			if (schemeJSONObj.getJSONObject("packages").get("feature") instanceof JSONObject) {
//				JSONObject featureJSONObj = schemeJSONObj.getJSONObject(
//						"packages").getJSONObject("feature");
//				PackageData.Feature feature = packageObj.new Feature();
//				buildObjectFromJSONTree(feature, featureJSONObj);
//			} else {
//				JSONArray packagesJSONArray = schemeJSONObj.getJSONObject(
//						"packages").getJSONArray("feature");
//				for (int i = 0; i < packagesJSONArray.length(); i++) {
//					JSONObject featureJSONObj = packagesJSONArray
//							.getJSONObject(i);
//					PackageData.Feature feature = packageObj.new Feature();
//					buildObjectFromJSONTree(feature, featureJSONObj);
//				}
//
//			}
//		}
//		if (schemeJSONObj.has("packages")
//				&& schemeJSONObj.getJSONObject("packages").has("storage")) {
//			if (schemeJSONObj.getJSONObject("packages").get("storage") instanceof JSONObject) {
//				JSONObject storageJSONObj = schemeJSONObj.getJSONObject(
//						"packages").getJSONObject("storage");
//				PackageData.Storage storage = packageObj.new Storage();
//				buildObjectFromJSONTree(storage, storageJSONObj);
//			} else {
//				JSONArray packagesJSONArray = schemeJSONObj.getJSONObject(
//						"packages").getJSONArray("storage");
//				for (int i = 0; i < packagesJSONArray.length(); i++) {
//					JSONObject storageJSONObj = packagesJSONArray
//							.getJSONObject(i);
//					PackageData.Storage storage = packageObj.new Storage();
//					buildObjectFromJSONTree(storage, storageJSONObj);
//				}
//
//			}
//		}
//		if (schemeJSONObj.has("packages")
//				&& schemeJSONObj.getJSONObject("packages")
//						.has("packageDetails")) {
//			JSONObject packageDetailsJSONObj = schemeJSONObj.getJSONObject(
//					"packages").getJSONObject("packageDetails");
//			buildObjectFromJSONTree(packageObj, packageDetailsJSONObj);
//		}
//		return packageObj;
//	}
//	public ArrayList<PackageData> buildArrayResponse(String json) throws Exception {
//		JSONObject packagesJSONObj = getServiceJSONObject("schemes", json);
//		ArrayList<PackageData> packageList = new ArrayList<PackageData>();
//
//		if (packagesJSONObj.get("scheme") instanceof JSONArray) {
//			JSONArray schemeJSONArray = packagesJSONObj.getJSONArray("scheme");
//			for (int i = 0; i < schemeJSONArray.length(); i++) {
//				JSONObject schemeJSONObject = schemeJSONArray.getJSONObject(i);
//				PackageData scheme = buildPackageDataObject(schemeJSONObject);
//
//				scheme.setStrResponse(json);
//				System.out.println("Scheme ---- " + scheme);
//				scheme.setResponseSuccess(isResponseSuccess(json));
//
//				packageList.add(scheme);
//			}
//
//		} else {
//			JSONObject schemeJSONObject = packagesJSONObj.getJSONObject("scheme");
//			PackageData scheme = buildPackageDataObject(schemeJSONObject);
//			scheme.setStrResponse(json);
//			scheme.setResponseSuccess(isResponseSuccess(json));
//			packageList.add(scheme);
//
//		}
//		return packageList;
//	}

	}

?>
