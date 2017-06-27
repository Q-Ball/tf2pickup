<?php // generate profile for player

$steamid64 = preg_replace('/[^0-9]/', '', $_GET['id']);

$authserver = bcsub($steamid64, 'STEAMID64') & 1;
$authid = (bcsub($steamid64, 'STEAMID64')-$authserver)/2;
$steamid = "STEAM_0:$authserver:$authid";

echo "
<li><a href='http://steamcommunity.com/profiles/$steamid64' target='_blank'>Steam Profile</a></li>
<li><a href='http://www.ugcleague.com/players_page.cfm?player_id=$steamid64' target='_blank'>UGC Profile</a></li>
<li><a href='http://etf2l.org/search/?q=$steamid' target='_blank'>ETF2L Profile</a></li>
<li class='dropdown-divider'></li>
<li><a href='http://logs.tf/profile/$steamid64' target='_blank'>logs.tf</a></li>
";

?>