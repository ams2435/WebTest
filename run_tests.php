<?php

date_default_timezone_set('GMT');

include_once(__DIR__.'/WebTest/Runner.php');

$apiKey = 'YOUR-API-KEY-HERE';

$runner = new WebTest_Runner(
			array('http://test-url-1.com',
				  'http://test-url-2.com'),
			$apiKey);

$runner->addLocation('Korea');
$runner->addLocation('China');
$runner->addLocation('Australia');

$runner->run();