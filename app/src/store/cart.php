<?php

global $redis, $site_info;

$session_id         = session_id();
$existing_cart_json = $redis->get( 'cart:' . $session_id );
$cart               = get_cart_object( $existing_cart_json );

if ( ! $existing_cart_json ) {
	$redis->set( 'cart:' . $session_id, json_encode( $cart ) );
}

function get_empty_cart_object(): array {
	return array(
		'items'                   => array(),
		'coupons'                 => array(),
		'fees'                    => array(),
		'totals'                  => array(),
		'shipping_address'        => array(),
		'billing_address'         => array(),
		'needs_shipping'          => false,
		'needs_payment'           => true,
		'payment_requirements'    => array(),
		'has_calculated_shipping' => false,
		'shipping_rates'          => array(),
		'items_count'             => 0,
		'items_weight'            => 0,
		'cross_sells'             => array(),
		'errors'                  => array(),
		'payment_methods'         => array(),
		'extensions'              => array(),
	);
}

function get_cart_object( $existing_cart_json ) {
	if ( ! $existing_cart_json ) {
		return get_empty_cart_object();
	} else {
		return json_decode( $existing_cart_json, true );
	}
}

header( 'Content-Type: application/json' );
print_r( json_encode( $cart ) );
