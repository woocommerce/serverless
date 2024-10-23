<?php
global $redis;
try {
	require_once __DIR__ . '/../src/init-redis.php';
} catch ( RedisException $exception ) {
	echo 'Redis connection error: ' . $exception->getMessage();
	exit;
}
const WEBHOOK_NS     = 't:wh:';
const CLIENT_SITE_NS = 't:si:';

function test_reset_site_info() {
	global $redis;
	$si_val = '{"id":"example_test_1","url":"https://example.test"}';
	$redis->set( CLIENT_SITE_NS . 'example.test', $si_val );

	$wh_val = '{"id":"example_test_1","url":"https://example.test"}';
	$redis->set( WEBHOOK_NS . 'example.test', $wh_val );
}

function test_get_site_info() {
	global $redis;
	$site_info = $redis->get( CLIENT_SITE_NS . 'example.test' );
	return json_decode( $site_info, true );
}

reset_site_info();
