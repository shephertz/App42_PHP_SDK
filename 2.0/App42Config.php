<?php
//include_once 'App42Log.php';
//define("HOST","localhost");
//define("PORT","8082");
//define("Connection","REST");
//define("CONTENT_TYPE","application/json");
//define("ACCEPT","application/json");
class App42Config {

	private static $config;
	private $protocol = "http://";
	private $baseURL;
	private $serverName = "/App42_API_SERVER/cloud/";
	private $contentType = "application/json";
	private $accept = "application/json";
	private $confProps;
	private $customCodeURL = null;

        public function __construct() {
                $this->baseURL = "https://api.shephertz.com/cloud/";
		$this->customCodeURL = "https://customcode.shephertz.com/";
		App42Log::debug("Configuration Properties " + $this->baseURL);
    }


	public static function getInstance() {
		if (self::$config == null) {
			try {
				self::$config = new App42Config();
			} catch (ConfigurationException $e) {
				throw new RuntimeException(" Instance Cannot be created due to wrong config.");
			}
		}
                return self::$config;
	}

	public function  setProperties($confProps) {
              $this->confProps = $confProps;
	}

	public function getProperties() {
            return $this->confProps;
	}

	public function  setProtocol($protocol) {
            $this->protocol = $protocol;
	}

	public function getProtocol() {
             return $this->protocol;
	}

	public function  setServerName($serverName) {
             $this->serverName = $serverName;
	}

	public function getServerName() {
             return $this->serverName;
	}

	public function setBaseURL($protocol, $host, $port) {
             $this->baseURL = $protocol. $host . ":" .$port.$this->serverName;
        }

	public function getBaseURL() {
             return $this->baseURL;
	}

	public function setContentType($contentType) {
              $this->contentType = $contentType;
	}

	public function   getContentType() {
             return $this->contentType;
	}

	public function  setAccept($accept) {
               $this->accept = $accept;
	}

	public function getAccept() {
              return $this->accept;
	}

	public function getCustomCodeURL() {
             return $this->customCodeURL;
	}

	public function setCustomCodeURL($customCodeURL) {
              $this->customCodeURL = $customCodeURL;
	}

}
?>