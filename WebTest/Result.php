<?php
require_once(__DIR__.'/Runner.php');
class WebTest_Result{

	protected $_testId;
	protected $_result;

	public function __construct($testId){
		$this->_testId = $testId;
	}

	public static function gatherResults(){
		$db = new WebTest_Database_Connection();
		$runs = $db->query('SELECT * FROM Runs WHERE ResultsGathered = 0');
		foreach($runs->fetchAllRows() as $runRow){
			$run = new WebTest_Result($runRow['TestId']);
			$loads = $run->getAllLoads();
			if($loads){
				foreach($loads as $runNumber => $load){
					$db->query('INSERT INTO Results VALUES ("'.$runRow['TestId'].'", '.$runNumber.', "'.$run->getUrl().'",
															"'.$run->getLocation().'", "'.date('Y-m-d G:i:s',$load->date).'",
															'.$load->loadTime.')');
				}
				$db->query('UPDATE Runs SET ResultsGathered = 1 WHERE TestId = "'.$runRow['TestId'].'"');
			}
		}
	}

	protected function _fetchResult(){
		if(is_null($this->_result)){
			$result = simplexml_load_string(file_get_contents(WebTest_Runner::WEBTEST_URL.'xmlResult/'.$this->_testId.'/'));
			if($result->statusCode == '200'){
				$this->_result = $result;
			}else{
				$this->_result = false;
			}
		}
		return $this->_result;
	}

	public function getAverageLoadTime(){
		if($result = $this->_fetchResult()){
			return $result->data->average->firstView->loadTime;
		}else{
			return false;
		}
	}

	public function getAllLoads(){
		$loads = array();
		if($result = $this->_fetchResult()){
			if(isset($result->data->run)){
				foreach($result->data->run as $run){
					$loads[] = $run->firstView->results;
				}
				return $loads;
			}
		}
		return false;
	}

	public function getUrl(){
		if($result = $this->_fetchResult()){
			return $result->data->median->firstView->URL;
		}else{
			return false;
		}
	}

	public function getLocation(){
		if($result = $this->_fetchResult()){
			return $result->data->location;
		}else{
			return false;
		}
	}
	/**
	 *
	 * Get run results in CSV format
	 * Format is: URL, Location, TimeOfRun, TestID, RunNumber, LoadTime(ms)
	 */
	public function getCsvLines(){
		$returnValue = '';
		foreach($this->getAllLoads() as $runNumber => $load){
			$returnValue .= $this->getUrl().','.$this->getLocation().','
			//The recorded dates appear to be 9 hours ahead of GMT, therefore an adjustment is being made
							.date('Y-m-d G:i:s',($load->date-(9*60*60))).','
							.$this->_testId.','.$runNumber.','.$load->loadTime."\n";
		}
		return $returnValue;
	}

}