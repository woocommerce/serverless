<?php

function es_index_document( array $site_info, string $document_type, array $document ) {
	$es_url = getenv( 'search' ) . "/{$site_info['id']}/_doc/{$document_type}";
	$ch = es_get_curl_object();
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $document ) );

	$response = curl_exec( $ch );
	curl_close( $ch );

	return json_decode( $response, true );
}

function es_map_index( array $site_info, string $document_type, array $mapping ) {
	$es_url = getenv( 'search' ) . "/{$site_info['id']}/_mapping/{$document_type}";
	$ch = es_get_curl_object();
	curl_setopt( $ch, CURLOPT_URL, $es_url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $mapping ) );

	$response = curl_exec( $ch );
	curl_close( $ch );

	return json_decode( $response, true );
}

function es_get_curl_object(): CurlHandle {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json') );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, 1);
	return $ch;
}
