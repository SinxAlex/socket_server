<?php
namespace  A\A\server;


class Server
{
    /**
     * @var
     */

    protected   $socket;
    protected   $bind;
    public  function __construct($host,$port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        $this->bind   = socket_bind($this->socket, $host, $port);
        socket_listen($this->socket);

    }

    public  function Start()
    {

        if($this->bind)
        {
               echo "Server started success!!! \r\n";
               echo $this->Run();
        }
        $this->Stop();

    }

    private function  Run()
    {
        $clients = array($this->socket);
        while (true)
        {
            // create a copy, so $clients doesn't get modified by socket_select()
            $read = $clients;

            // get a list of all the clients that have data to be read from
            // if there are no clients with data, go to next iteration
            if (socket_select($read, $write, $except, 0) < 1)
                continue;
            if (in_array($this->socket, $read))
            {
                // accept the client, and add him to the $clients array
                $clients[] = $newsock = socket_accept($this->socket);

                // send the client a welcome message
                socket_write($newsock, "Client connected success...\n".
                    "There are ".(count($clients) - 1)." client(s) connected to the server\n");

                socket_getpeername($newsock, $ip);
                echo "New client connected: {$ip}\n";

                // remove the listening socket from the clients-with-data array
                $key = array_search($this->socket, $read);
                unset($read[$key]);
            }

            foreach ($read as $read_sock) {
                // read until newline or 1024 bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $data = @socket_read($read_sock, 1024, PHP_NORMAL_READ);

                // check if the client is disconnected
                if ($data === false) {
                    // remove client for $clients array
                    $key = array_search($read_sock, $clients);
                    unset($clients[$key]);
                    echo "client disconnected.\n";
                    // continue to the next client to read from, if any
                    continue;
                }

                // trim off the trailing/beginning white spaces
                $data = trim($data);

                // check if there is any data after trimming off the spaces
                if (!empty($data))
                {

                    // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                    foreach ($clients as $send_sock)
                    {

                        // if its the listening sock or the client that we got the message from, go to the next one in the list
                        if ($send_sock == $this->socket || $send_sock == $read_sock)
                            continue;

                        // write the message to the client -- add a newline character to the end of the message
                       $this->SendMessage($send_sock,$data."\n");
                    } // end  foreach

                }

            } // end of reading foreach
        }
       socket_close($this->socket);

    }
    public  function SendMessage($client,$msg)
    {
            socket_write($client,$msg);
    }

    public  function Stop()
    {
        socket_close($this->socket);
    }
}