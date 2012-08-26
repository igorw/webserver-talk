<?php

$sock = stream_socket_server('tcp://0.0.0.0:5000');

while (true) {
    $conn = stream_socket_accept($sock, -1);
    fwrite($conn, date(DATE_RFC822)."\n");
    fclose($conn);
}
