<?php // find server for pickup
require __DIR__ . '/SourceQuery/SourceQuery.class.php';

include "dbinfo.php";

$rconpswd = "tf2pickup";

$gameurl = preg_replace("/[^A-Za-z0-9]/", "", $_GET['gameurl']);

if ( !isset($gameurl) ) { // check if the variable was not passed
	return false;
}
else {
	$filepath = '../logs/'.$gameurl.'.json';
	$json = json_decode(file_get_contents($filepath), true);
	
	if ($json[0]['serverip'] == '') { // if server is not yet set => try to set it
		$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); // connect to the database
		$query = $db->query("SELECT * FROM servers");
		$serversrowcount = $query->rowCount();
		$resultservers = $query->fetchAll(PDO::FETCH_ASSOC); $resultservers = array_filter($resultservers);

		for ($i = 0; $i < $serversrowcount; $i++) {
			$Timer = MicroTime(true);
			$Query = new SourceQuery();
			$Query->Connect($resultservers[$i]['ip'], $resultservers[$i]['port']);
			$Info = Array(); $Info = $Query->GetInfo();

			if ($Info['Players'] == '0') {
				$Query->SetRconPassword($rconpswd);
				$Query->Rcon("sv_password tf2pickup");
				$json[0]['serverpass'] = "tf2pickup";
				sleep(5);
//				$Query->SetRconPassword($rconpswd);
				$Query->Rcon("map ".$json[0]['mapwinner']);

				$json[0]['serverip'] = $resultservers[$i]['ip'];
				$json[0]['serverport'] = $resultservers[$i]['port'];
				$json[0]['serverid'] = $i + 1; // server id = mumble channel number
				$i = $serversrowcount; // exit basically
			}
			$Query->Disconnect();
		}

/*
		// password bug fix
		$fp = fopen($filepath, 'w');
		fwrite($fp, json_encode($json));
		fclose($fp);
		sleep(1);
		$testjson = json_decode(file_get_contents($filepath), true);
		if ($json[0]['serverpass'] != $testjson[0]['serverpass']) {
			$json[0]['serverpass'] == $testjson[0]['serverpass'];
		}
*/

		// save json file
		$jsontest = json_decode(file_get_contents($filepath), true);
		if ($jsontest[0]['serverip'] == '') {
			$fp = fopen($filepath, 'w');
			fwrite($fp, json_encode($json));
			fclose($fp);
		}

	}

// get info now
	header('Content-Type: application/json');
	$result = file_get_contents($filepath);
	echo $result;
}

?>