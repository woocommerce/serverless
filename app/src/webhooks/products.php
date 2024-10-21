<?php

if ( ! in_array( $_SERVER['REQUEST_METHOD'], array('POST', 'PUT', 'DELETE' ) ) ) {
	header( 'HTTP/1.1 405 Method Not Allowed' );
	exit;
}

if ( ! isset( $_GET['key'] ) ) {
	header( 'HTTP/1.1 400 Bad Request' );
	echo 'Key is required.';
	exit;
}

$key = $_GET['key'];
global $redis;
$site_info_json = $redis->get( WEBHOOK_NS . $key );

if ( ! $site_info_json ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit;
}

$site_info = json_decode( $site_info_json, true );

$product_data = array();

$product_keys = array(
	'id',
	'name',
	'description',
	'slug',
	'date_created',
	'date_created_gmt',
	'date_modified',
	'date_modified_gmt',
	'type',
	'status',
	'featured',
	'catalog_visibility',
	'description',
	'short_description',
	'sku',
	'price',
	'regular_price',
	'sale_price',
	'date_on_sale_from',
	'date_on_sale_from_gmt',
	'date_on_sale_to',
	'date_on_sale_to_gmt',
	'price_html',
	'on_sale',
	'purchasable',
	'total_sales',
	'virtual',
	'downloadable',
	'downloads',
	'download_limit',
	'download_expiry',
	'external_url',
	'button_text',
	'tax_status',
	'tax_class',
	'manage_stock',
	'stock_quantity',
	'stock_status',
	'backorders',
	'backorders_allowed',
	'backordered',
	'sold_individually',
	'weight',
	'dimensions',
	'shipping_required',
	'shipping_taxable',
	'shipping_class',
	'shipping_class_id',
	'reviews_allowed',
	'average_rating',
	'rating_count',
	'related_ids',
	'upsell_ids',
	'cross_sell_ids',
	'parent_id',
	'purchase_note',
	'categories',
	'tags',
	'images',
	'attributes',
	'default_attributes',
	'variations',
	'grouped_products',
	'menu_order',
	'meta_data',
);

$received_data = json_decode( file_get_contents( 'php://input' ), true );
foreach ( $product_keys as $product_key ) {
	if ( isset( $received_data[ $product_key ] ) ) {
		$product_data[ $product_key ] = $received_data[ $product_key ];
	}
}

function index_product( $product_data, $site_info ) : array {
	$method = isset( $product_data['id'] ) ? 'PUT' : 'POST';
	$index_name = intval($site_info['id']);
	$product_id = intval( $product_data['id'] );
	$es_url = getenv( 'search' ) . "/{$index_name}/_doc";
	$es_url = $method === 'PUT' ? "{$es_url}/{$product_id}" : $es_url;

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $product_data ) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, 1);

	$response = curl_exec( $ch );

	curl_close( $ch );

	return json_decode( $response, true );
}

$response = index_product( $product_data, $site_info );