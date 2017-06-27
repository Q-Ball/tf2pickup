<?php // ban menu

include "dbinfo.php";

$name = preg_replace("/[^0-9]/", "", $_GET['name']);

$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); // connect to the database
$query = $db->query("SELECT * FROM admins"); 
$resultall = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($name)) { $name = '0'; } // fix for empty user value
function isadded($array, $key, $val) {
    foreach ($array as $item)
        if (isset($item[$key]) && $item[$key] == $val)
            return true;
    return false;
}
$added = isadded($resultall, 'steamid64', $name);

if ($added == '1') {
	echo "
	<script type='text/javascript'>
		function banuser() {
			var banname = document.getElementById('banplayername').value;
			var banreason = document.getElementById('banplayerreason').value;
			
			$.ajax({
				url: './php/banuser.php',
				data: {'name': banname, 'reason': banreason, 'adname': playername}
			});
			
			alert('You just banned player ' +banname+ ' for the following reason: ' +banreason);

		}
	</script>

	<div>
	<table>
	<tr>
		<td><strong>Player steamid64: </strong></td>
		<td><input id='banplayername'></input></td>
	</tr>
	<tr>
		<td><strong>Reason: </strong></td>
		<td><input id='banplayerreason'></input></td>
	</tr>
	</table>
	</div>

	<div style='margin-top:20px;'>
		<a class='button-inv' id='banbuttonclose' onclick='banuser()'>Ok</a>
		<a class='button-inv' id='banbuttonclose'>Cancel</a>
	</div>
	";
} else {
	echo "Are you sure you can do that?";
}

?>