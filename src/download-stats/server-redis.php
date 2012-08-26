<?php

// publish something using:
// echo 'PUBLISH pub:downloads 25' | redis-cli
// curl localhost:8080

require __DIR__.'/vendor/autoload.php';

$port = 8080;

echo "Stats server is listening on port $port\n";

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$emitter = new \Evenement\EventEmitter();

$redis = new Predis\Async\Client('tcp://127.0.0.1:6379', $loop);
$redis->connect(function () use ($redis, $emitter) {
    $redis->psubscribe('pub:*', function ($event) use ($emitter) {
        list(, $pattern, $chan, $msg) = $event;
        $emitter->emit('message', array($chan, $msg));
    });
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
