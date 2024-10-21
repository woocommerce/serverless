<?php

global $redis;

const WEBHOOK_NS = 'wh:';


$redis = new Redis();
try {
	$redis->connect( getenv( 'valkey' ) );
} catch ( RedisException $e ) {
	header( 'HTTP/1.1 500 Internal Server Error' );
	exit;
}

$request = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

switch ( $request ) {
	case '/webhooks/products':
		require __DIR__ . '/webhooks/products.php';
		break;
}
