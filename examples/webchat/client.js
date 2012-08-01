var sock;

$(function() {
	var input = $("#input");
	input.bind('keydown', function(e) {
		var pressed = e.which || e.keyCode;
		if(pressed == 13) {
			sock.send(input.val());
			input.val('');
			out('\n');
		}
	});
	initSock(function() {
		input.focus();
	});
});

function out(data) {
	if(data.client && data.message) {
		$('#out').append('[client '+data.client+'] '+data.message.replace(/\n/, '<br />'));
		scrollConsole();
	}
}

function scrollConsole() {
	var o = $('#out');
	o.scrollTop(o[0].scrollHeight);
}

function initSock(fn) {
	console.log('initSock');
	sock = new WebSocket("ws://127.0.0.1:9000");
	
	sock.onopen = function() {
		console.log('connection open');
		fn();
	};
	
	sock.onmessage = function(m) {
		console.log('message received');
		out(eval('('+m.data+')'));
	};
	
	sock.onclose = function() {
		console.log('connection closed');
		out('Connection closed.');
	};
}

function send(json) {
	console.log('sending: '+s);
	sock.send(s);
}
