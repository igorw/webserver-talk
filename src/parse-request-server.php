<?php

require __DIR__.'/parse-request.php';
require __DIR__.'/build-response.php';

$sock = stream_socket_server('tcp://0.0.0.0:5000');

while (true) {
    $conn = stream_socket_accept($sock, -1);

    $request = fread($conn, 512);
    $parsedRequest = parseRequest($request);

    switch ($parsedRequest['path']) {
        case '/':
            $responseSpec = [200, ['Content-Length' => 3], "Hi\n"];
            break;
        default:
            $responseSpec = [404, ['Content-Length' => 10], "Not found\n"];
    }

    fwrite($conn, buildResponse($responseSpec));
}
