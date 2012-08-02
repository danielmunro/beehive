#beehive#

Beehive is an event-driven socket server powered by libevent and implemented in PHP (requires libevent and PHP 5.4).

#how to run#

##telnet echo server##

In one terminal, type:

>php examples/telnet.php

Beehive will now be listening on 127.0.0.1:9000

Open a new terminal, and type:

>telnet localhost 9000

The server will echo any messages sent to it

##webchat web socket server##

Copy examples/webchat/client.js and examples/webchat/index.html to the document root of a test server.

In one terminal, type:

>php examples/webchat/server.php

Beehive will now be listening on 127.0.0.1:9000

Open a browser window and navigate to your testing vhost, ie http://localhost/

You should see a simple box with a text input beneath it. Open up multiple tabs to the "chat" and see as messages are shared realtime across clients.
