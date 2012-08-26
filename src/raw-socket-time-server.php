<?php

$address = '0.0.0.0';
$port = 5000;

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($sock, $address, $port);
socket_listen($sock);

while (true) {
    $conn = socket_accept($sock);
    socket_write($conn, date(DATE_RFC822)."\n");
    socket_close($conn);
}
