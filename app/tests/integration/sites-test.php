<?php

class SitesTest extends PHPUnit\Framework\TestCase {

	private function get_new_site_info() {
		$site_id = get_new_site_id();
		return array(
			'id' => $site_id,
			'url' => $site_id . '.integration.test',
		);
	}

	public function test_site_can_be_registered() {
		$site_info = $this->get_new_site_info();

		$client = new GuzzleHttp\Client();
		$response = $client->post(
			get_test_api_domain() . '/sites?token=test',
			array(
				'json' => $site_info
		) );

		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 'Site created', json_decode( $response->getBody()->getContents(), true )['message'] );
	}

	public function test_site_endpoint_is_not_available_without_token() {
		$site_info = $this->get_new_site_info();

		$client = new GuzzleHttp\Client();
		$response = $client->post(
			get_test_api_domain() . '/sites',
			array(
				'json' => $site_info,
				'http_errors' => false
			)
		);

		$this->assertEquals( 404, $response->getStatusCode() );

		$response = $client->post(
			get_test_api_domain() . '/sites?token=incorrect',
			array( 'json' => $site_info, 'http_errors' => false )
		);

		$this->assertEquals( 403, $response->getStatusCode() );
	}
}