<?php

function get_new_site_id() {
	return str_replace( '.', '', uniqid( '', true ) );
}

function get_test_api_domain() {
	return 'wb.test:81';
}
