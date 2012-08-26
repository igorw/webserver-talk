<?php

$sock = stream_socket_server('tcp://[::1]:5000');

while (true) {
    $response = implode("\r\n", [
        'HTTP/1.1 200 OK',
        'Content-Length: 2',
        '',
        'Hi',
    ]);

    $conn = stream_socket_accept($sock, -1);
    fwrite($conn, $response);
}
