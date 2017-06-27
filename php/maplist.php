<?php // show the list of available maps

include "dbinfo.php";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
/*$query = $db->query("SELECT * FROM maps");
$result = $query->fetchAll(PDO::FETCH_ASSOC); $result = array_filter($result);
*/
$query = $db->query("SELECT * FROM maps");
$rowcount = $query->rowCount();
$result = $query->fetchAll(PDO::FETCH_ASSOC); $result = array_filter($result);

echo "<div id='maps'>Click on the map icon to vote for it:<table>";
for ($i = 0; $i < $rowcount; $i++) {
	echo "<tr><td><a id='close' href='#' onclick='votemap($i)'><img src='./images/maps/".$result[$i]['name'].".png'/></a></td><td style='padding-left:20px;'><a id='close' href='#' onclick='votemap($i)'>".$result[$i]['name']."</a></td></tr>";
}
echo "</table></div>";
echo "<a id='close' class='close-reveal-modal'>CLOSE</a>";

?>