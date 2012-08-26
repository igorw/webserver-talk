<?php

require __DIR__.'/build-response.php';

$responseSpec = [200, ['Content-Length' => '2'], 'Hi'];

$response = implode("\r\n", [
    'HTTP/1.1 200 OK',
    'Content-Length: 2',
    '',
    'Hi',
]);

assert($response === buildResponse($responseSpec));
