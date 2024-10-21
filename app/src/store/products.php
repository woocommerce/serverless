<?php

global $redis, $site_info;

$site_id = $site_info['id'];

$available_params = array(
	'_fields',
	'search',
	'slug'
);

$params = array();
foreach( $available_params as $param ) {
	if ( isset( $_GET[ $param ] ) ) {
		$params[ $param ] = $_GET[ $param ];
	}
}

$query = generate_es_product_query( $params );
$response = send_es_product_query( $query, $site_id, getenv('search') );

if ( isset( $response['hits']['hits'] ) ) {
	$products = array();
	foreach ( $response['hits']['hits'] as $hit ) {
		$products[] = $hit['fields'];
	}
	header( 'Content-Type: application/json' );
	print_r( json_encode( $products ) );
} else {
	header( 'HTTP/1.1 500 Internal Server Error' );
}


function generate_es_product_query( array $params ) : array {
	$query = array( '_source' => false );

	$matchers = array();

	$matchers[] = array(
		'term' => array(
			'status' => 'publish'
		)
	);

	$allowed_fields = array(
		'id',
		'name',
		'slug',
		'description',
		'sku',
		'price',
		'regular_price',
		'sale_price',
		'categories',
		'tags',
		'images',
	);

	if ( isset( $params['_fields'] ) ) {
		$param_fields = explode( ',', $params['_fields'] );
		if ( count( array_diff( $param_fields, $allowed_fields ) ) > 0 ) {
			header( 'HTTP/1.1 400 Bad Request' );
			echo 'Invalid field requested';
			exit;
		}
		$query['fields'] = array_intersect( $param_fields, $allowed_fields );
		if ( ! in_array ( 'id', $query['fields'] ) ) {
			$query['fields'][] = 'id';
		}
	} else {
		$query['fields'] = $allowed_fields;
	}

	if ( isset( $params['search'] ) ) {
		$matchers[] = array(
			'multi_match' => array(
				'query' => $params['search'],
				'fields' => array( 'name', 'description' )
			)
		);
	}

	if ( isset( $params['slug'] ) ) {
		$matchers[] = array(
			'term' => array(
				'slug' => $params['slug']
			)
		);
	}

	if ( count( $matchers ) > 1 ) {
		$query['query'] = array(
			'bool' => array(
				'must' => $matchers
			)
		);
	} else {
		$query['query'] = $matchers[0];
	}

	return $query;
}

function send_es_product_query( array $query, string $site_id, string $es_url ) : array {
	$index_name = $site_id;
	$es_url = $es_url . '/' . $index_name . '/_search';

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $query ) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, 1);

	$response = curl_exec( $ch );

	curl_close( $ch );

	return json_decode( $response, true );
}
