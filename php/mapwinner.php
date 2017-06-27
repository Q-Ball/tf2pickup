<?php // show which map is a winner

include "dbinfo.php";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$query = $db->query("SELECT * FROM currentplayers");
$result = $query->fetchAll(PDO::FETCH_ASSOC); $result = array_filter($result);

// creating new array of maps, that people've voted for
$maparray = array();
for ($i = 0; $i <= 17; $i++) {
	if ($result[$i]['map'] != null) {
		array_push($maparray, $result[$i]['map']);
	}
}

$votecount = array_count_values($maparray); // get array of votes for each map
$winner = array_search(max($votecount), $votecount); // check who get the highest number of votes

$query1 = $db->query("SELECT * FROM maps WHERE id='$winner'"); // search for the map name by id
$result1 = $query1->fetchAll(PDO::FETCH_ASSOC); $result1 = array_filter($result1);
echo $result1[0]['name'];

?>