<?php

require __DIR__.'/vendor/autoload.php';

$config = include 'config.php';

use App\Requirements;

$requirements = new Requirements();
$phpReqs = $requirements->checkPHP();
$externalReqs = $requirements->checkExternalApps();
$databaseReqs = $requirements->checkDatabase($config['db']);

echo '<h2>Statusy:</h2><ul><li>OK - wymaganie spełnione</li><li>WARNING - wymaganie nieobowiązkowe niespełnione</li><li>ERROR - obowiązkowe niespełnione</li></ul>';
echo '<h2>Wymagania dotyczące PHP:</h2>';
echo '<ol>';
foreach ($phpReqs as $key => $value) {
	echo '<li>';
	$req = $value->getData();
	echo '<h3>Test: ' . $req['name'] . '</h3>';
	if(array_key_exists('message', $req)){
		echo '<h3>Informacje dodatkowe: ' . $req['message'] . '</h3>';
	}
	echo '<h3>Rozwiązanie: <a href="' . $req['link'] . '"> ' . $req['link'] . '</a></h3>';
	if ($req['state'] == 'OK') {
		echo "<h3 style='color: green;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'WARNING') {
		echo "<h3 style='color: orange;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'ERROR') {
		echo "<h3 style='color: red;'>Status: " . $req['state'] . '</h3>';
	}
	if($key == 0) {
		echo "<h3 style='color: orange;'>Status: " . $req['state'] . '</h3>';
	}
	echo '</li>';
	echo '<hr>';
}
echo '</ol>';
echo '<h2>Wymagania dotyczące zewnętrznych rozszerzeń/aplikacji:</h2>';
echo '<ol>';
foreach ($externalReqs as $key => $value) {
	echo '<li>';
	$req = $value->getData();
	echo '<h3>Test: ' . $req['name'] . '</h3>';
	if(array_key_exists('message', $req)){
		echo '<h3>Informacje dodatkowe: ' . $req['message'] . '</h3>';
	}
	echo '<h3>Rozwiązanie: <a href="' . $req['link'] . '"> ' . $req['link'] . '</a></h3>';
	if ($req['state'] == 'OK') {
		echo "<h3 style='color: green;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'WARNING') {
		echo "<h3 style='color: orange;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'ERROR') {
		echo "<h3 style='color: red;'>Status: " . $req['state'] . '</h3>';
	}
	echo '</li>';
	echo '<hr>';
}
echo '</ol>';
echo '<h2>Wymagania dotyczące bazy danych:</h2>';
echo '<ol>';
foreach ($databaseReqs as $key => $value) {
	echo '<li>';
	$req = $value->getData();
	echo '<h3>Test: ' . $req['name'] . '</h3>';
	if(array_key_exists('message', $req)){
		echo '<h3>Informacje dodatkowe: ' . $req['message'] . '</h3>';
	}
	echo '<h3>Rozwiązanie: <a href="' . $req['link'] . '"> ' . $req['link'] . '</a></h3>';
	if ($req['state'] == 'OK') {
		echo "<h3 style='color: green;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'WARNING') {
		echo "<h3 style='color: orange;'>Status: " . $req['state'] . '</h3>';
	}
	if ($req['state'] == 'ERROR') {
		echo "<h3 style='color: red;'>Status: " . $req['state'] . '</h3>';
	}
	echo '</li>';
	echo '<hr>';
}
echo '</ol>';


