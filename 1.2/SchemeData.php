<?php

namespace com\shephertz\app42\paas\sdk\php\appTab;

use com\shephertz\app42\paas\sdk\php\App42Response;

include_once "App42Response.php";

class SchemeData extends App42Response {

   public $name;
    public $description;
  
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }
     public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
 
}
?>
