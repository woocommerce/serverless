<?php
global $redis;

if ( ! isset( $argc ) ) {
	die( 'This script can only be run from the command line, additionally a pattern to go through sites must be provided.' );
}

try {
	include_once __DIR__ . '../init-redis.php';
} catch ( RedisException $e ) {
	echo 'Unable to connect to redis instance';
}

$pattern = $argv[0];
$sites   = $redis->keys( 'si:' . $pattern );

require_once __DIR__ . '../es-requests.php';
foreach ( $sites as $site ) {
	$site_info = $redis->get( $site );
	$response  = es_map_products( $site_info );
}
