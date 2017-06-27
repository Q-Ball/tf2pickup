<?php

include "./php/apikey.php";
include "./php/openid.php";

$website_url = "hl.lethal-zone.eu";
$OpenID = new LightOpenID($website_url);

session_start();

if(!$OpenID->mode){
	if(isset($_GET['login'])){
		$OpenID->identity = "http://steamcommunity.com/openid";
		header("Location: {$OpenID->authUrl()}");
	}

	if(!isset($_SESSION['T2SteamAuth'])){
		$login = "<div id=\"login\">Please login in order to play a pickup<br><a href=\"?login\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_noborder.png\"/></a></div>";
	}

} elseif($OpenID->mode == "cancel"){
	echo "User has canceled Authenticiation.";
} else {
	if(!isset($_SESSION['T2SteamAuth'])){
		$_SESSION['T2SteamAuth'] = $OpenID->validate() ? $OpenID->identity : null;
		$_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION['T2SteamAuth']);
		if($_SESSION['T2SteamAuth'] !== null){
			$Steam64 = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION['T2SteamAuth']);
			$profile = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$api}&steamids={$Steam64}");
			$buffer = fopen("cache/{$Steam64}.json", "w+");
			fwrite($buffer, $profile);
			fclose($buffer);
		}
		header("Location: index.php");
	}
}

$steam = json_decode(file_get_contents("cache/{$_SESSION['T2SteamID64']}.json"));
$_SESSION['playersteamid64'] = $playersteamid = $steam->response->players[0]->steamid;
$playernickname = $steam->response->players[0]->personaname;
$profileurl = $steam->response->players[0]->profileurl;
$avatar = "<img src=\"{$steam->response->players[0]->avatarmedium}\"/>";

//echo $_SESSION['playersteamid64'];

if(isset($_SESSION['T2SteamAuth'])){
	$login  = "<div id=\"login\">
					<div id=\"profile\">
						<div id=\"profile-left\">
							$avatar<span style='color:#FBECCB;position:absolute;font-size:20px;margin-left:10px;margin-top:20px;'>Welcome, $playernickname!</span>
						</div>
						<div id=\"profile-right\">
							<div class=\"button2\" id=\"logout\"><a href=\"?logout\">LOGOUT</a></div>
							<div class=\"button2\" id=\"myprofile\" data-reveal-id=\"myModal\">PROFILE</div>
						</div>
					</div>
				</div>";
}

if(isset($_GET['logout'])){
	unset($_SESSION['T2SteamAuth']);
	unset($_SESSION['T2SteamID64']);
	header("Location: index.php");
}

echo "
<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<link rel='shortcut icon' href='./images/icons/favicon.ico'>	
	<link rel='stylesheet' type='text/css' href='css/style.css' />
	<link rel='stylesheet' type='text/css' href='css/reveal.css' />
	<link rel='stylesheet' type='text/css' href='css/jquery.dropdown.css' />
	<link rel='stylesheet' type='text/css' href='./phpfreechat-2.1.0/client/themes/phpfreechat/jquery.phpfreechat.css' />
<!--	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js'></script> -->
	<script type='text/javascript' src='js/jquery-1.8.2.min.js'></script>
	<script type='text/javascript' src='js/buzz.min.js'></script>
	<script type='text/javascript' src='js/jquery.dropdown.js'></script>
	<script type='text/javascript' src='js/jquery.cookie.js'></script>
	<script type='text/javascript' src='js/jquery.reveal.js'></script>
	<script type='text/javascript' src='js/jquery.blockUI.js'></script>
	<script type='text/javascript' src='js/jquery.timers.js'></script>
	<script type='text/javascript'>
		$.cookie('steamid', '$playersteamid', { path: '/' });
		$.cookie('nickname', '$playernickname', { path: '/' });
	</script>
	<title>TF2 Highlander Pickup</title>
</head>
<body onload='reload()' onunload='checkremoveuser(\"$playersteamid\")'>
	<div class='container'>
		<h1 id='logo'>TF2 Highlander Pickup</h1>
		$login
";

///////////////////////////////////////////////////////////////
//                   Checking for ban here                   //
///////////////////////////////////////////////////////////////
include "php/dbinfo.php";
$name = $playersteamid;
$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); // connect to the database
$query = $db->query("SELECT * FROM banlist"); 
$resultall = $query->fetchAll(PDO::FETCH_ASSOC);
if (empty($name)) { $name = '0'; } // fix for empty user value
function isadded($array, $key, $val) {
    foreach ($array as $item)
        if (isset($item[$key]) && $item[$key] == $val)
            return true;
    return false;
}
$added = isadded($resultall, 'steamid', $name);

if ($playersteamid) {
	if ($added == '1') {
		echo "<div>You were banned.<br>If you think it's a mistake, please add one of our admins.</div>";
	} else {
		echo "
		<div id='pickupcontent'>

			<div id='warning'><a id='warning-close'>&#215;</a><br>
				- Please, remove yourself from the pickup before you close webpage/browser<br>
				- Before playing pickup, please make sure that you have <a target='_blank' href='http://www.mumble.com/mumble-download.php'><strong>Mumble 1.2.4</strong></a> client installed<br>
				- Join our steamgroup <a target='_blank' href='http://steamcommunity.com/groups/tf2highlanderpug'>here</a> for more information about upcoming updates, etc
			</div>
			
			<div id='sublist'></div>

			<div id='teams-wrapper'>
				<div id='blue'><div id='title-blue'>Team BLUE</div>
					<div class='button1' id='scout'><p></p></div>
					<div class='button1' id='soldier'><p></p></div>
					<div class='button1' id='pyro'><p></p></div>
					<div class='button1' id='demoman'><p></p></div>
					<div class='button1' id='heavy'><p></p></div>
					<div class='button1' id='engineer'><p></p></div>
					<div class='button1' id='medic'><p></p></div>
					<div class='button1' id='sniper'><p></p></div>
					<div class='button1' id='spy'><p></p></div>
				</div>
				<div id='red'><div id='title-red'>Team RED</div>
					<div class='button1' id='scout'><p></p></div>
					<div class='button1' id='soldier'><p></p></div>
					<div class='button1' id='pyro'><p></p></div>
					<div class='button1' id='demoman'><p></p></div>
					<div class='button1' id='heavy'><p></p></div>
					<div class='button1' id='engineer'><p></p></div>
					<div class='button1' id='medic'><p></p></div>
					<div class='button1' id='sniper'><p></p></div>
					<div class='button1' id='spy'><p></p></div>
				</div>
				<div id='teams-menu'>
					<div class='button' id='remove' onclick='removeuser(\"$playersteamid\")'>REMOVE</div>
					<div class='button' id='mapvote' data-reveal-id='myModal'>MAP VOTE</div>
					<div class='button' id='toogleready' onclick='toogleready(\"$playersteamid\")'>READY/NOT READY</div>
					<div id='mapstatus'>
						<p class='plist'>Map votes:</p>
						<p class='maplist'>No votes yet</p>
					</div>
				</div>
			</div>

			<div id='admenu'></div>

			<h2><a name='chat' class='bookmark'>Chat</a></h2>
			<div id='tf2chat'>
				<div id='tf2chatloader'>
					<a style='text-align:center;text-decoration:none;' href='http://www.phpfreechat.net'>LOADING CHAT STAY PUT...</a>
				</div>
			</div>

			<script type='text/javascript'>
				$(function(){
					$('.button').hover(
						function(){
							$(this).addClass('button-hover');
							$(this).removeClass('button');
						},
						function(){
							$(this).removeClass('button-hover');
							$(this).addClass('button');
						}
					)
					$('.button2').hover(
						function(){
							$(this).addClass('button2-hover');
							$(this).removeClass('button2');
						},
						function(){
							$(this).removeClass('button2-hover');
							$(this).addClass('button2');
						}
					)
				});
			</script>

			<div id='myModal' class='reveal-modal'>
				<p><center>Loading content. Please wait.</center></p>
				<a id='close' class='close-reveal-modal'>&#215;</a>
			</div>

			<div id='dropdown' class='dropdown dropdown-tip'>
				<ul class='dropdown-menu'>
				</ul>
			</div>
			
			<div id='question' style='display:none; cursor: default'> 
				<strong>Press Ready button if you are ready to play. Otherwise press Remove or don't press any buttons for <span class='timer'>15</span> seconds.</strong><br>
				<input type='button' id='yes' value='Ready' /> 
				<input type='button' id='no' value='Remove' /> 
			</div>

		</div>
		<script type='text/javascript' src='js/pickup.js'></script>
		<script type='text/javascript' src='./phpfreechat-2.1.0/client/jquery.phpfreechat.js'></script>
		<script type='text/javascript'>
			$('#tf2chat').phpfreechat({ serverUrl: './phpfreechat-2.1.0/server' });
		</script>
		";
	}
}

echo "
	</div>
	<div class='footer'>
		<p>
			Team Fortress, the Team Fortress logo, Steam, the Steam logo are trademarks and/or registered trademarks of Valve Corporation.<br>
			Powered by <a href='http://steampowered.com'>Steam</a>. Created by <a href='http://steamcommunity.com/profiles/76561197988418322'>Q-Ball</a>
		</p>
	</div>
</body>
</html>
";
?>