<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';

class WebSocketServer implements MessageComponentInterface {

    private $clients;

public function __construct() {
    $this->clients = new \SplObjectStorage; // To keep track of all clients
}
public function onOpen(ConnectionInterface $conn) {
    $this->clients->attach($conn);
    echo "New connection: ({$conn->resourceId})\n";
}


public function onMessage(ConnectionInterface $from, $msg) {
    echo "Received message: $msg\n";
    
    foreach ($this->clients as $client) {
       
            $client->send($msg);
     
    }
}

    

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection closed: ({$conn->resourceId})\n";
    }
    

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new WebSocketServer()
        )
    ),
    8080
);

$server->run();
