beehive
=======

Beehive is an event-driven socket server powered by libevent and implemented in PHP (requires libevent and PHP 5.4).

how to run
==========

#Telnet echo server#

In one terminal, type:

>php examples/telnet.php

Beehive will now be listening on 127.0.0.1:9000

Open a new terminal, and type:

>telnet localhost 9000

The server will echo any messages sent to it.
