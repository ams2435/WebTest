<?php
require_once(__DIR__.'/WebTest/Database/Connection.php');

$db = new WebTest_Database_Connection();

print "<h1>Current Results</h1>\n";
print "<div style='float:left; margin:15px;'>";
print "\n".'<h3>Grouped by location of client:</h3>'."\n";
$result = $db->query('SELECT URL, Location, COUNT(LoadTime) as hits, AVG(LoadTime) as meanTime,
							 MAX(LoadTime) as maxTime, MIN(LoadTime) as minTime
					  FROM Results
					  GROUP BY URL, Location
					  ORDER BY Location, meanTime');

$location = '';
foreach($result->fetchAllRows() as $row){
	if($location != $row['Location']){
		print '</table><br /><br />'.$row['Location'].'<br />';
		print '<table border="1" cellpadding="3"><tr><th>Site</th><th>Hits</th><th>Max Time</th><th>Min Time</th><th>Mean Time</th></tr>';
		$location = $row['Location'];
	}
	print '<tr><td>' . $row['URL'] . '</td><td>'  . $row['hits'] . '</td><td>' . $row['maxTime'] . 'ms'. '</td><td>' . $row['minTime'] . 'ms'. '</td><td>' . round($row['meanTime'], 2) . 'ms'."</td></tr>\n";
}

print '</table></div>';

print "<div style='float:left; margin:15px;'><h3>Grouped by location of server:</h3>\n";
$result = $db->query('SELECT URL, Location, COUNT(LoadTime) as hits, AVG(LoadTime) as meanTime,
							 MAX(LoadTime) as maxTime, MIN(LoadTime) as minTime
					  FROM Results
					  GROUP BY Location, URL
					  ORDER BY URL, meanTime');

$url = '';
foreach($result->fetchAllRows() as $row){
	if($url != $row['URL']){
		print '</table><br /><br />'.$row['URL'].'<br />';
		print '<table border="1" cellpadding="3"><tr><th>Location</th><th>Hits</th><th>Max Time</th><th>Min Time</th><th>Mean Time</th></tr>';
		$url = $row['URL'];
	}
	print '<tr><td>' . $row['Location'] . '</td><td>'  . $row['hits'] . '</td><td>' . $row['maxTime'] . 'ms'. '</td><td>' . $row['minTime'] . 'ms'. '</td><td>' . round($row['meanTime'], 2) . 'ms'."</td></tr>\n";
}

print '</table></div>';



$result = $db->query('SELECT * FROM Runs WHERE ResultsGathered = 0');

print '<div style="float:left; margin:15px;"><h3>Tests without results gathered:</h3>';
foreach($result->fetchAllRows() as $run){
	print $run['TestId'].' in '.$run['Location'].' at '.$run['TimeOfRun']."<br />\n";
}
print "</div>";
