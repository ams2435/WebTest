<?php
require_once(__DIR__.'/WebTest/Database/Connection.php');

echo 'SQL Lite Version: '. sqlite_libversion();
echo "<br>";
echo 'PHP Version: '. phpversion();

$db = new WebTest_Database_Connection();
$result = $db->query('CREATE TABLE Runs(TestId text,
	 							  URL text,
	 							  Location text,
	 							  TimeOfRun datetime,
	 							  ResultsGathered boolean)');
if (!$result){
   echo '<br /><br />
   		 Cannot create table, '.$db->getLastError();
}else{
	echo '<br /><br />
		  Runs Table Created';
}


$result = $db->query('CREATE TABLE Results(TestId text,
	 							  RunNumber integer,
	 							  URL text,
	 							  Location text,
	 							  TimeOfRun datetime,
	 							  LoadTime integer)');
if (!$result){
   echo '<br /><br />
   		 Cannot create table, '.$db->getLastError();
}else{
	echo '<br /><br />
		  Results Table Created';
}
