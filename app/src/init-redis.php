<?php

global $redis;

$redis = new Redis();
$redis->connect( getenv( 'valkey' ) );
