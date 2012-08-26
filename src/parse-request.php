<?php

function parseRequest($request)
{
    $lines = explode("\r\n", $request);

    $requestLine = array_shift($lines);
    list($method, $path, $protocol) = explode(' ', $requestLine);

    $headers = [];
    while ($header = array_shift($lines)) {
        list($name, $value) = explode(':', $header, 2);
        $headers[trim($name)] = trim($value);
    }

    $body = array_shift($lines);

    return compact('method', 'path', 'protocol', 'headers', 'body');
}
