<?php // remove user/player from the database/pickupsession_start();include "dbinfo.php";$name = preg_replace("/[^0-9]/", "", $_GET['name']);if ( !isset($name) ) { // check if the variables were not passed	return false;}else {	if ($name = $_SESSION['playersteamid64']) {		$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);		$query = $db->query("SELECT * FROM currentplayers WHERE name='$name'");		$result = $query->fetchAll(PDO::FETCH_ASSOC); $result = array_filter($result);		echo $result[0]['ready'];	}}?>