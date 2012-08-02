var sock, $out, $input;

$(function() {
	$input = $("#input");
	$out = $('#out');
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

function out(data) {
	if(data.client && data.message) {
		$out.append('[client '+data.client.substr(0, 4)+'...] '+data.message.replace(/\n/, '<br />'));
		$out.scrollTop($out[0].scrollHeight);
	}
}

function initSock(fn) {
	sock = new WebSocket("ws://127.0.0.1:9000");
	
	sock.onopen = function() {
		input.focus();
	};
	
	sock.onmessage = function(m) {
		out(eval('('+m.data+')'));
	};
	
	sock.onclose = function() {
		out('Connection closed.');
	};
}
