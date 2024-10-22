<?php
global $redis;

if ( ! isset( $argc ) ) {
	die( 'This script can only be run from the command line, additionally a pattern to go through sites must be provided.' );
}

$product_mapping = array(
	'mappings' => array(
		'properties' => array(
			'id'                  => array( 'type' => 'integer' ),
			'name'                => array( 'type' => 'text' ),
			'slug'                => array( 'type' => 'text' ),
			'variation'           => array( 'type' => 'nested' ),
			'sku'                 => array( 'type' => 'keyword' ),
			'summary'             => array( 'type' => 'text' ),
			'short_description'   => array( 'type' => 'text' ),
			'description'         => array( 'type' => 'text' ),
			'on_sale'             => array( 'type' => 'boolean' ),
			'prices'              => array(
				'properties' => array(
					'currency_code' => array( 'type' => 'keyword' ),
					'price'         => array( 'type' => 'float' ),
					'regular_price' => array( 'type' => 'float' ),
					'sale_price'    => array( 'type' => 'float' ),
					'price_range'   => array( 'type' => 'float' ),
				),
			),
			'average_rating'      => array( 'type' => 'float' ),
			'review_count'        => array( 'type' => 'integer' ),
			'images'              => array(
				'properties' => array(
					'id'        => array( 'type' => 'integer' ),
					'src'       => array( 'type' => 'keyword' ),
					'thumbnail' => array( 'type' => 'keyword' ),
					'srcset'    => array( 'type' => 'keyword' ),
					'sizes'     => array( 'type' => 'keyword' ),
					'name'      => array( 'type' => 'keyword' ),
					'alt'       => array( 'type' => 'keyword' ),
				),
			),
			'has_options'         => array( 'type' => 'boolean' ),
			'is_purchasable'      => array( 'type' => 'boolean' ),
			'is_in_stock'         => array( 'type' => 'boolean' ),
			'low_stock_remaining' => array( 'type' => 'integer' ),
			'add_to_cart'         => array(
				'properties' => array(
					'text'        => array( 'type' => 'keyword' ),
					'description' => array( 'type' => 'keyword' ),
				),
			),
			'public_meta_data'    => array(
				'properties' => array(
					'key'   => array( 'type' => 'keyword' ),
					'value' => array( 'type' => 'text' ),
				),
			),
		),
	),
);

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
	$response  = es_map_index( $site_info, 'products', $product_mapping );
}
