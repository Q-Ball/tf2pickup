<?php // start pickup

include "dbinfo.php";

$gameurl = preg_replace("/[^A-Za-z0-9]/", "", $_GET['gameurl']);

if ( !isset($gameurl) ) { // check if the variable was not passed
	return false;
}
else {
	$filepath = '../logs/'.$gameurl.'.json';

	$exists = file_exists($filepath);
	if (!$exists) { // if the file is not yet created
		$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); // connect to the database
		$query = $db->query("SELECT * FROM currentplayers"); 
		$resultall = $query->fetchAll(PDO::FETCH_ASSOC);

		for ($i = 0; $i < 18; $i++) {
			$checkgameurl = $checkgameurl + $resultall[$i]['name'] + $resultall[$i]['map'];
		}
		$checkgameurl = md5($checkgameurl);

		if ($gameurl == $checkgameurl) { // create new array of maps, that people've voted for
			$maparray = array();
			for ($i = 0; $i < 18; $i++) {
				if ($resultall[$i]['map'] != null) {
					array_push($maparray, $resultall[$i]['map']);
				}
			}

			$votecount = array_count_values($maparray); // get array of votes for each map
			$winner = array_search(max($votecount), $votecount); // check who get the highest number of votes

			$query = $db->query("SELECT * FROM maps WHERE id='$winner'"); // search for the map name by id
			$resultwinner = $query->fetchAll(PDO::FETCH_ASSOC); $resultwinner = array_filter($resultwinner);
			if (!isset($resultwinner[0]['name'])) {$resultwinner[0]['name'] = 'pl_badwater';}
			$resultall[0]['mapwinner'] = $resultwinner[0]['name'];

			// server info
			$resultall[0]['serverpass'] = '';
			$resultall[0]['serverip'] = '';
			$resultall[0]['serverport'] = '';

			// save json file and check again if file exists
			if (!$exists) {
				$fp = fopen($filepath, 'w');
				fwrite($fp, json_encode($resultall));
				fclose($fp);

				// remove all players from pickup
				$query = $db->query("UPDATE currentplayers SET name='', nickname='', ready='0', map=''");
			}
		} else {
			echo "Looks like the gameurl you provided is not the right one. If you got here by mistake, please contact admin";
			exit();
		}
	}

	$json = json_decode(file_get_contents($filepath), true);
	
	$mumble_host = "teamcolony.ath.cx";
	$mumble_port = "64738";
	$mumble_link = "mumble://".$mumble_host."/TF2/Highlander%20Pickups/Pickup%20channel%20".$json[0]['serverid']."?title=Root&version=1.2.0";

	echo "
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset='utf-8'>
		<link rel='shortcut icon' href='../images/icons/favicon.ico'>	
		<link rel='stylesheet' type='text/css' href='../css/style.css' />
		<link rel='stylesheet' type='text/css' href='../css/jquery.dropdown.css' />
		<style>
			h1 {margin: -30px 0px 10px;}
			.plist {margin-top:0px;}
		</style>
		<script type='text/javascript' src='../js/jquery-1.8.2.min.js'></script>
		<script type='text/javascript' src='../js/jquery.dropdown.js'></script>
		<script type='text/javascript' src='../js/jquery.cookie.js'></script>
		<script type='text/javascript' src='../js/jquery.blockUI.js'></script>
		<title>TF2 Highlander Pickup</title>
	</head>
	<body onload='reload()'>
		<div class='container'>
			<h1 id='logo'>TF2 Highlander Pickup</h1>
			<div id='content'>
				<div id='infobox'>- In case player doesn't shown up, you can request a substitute for him by clicking on the flag icon, next to player's name (At least 4 votes are required).<br><br>- Wait at least 5 minutes before reporting missing players, some people tend to have slow internet connection.<br><br>- If password for game server is not working, try reloading this page.</div>
				<div id='teams-wrapper' style='padding-left:0px;'>
					<div id='map-info'>
						<p class='plist'>MAP:</p>
						<img src='../images/maps/".$json[0]['mapwinner'].".png'/>
						<p>".$json[0]['mapwinner']."</p>
					</div>
					<div id='server-info'><p class='plist'>SERVER:</p><p>Looking for an empty server, please wait...</p></div>
					<div id='mumble-info'>
						<p class='plist'>MUMBLE:</p>
						<p>Host: ".$mumble_host."</p>
						<p>Port: ".$mumble_port."</p>
						<p>Channel: Pickup ".$json[0]['serverid']."</p>
						<a id='connecttomumble' href='".$mumble_link."'>CONNECT</a></p>
					</div>
					<div id='bluetwo'><div id='title-blue'>Team BLUE</div>
						<div id='scout'><p>&nbsp;".$json[9]['nickname']."</p><span id='spanbluescout' class='reportbutton' onclick='reportplayer(\"".$json[9]['name']."\",\"red_scout\",\"$gameurl\")'></span></div>
						<div id='soldier'><p>&nbsp;".$json[10]['nickname']."</p><span id='spanbluesoldier' class='reportbutton' onclick='reportplayer(\"".$json[10]['name']."\",\"red_soldier\",\"$gameurl\")'></span></div>
						<div id='pyro'><p>&nbsp;".$json[11]['nickname']."</p><span id='spanbluepyro' class='reportbutton' onclick='reportplayer(\"".$json[11]['name']."\",\"red_pyro\",\"$gameurl\")'></span></div>
						<div id='demoman'><p>&nbsp;".$json[12]['nickname']."</p><span id='spanbluedemoman' class='reportbutton' onclick='reportplayer(\"".$json[12]['name']."\",\"red_demoman\",\"$gameurl\")'></span></div>
						<div id='heavy'><p>&nbsp;".$json[13]['nickname']."</p><span id='spanblueheavy' class='reportbutton' onclick='reportplayer(\"".$json[13]['name']."\",\"red_heavy\",\"$gameurl\")'></span></div>
						<div id='engineer'><p>&nbsp;".$json[14]['nickname']."</p><span id='spanblueengineer' class='reportbutton' onclick='reportplayer(\"".$json[14]['name']."\",\"red_engineer\",\"$gameurl\")'></span></div>
						<div id='medic'><p>&nbsp;".$json[15]['nickname']."</p><span id='spanbluemedic' class='reportbutton' onclick='reportplayer(\"".$json[15]['name']."\",\"red_medic\",\"$gameurl\")'></span></div>
						<div id='sniper'><p>&nbsp;".$json[16]['nickname']."</p><span id='spanbluesniper' class='reportbutton' onclick='reportplayer(\"".$json[16]['name']."\",\"red_sniper\",\"$gameurl\")'></span></div>
						<div id='spy'><p>&nbsp;".$json[17]['nickname']."</p><span id='spanbluespy' class='reportbutton' onclick='reportplayer(\"".$json[17]['name']."\",\"red_spy\",\"$gameurl\")'></span></div>
					</div>
					<div id='redtwo'><div id='title-red'>Team RED</div>
						<div id='scout'><p>&nbsp;".$json[0]['nickname']."</p><span id='spanredscout' class='reportbutton' onclick='reportplayer(\"".$json[0]['name']."\",\"red_scout\",\"$gameurl\")'></span></div>
						<div id='soldier'><p>&nbsp;".$json[1]['nickname']."</p><span id='spanredsoldier' class='reportbutton' onclick='reportplayer(\"".$json[1]['name']."\",\"red_soldier\",\"$gameurl\")'></span></div>
						<div id='pyro'><p>&nbsp;".$json[2]['nickname']."</p><span id='spanredpyro' class='reportbutton' onclick='reportplayer(\"".$json[2]['name']."\",\"red_pyro\",\"$gameurl\")'></span></div>
						<div id='demoman'><p>&nbsp;".$json[3]['nickname']."</p><span id='spanreddemoman' class='reportbutton' onclick='reportplayer(\"".$json[3]['name']."\",\"red_demoman\",\"$gameurl\")'></span></div>
						<div id='heavy'><p>&nbsp;".$json[4]['nickname']."</p><span id='spanredheavy' class='reportbutton' onclick='reportplayer(\"".$json[4]['name']."\",\"red_heavy\",\"$gameurl\")'></span></div>
						<div id='engineer'><p>&nbsp;".$json[5]['nickname']."</p><span id='spanredengineer' class='reportbutton' onclick='reportplayer(\"".$json[5]['name']."\",\"red_engineer\",\"$gameurl\")'></span></div>
						<div id='medic'><p>&nbsp;".$json[6]['nickname']."</p><span id='spanredmedic' class='reportbutton' onclick='reportplayer(\"".$json[6]['name']."\",\"red_medic\",\"$gameurl\")'></span></div>
						<div id='sniper'><p>&nbsp;".$json[7]['nickname']."</p><span id='spanredsniper' class='reportbutton' onclick='reportplayer(\"".$json[7]['name']."\",\"red_sniper\",\"$gameurl\")'></span></div>
						<div id='spy'><p>&nbsp;".$json[8]['nickname']."</p><span id='spanredspy' class='reportbutton' onclick='reportplayer(\"".$json[8]['name']."\",\"red_spy\",\"$gameurl\")'></span></div>
					</div>
				</div>

				<script type='text/javascript'>
					$(function(){
						$('.reportbutton').hover(
							function(){
								$(this).addClass('reportbutton-hover');
								$(this).removeClass('reportbutton');
							},
							function(){
								$(this).removeClass('reportbutton-hover');
								$(this).addClass('reportbutton');
							}
						);
						$('.button').hover(
							function(){
								$(this).addClass('button-hover');
								$(this).removeClass('button');
							},
							function(){
								$(this).removeClass('button-hover');
								$(this).addClass('button');
							}
						);
					});
				</script>

				<div id='dropdown' class='dropdown dropdown-tip'>
					<ul class='dropdown-menu'>
					</ul>
				</div>
			</div>
		<script type='text/javascript' src='../js/server.lookup.js'></script>
		</div>
	</body>
	</html>";

}

?>