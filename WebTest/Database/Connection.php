<?php
require_once __DIR__.'/Result.php';
class WebTest_Database_Connection{

	protected $_dbHandle;
	protected $_lastError;

	public function __construct($dbHandler = null){
		if(is_null($dbHandler)){
			$dbHandler = sqlite_open(dirname(dirname(__DIR__)).'/db/webtest.db', 0666);
		}
		$this->_dbHandle = $dbHandler;
	}

	public function query($query){
		return new WebTest_Database_Result(sqlite_query($this->_dbHandle, $query, $this->_lastError));
	}

	public function getLastError(){
		return $this->_lastError;
	}

}