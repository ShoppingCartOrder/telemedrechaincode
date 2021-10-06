<?php
if (isset ( $_POST ['jsonString'] )) {
	include "ss2.php";
	$shieldsquare_config_data = new shieldsquare_config ();
	$shieldsquare_service_url = 'http://' . $shieldsquare_config_data->_ss2_domain . '/getss2data';
	$shieldsquare_request = json_decode ( urldecode ( $_POST ['jsonString'] ) );
	$shieldsquare_request->sid = $shieldsquare_config_data->_sid;
	$shieldsquare_request->host = isset ( $_SERVER [$shieldsquare_config_data->_ipaddress] ) ? $_SERVER [$shieldsquare_config_data->_ipaddress] : '';
	$shieldsquare_post_data = json_encode ( $shieldsquare_request );
	if ($shieldsquare_config_data->_async_http_post === true)
	{
		$error_code = shieldsquare_post_async ( $shieldsquare_service_url, $shieldsquare_post_data );
		echo $error_code->error_string;
	}
	else {
		// Get Curl version
		$curl_info = curl_version ();
		// For older verisons of CURL, the timeout is set to one second.
		$curl_timeout = 1;
		$is_curl_ms_timeout = false;
		
		if (version_compare ( $curl_info ['version'], '7.16.2' ) >= 0) {
			if ($shieldsquare_config_data->_timeout_value > 1000) {
				echo "ShieldSquare Timeout can't be greater then 1000 Milli seconds";
				exit ();
			}
			$curl_timeout = $shieldsquare_config_data->_timeout_value;
			$is_curl_ms_timeout = true;
		}
		$error_code = shieldsquare_post_sync ( $shieldsquare_service_url, $shieldsquare_post_data, $curl_timeout, $is_curl_ms_timeout );
		echo $error_code->error_string;
	}
}
?>