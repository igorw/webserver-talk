<?php

// publish something using:
// echo 'pub:downloads 25' | zmqc -cw PUB tcp://127.0.0.1:5555
// curl localhost:8080

require __DIR__.'/vendor/autoload.php';

$port = 8080;

echo "Stats server is listening on port $port\n";

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$emitter = new \Evenement\EventEmitter();

$context = new React\ZMQ\Context($loop);
$sock = $context->getSocket(ZMQ::SOCKET_SUB);
$sock->bind('tcp://127.0.0.1:5555');
$sock->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, 'pub:downloads ');
$sock->on('message', function ($data) use ($emitter) {
    list($chan, $msg) = explode(' ', $data, 2);
    $emitter->emit('message', array($chan, $msg));
});

$http->on('request', function ($request, $response) use ($emitter) {
    $headers = array(
        'Content-Type'  => 'text/event-stream',
        'Access-Control-Allow-Origin' => '*',
    );
    $response->writeHead(200, $headers);

    $emitter->on('message', function ($chan, $msg) use ($response) {
        $data = \Igorw\EventSource\Event::create()
            ->setEvent($chan)
            ->setData($msg)
            ->dump();
        $response->write($data);
    });
});

$socket->listen($port);
$loop->run();
