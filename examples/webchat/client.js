var sock, $out, $input, $clientcount, $clientlist, clients = [];

$(function() {
	$input = $("#input");
	$out = $('#out');
	$clientcount = $('#client-count');
	$clientlist = $('#client-list');
	$input.bind('keydown', checkForSubmit);
	initSock();
});

function checkForSubmit(e) {
	var pressed = e.which || e.keyCode;
	if(pressed == 13) {
		sock.send(JSON.stringify({'message': $input.val()}));
		$input.val('');
	}
}

function out(sender, message) {
		$out.append('['+sender+'] '+message);
		$out.scrollTop($out[0].scrollHeight);
}

function client(data) {
	if(data.client && data.message) {
		out('client '+data.client.substr(0, 4)+'...', data.message.replace(/\n/, '<br />'));
	}
}

function info(message) {
	out('info', message+'<br />');
}

function updateClientList() {
	$clientlist.html('');
	var count = 0;
	for(i in clients) {
		$clientlist.append('client '+clients[i].id+'<br />');
		count++;
	}
	$clientcount.html(count+' '+(count === 1 ? 'client':'clients'));
}

function initSock(fn) {

	info('opening connection');
	sock = new WebSocket("ws://127.0.0.1:9000");
	
	sock.onopen = function() {
		input.focus();
		info('connection established');
	};
	
	sock.onmessage = function(m) {
		console.log('incoming: '+m.data);
		var recvd = (eval('('+m.data+')'));
		switch(recvd.action) {
			case 'client':
				client(recvd);
				break;
			case 'clientAdded':
				clients[recvd.client.id] = recvd.client;
				updateClientList();
				break;
			case 'clientRemoved':
				delete clients[recvd.client.id];
				updateClientList();
				break;
			case 'clientList':
				clients = recvd.clients;
				updateClientList();
				break;
			default:
				console.log('[ignored] '+m.data);
		}
	};
	
	sock.onclose = function() {
		info('connection closed');
	};
}
