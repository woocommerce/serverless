<?php

function get_index_name( array $site_info, string $document_type ): string {
	return implode(
		'_',
		array(
			str_replace( ':', '_', CLIENT_SITE_NS ),
			$site_info['id'],
			$document_type,
		)
	);
}

function es_index_document( array $site_info, string $document_type, array $document ): array {
	$es_url = ES_URL . '/' . get_index_name( $site_info, $document_type ) . '/_doc';
	$ch     = es_get_curl_object();
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $document ) );

	$response  = curl_exec( $ch );
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );

	return array(
		'status'   => $http_code,
		'response' => json_decode( $response, true ),
	);
}

function es_map_index( array $site_info, string $document_type, array $mapping ): array {
	$es_url = ES_URL . '/' . get_index_name( $site_info, $document_type );
	$ch     = es_get_curl_object();
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $mapping ) );

	$response  = curl_exec( $ch );
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );

	return array(
		'status'   => $http_code,
		'response' => json_decode( $response, true ),
	);
}

function es_get_curl_object(): CurlHandle {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json' ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	return $ch;
}

function es_map_products( array $site_info ): array {
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

	return es_map_index( $site_info, 'products', $product_mapping );
}
