<?php
// www/routing.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Max-Age:1000");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, x-auth-token,Content-Type, Accept");

if (preg_match('/\.(?:jpg|jpeg|gif|css|js|ico|html)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include __DIR__ . '/index.php';
}
