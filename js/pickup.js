var reload_interval;
var playername = $.cookie('steamid');
var nickname = $.cookie('nickname');
var pickupstarted = 0;
var soundplayed = 0;

var readySound = new buzz.sound("/js/sounds/ready.ogg");
var startSound = new buzz.sound("/js/sounds/start.ogg");

function reload() {
	$.ajaxSetup ({ cache: false }); // Disable caching of AJAX responses

	$.getJSON("./php/status3.php?name=" + playername, function(data){
		$("#red #scout").html(checkready(data[0].ready, data[0].nickname, data[0].name));
		$("#red #soldier").html(checkready(data[1].ready, data[1].nickname, data[1].name));
		$("#red #pyro").html(checkready(data[2].ready, data[2].nickname, data[2].name));
		$("#red #demoman").html(checkready(data[3].ready, data[3].nickname, data[3].name));
		$("#red #heavy").html(checkready(data[4].ready, data[4].nickname, data[4].name));
		$("#red #engineer").html(checkready(data[5].ready, data[5].nickname, data[5].name));
		$("#red #medic").html(checkready(data[6].ready, data[6].nickname, data[6].name));
		$("#red #sniper").html(checkready(data[7].ready, data[7].nickname, data[7].name));
		$("#red #spy").html(checkready(data[8].ready, data[8].nickname, data[8].name));
		$("#blue #scout").html(checkready(data[9].ready, data[9].nickname, data[9].name));
		$("#blue #soldier").html(checkready(data[10].ready, data[10].nickname, data[10].name));
		$("#blue #pyro").html(checkready(data[11].ready, data[11].nickname, data[11].name));
		$("#blue #demoman").html(checkready(data[12].ready, data[12].nickname, data[12].name));
		$("#blue #heavy").html(checkready(data[13].ready, data[13].nickname, data[13].name));
		$("#blue #engineer").html(checkready(data[14].ready, data[14].nickname, data[14].name));
		$("#blue #medic").html(checkready(data[15].ready, data[15].nickname, data[15].name));
		$("#blue #sniper").html(checkready(data[16].ready, data[16].nickname, data[16].name));
		$("#blue #spy").html(checkready(data[17].ready, data[17].nickname, data[17].name));
		$('.maplist').html(data[0].maps);


        if (data[0].nplayers == 18 && soundplayed == 0) {
			readySound.play();
			soundplayed = 1;
        } else if (data[0].nplayers !== 18) {
			soundplayed = 0;
        }

/*
		if (data[0].nplayers == 18) {
			if (soundplayed == 0) {
				readySound.play();
				soundplayed = 1;
			}
		}
*/
/*
		if ( playername == '76561197988418322' ) {
			if (data[0].nplayers == 10) {
				if (soundplayed == 0) {
					readySound.play();
					soundplayed = 1;
				}
				clearInterval(reload_interval);
				$.blockUI({
					message: $('#question'),
					css: {
						border: 'none',
						padding: '15px',
						backgroundColor: '#FBECCB',
						'border-radius': '5px',
						'-webkit-border-radius': '5px',
						'-moz-border-radius': '5px',
						color: '#3C352F'
					}
				});
			}
		}
*/

		if (data[0].nready == 18){
			if (data[0].checkadded == true){
				clearInterval(reload_interval);
				$.blockUI({
					message: "<div><img width='32px;' src='./images/icons/ajax_loader.gif'/><br/>Pickup is starting, please wait...</div>",
					css: {
						border: 'none',
						padding: '15px',
						backgroundColor: '#FBECCB',
						'border-radius': '5px',
						'-webkit-border-radius': '5px',
						'-moz-border-radius': '5px',
						color: '#3C352F'
					}
				});
				startSound.play();
				soundplayed = 0;
				pickupstarted = 1;
				setTimeout(function(){
					location.replace("./php/startpickup.php?gameurl=" + data[0].gameurl);
				}, 16000);
			}
		}
	});

//	$('#sublist').load('./php/checksubs.php?name=' + playername);

	$.getJSON("./php/checksubs.php?name=" + playername, function(data){
		$('#sublist').empty();
		for (var i = 0; i < data[0].numsubs; i++) {
			$("#sublist").append(data[i].link);
		}
	});

}

function adduser(teamname,classname,name) {
	reload();
	$.ajax({
		url: './php/adduser.php',
		data: {'name': playername,'team': teamname,'class': classname, 'nick': nickname},
//		success: function(data) { reload(); }
		success: function(data) { if (pickupstarted == 0) {reload();} }
	});
}

function removeuser(name) {
	reload();
	$.ajax({
		url: './php/removeuser.php',
		data: {'name': playername},
//		success: function(data) { reload(); }
		success: function(data) { if (pickupstarted == 0) {reload();} }
	});
}

function checkremoveuser(name) {
	if (pickupstarted == 0) {
		removeuser(name);
	}
}

function toogleready(name) {
	reload();
	$.ajax({
		url: './php/toogleready.php',
		data: {'name': name},
//		success: function(data) { reload(); }
		success: function(data) { if (pickupstarted == 0) {reload();} }
	});
}

function votemap(id) {
//	reload();
	$.ajax({
		url: './php/mapvote.php',
		data: {'name': playername, 'id': id},
		success: function(data) { reload(); }
	});
}

function checkready(ready, nickname, steamid) {
	if ( ready == '1') {
		var result = '<p>' + nickname + '</p><span id="readyimage"></span>';
		if (nickname !== '') { result = result + '<a class="class-profile" id="' + steamid + '" data-dropdown="#dropdown">Profile &#9660;</a>'; }
		return result;
	}
	else {
		var result = '<p>' + nickname + '</p>';
		if (nickname !== '') { result = result + '<a class="class-profile" id="' + steamid + '" data-dropdown="#dropdown">Profile &#9660;</a>'; }
		return result;
	}
}

function checkuser(playername) {
	$.ajax({
		url: './php/checkuser.php',
		data: {'name': playername},
		success: function(data) {
			if (data == '1') {$('#admenu').load('./php/admenuu.php');}
		}
	});
}

function addsub(playername,gameurl,team) {
	reload();
	$.ajax({
		url: './php/addsub.php',
		data: {'name': playername, 'gameurl': gameurl, 'team': team},
		success: function(data) {
			location.replace("./php/startpickup.php?gameurl=" + gameurl);
		}
	});
}

function refresh(func, freq) {
    clearInterval(reload_interval);
    reload_interval = setInterval(func, freq);
}

$('#red #scout, #blue #scout, #red #soldier, #blue #soldier, #red #pyro, #blue #pyro, #red #demoman, #blue #demoman, #red #heavy, #blue #heavy, #red #engineer, #blue #engineer, #red #medic, #blue #medic, #red #sniper, #blue #sniper, #red #spy, #blue #spy').live('click', function(){
	adduser($(this).parent().attr('id'),$(this).attr('id'),playername);
});

$('#warning-close').live('click', function(){
	$('#warning').hide();
});

$('#closesub').live('click', function(){
	$('.sub.' + $(this).attr('data')).hide();
});

/*
$('#yes').click(function() {
	
});

$('#no').click(function() {
	$.ajax({
		url: './php/removeuser.php',
		data: {'name': playername},
		success: function(data) {
			
		}
	});
	$.unblockUI();
});
*/

refresh(reload, 6000);
checkuser(playername);