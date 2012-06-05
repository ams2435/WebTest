<?php
class WebTest_Database_Result{

	protected $_result;

	public function __construct($result){
		$this->_result = $result;
	}

	public function fetchAllRows(){
		$rows = array();
		while($row = sqlite_fetch_array($this->_result, SQLITE_ASSOC)){
			$rows[] = $row;
		}
		return $rows;
	}
}