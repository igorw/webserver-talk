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

$request = implode("\r\n", [
    'POST / HTTP/1.1',
    'Host: igor.io',
    '',
    "foo\r\nbar",
]);

$expected = [
    'method'    => 'POST',
    'path'      => '/',
    'protocol'  => 'HTTP/1.1',
    'headers'   => ['Host' => 'igor.io'],
    'body'      => "foo\r\nbar",
];

assert($expected === parseRequest($request));
