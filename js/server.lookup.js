var reload_interval;

function getUrlVars() {
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}

function reload() {
	$.ajaxSetup ({ cache: false }); // Disable caching of AJAX responses

	var gameurl = getUrlVars()['gameurl'];

	$.getJSON("../php/findserver.php?gameurl=" + gameurl, function(data){
		if (data[0].serverip !== '' && data[0].serverport !== '') {
			clearInterval(reload_interval);
			$('#server-info').html("<p class='plist'>SERVER:</p><p>Host: " +data[0].serverip+ "</p><p>Port: " +data[0].serverport+ "</p><p>Password: " +data[0].serverpass+ "</p><a id='connecttogame' href='steam://connect/" +data[0].serverip+":"+data[0].serverport+"/"+data[0].serverpass+ "'>CONNECT</a></p>");
		}
		else {
			$('#server-info').html("<p class='plist'>SERVER:</p><p>There are no available servers at the moment, please wait...</p>");
		}
	});

}


function refresh(func, freq) {
    clearInterval(reload_interval);
    reload_interval = setInterval(func, freq);
}

function reportplayer(playername,team,gameurl) {
	$.ajax({
		url: '../php/reportplayer.php',
		data: {'name': playername, 'team': team, 'gameurl': gameurl},
		success: function(data) {reload();}
	});
}

$('#spanredscout, #spanredsoldier, #spanredpyro, #spanreddemoman, #spanredheavy, #spanredengineer, #spanredmedic, #spanredsniper, #spanredspy, #spanbluescout, #spanbluesoldier, #spanbluepyro, #spanbluedemoman, #spanblueheavy, #spanblueengineer, #spanbluemedic, #spanbluesniper, #spanbluespy').live('click', function(){
	$(this).remove();
});

refresh(reload, 10000);