<?php

namespace SYBAT\Api\Route;

use SYBAT\Model\Settings;

require_once __DIR__ . '/../../model/Settings.php';

class UpdateSettingsRoute {

	public function __construct( $namespace ) {
		register_rest_route( $namespace, '/update-settings', [
			'methods' => 'POST',
			'callback' => [$this, 'updateSettings'],
			'permission_callback' => function() {
				return current_user_can('manage_options');
			}
		] );
	}

	public function updateSettings(\WP_REST_Request $request) {
		$json = $request->get_json_params();

		$adTagName = $json['ad_tag_name'];
		if (!$adTagName || !is_string($adTagName)) {
			wp_die('error: ad_tag_name');
		}

		$affiliateUrls = $json['affiliate_urls'];
		if (!$affiliateUrls || !is_string($affiliateUrls)) {
			wp_die('error: affiliateUrls');
		}

		$updateOnSave = $json['update_on_save'];

		$affiliateUrlList = preg_split("/\r\n|\n|\r/", trim($affiliateUrls));
		$newAffiliateUrlList = [];
		foreach ($affiliateUrlList as $url) {
			$url = trim($url);
			if ($url != "") {
				array_push($newAffiliateUrlList, $url);
			}
		}

		$settings = new Settings();
		$settings->setAdTagName($adTagName);
		$settings->setAffiliateUrlList($newAffiliateUrlList);
		$settings->setUpdateOnSave(boolval($updateOnSave));
		$settings->store();
		
		return [
			'result' => 'ok',
		];
	}

}
