<?php

class SitesTest extends PHPUnit\Framework\TestCase {

	public function test_site_can_be_registered() {
		$site_id = get_new_site_id();
		$site_info = array(
			'id' => $site_id,
			'url' => $site_id . '.integration.test',
		);

		$client = new GuzzleHttp\Client();
		$response = $client->post(
			get_test_api_domain() . '/sites?token=test',
			array(
				'json' => $site_info
		) );

		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 'Site created', json_decode( $response->getBody()->getContents(), true )['message'] );
	}
}