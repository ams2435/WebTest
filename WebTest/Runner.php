<?php
require_once(__DIR__.'/Database/Connection.php');

class WebTest_Runner{

	protected $_urls;
	protected $_runsPerLocation = 5;
	protected $_speedProfile = 'DSL';
	protected $_apiKey;
	protected $_locations = array();

	protected $_errors = array();

	const WEBTEST_URL = 'http://www.webpagetest.org/';

	public function __construct(Array $urls, $apiKey){
		$this->_urls = $urls;
		$this->_apiKey = $apiKey;
	}

	public function setRunsPerLocation($runs){
		$this->_runsPerLocation = intval($runs);
	}

	public function getRunsPerLocation(){
		return $this->_runsPerLocation;
	}

	public function addLocation($location){
		$this->_locations[] = $location;
	}

	public function getLocations(){
		return $this->_locations;
	}

	public function setSpeedProfile($profile){
		$this->_speedProfile = $profile;
	}

	public function getSpeedProfile(){
		return $this->_speedProfile;
	}

	public function getErrors(){
		return $this->_errors;
	}

	public function run(){
		$db = new WebTest_Database_Connection();
		$errorFound = false;
		foreach($this->_locations as $location){
			foreach($this->_urls as $url){
				try{
					$testId =  $this->_runLocation($url, $location);
					$db->query('INSERT INTO Runs VALUES ("'.$testId.'", "'.$url.'", "'.$location.'", "'.date('Y-m-d G:i:s').'",0)');
				}catch(Exception $e){
					$this->_errors[] = $e->getMessage();
					$errorFound = true;
				}
			}
		}
		return !$errorFound;
	}

	protected function _runLocation($url, $location){
		$result = simplexml_load_string(file_get_contents(self::WEBTEST_URL.'runtest.php?f=xml&fvonly=1'.
													    '&runs='.$this->getRunsPerLocation()
													   .'&location='.$location.'.'.$this->getSpeedProfile()
													   .'&k='.$this->_apiKey
													   .'&url='.$url));
		if($result->statusCode == '200'){
			return $resultArray->data->testId;
		}else{
			throw new Exception($location. ' - ' . $result->statusText);
		}

	}
}