beehive
=======

Beehive is an event-driven socket server powered by libevent and implemented in PHP (requires libevent and PHP 5.4).

how to run
==========

In one terminal, enter:

>php example.php

Open a new terminal, and type:

>telnet localhost 9000

The client is now connected to the server. The server will accept incoming messages over telnet and will relay them to the client. In the example implementation (the server default) the input is just echo'd back to the client.
