<?php

require __DIR__.'/parse-request.php';

$request = implode("\r\n", [
    'GET / HTTP/1.1',
    'Host: igor.io',
    '',
    '',
]);

$expected = [
    'method'    => 'GET',
    'path'      => '/',
    'protocol'  => 'HTTP/1.1',
    'headers'   => ['Host' => 'igor.io'],
    'body'      => '',
];

assert($expected === parseRequest($request));
