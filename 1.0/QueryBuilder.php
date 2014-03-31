<?php

namespace com\shephertz\app42\paas\sdk\php\storage;

use com\shephertz\app42\paas\sdk\php\util\Util;
use com\shephertz\app42\paas\sdk\php\App42Exception;
use com\shephertz\app42\paas\sdk\php\JSONObject;
use com\shephertz\app42\paas\sdk\php\storage\Query;

include_once "JSONObject.php";
include_once "Query.php";
include_once 'App42Exception.php';
include_once 'Util.php';

class Operator {
    const EQUALS = "\$eq";
    const NOT_EQUALS = "\$ne";
    const GREATER_THAN = "\$gt";
    const LESS_THAN = "\$lt";
    const GREATER_THAN_EQUALTO = "\$gte";
    const LESS_THAN_EQUALTO = "\$lte";
    const LIKE = "\$lk";
    const ANDop = "\$and";
    const ORop = "\$or";

    public function enum($string) {
        return constant('com\shephertz\app42\paas\sdk\php\storage\Operator::' . $string);
    }

    public function isAvailable($string) {
        if ($string == "\$eq")
            return "\$eq";
        else if ($string == "\$ne")
            return "\$ne";
        else if ($string == "\$gt")
            return "\$gt";
        else if ($string == "\$lt")
            return "\$lt";
        else if ($string == "\$gte")
            return "\$gte";
        else if ($string == "\$lte")
            return "\$lte";
        else if ($string == "\$lk")
            return "\$lk";
        else if ($string == "\$and")
            return "\$and";
        else if ($string == "\$or")
            return "\$or";
        else
            return "null";
    }

}

class QueryBuilder {

    public function build($key, $value, $op) {
        Util::throwExceptionIfNullOrBlank($key, "Key");
      //Util::throwExceptionIfNullOrBlank($value, "Value");
        Util::throwExceptionIfNullOrBlank($op, "Operator");
        try {
            $operatorObj = new Operator();
            if ($operatorObj->isAvailable($op) == "null") {
                throw new App42Exception("The Operator with type '$op' does not Exist ");
            }

            $jsonObj = new JSONObject();
            $jsonObj->value = $value;
            $jsonObj->operator = $op;
            $jsonObj->key = $key;
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $jsonObj;
    }

    public function compoundOperator($q1, $op, $q2) {
        Util::throwExceptionIfNullOrBlank($q1, "Query");
        Util::throwExceptionIfNullOrBlank($q2, "Query");
        Util::throwExceptionIfNullOrBlank($op, "Operator");
        try {
            $operatorObj = new Operator();
            if ($operatorObj->isAvailable($op) == "null") {
                throw new App42Exception("The Operator with type '$op' does not Exist ");
            }

            $array = array();
            if ($q1 instanceof JSONObject) {
                array_push($array, $q1);
            } else {
                array_push($array, $q1);
            }
            $jsonObj1 = new JSONObject();
            $jsonObj1->compoundOpt = $op;
            array_push($array, $jsonObj1);
            if ($q2 instanceof JSONObject) {
                array_push($array, $q2);
            } else {
                array_push($array, $q2);
            }
            $query = new Query();
            $buildQuery = $query->Query($array);
        } catch (App42Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw new App42Exception($e);
        }
        return $buildQuery;
    }

}
?>
