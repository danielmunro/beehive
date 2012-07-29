beehive
=======

Beehive is an event-driven socket server implemented in PHP

how to run
==========

In one terminal, enter:

>php example.php

Open a new terminal, and type:

>telnet localhost 9000

The client is now connected to the server. The server will accept incoming messages over telnet and will relay them to the client. In the current implementation, the client does nothing with the input.
