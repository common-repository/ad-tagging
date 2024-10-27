<?php

namespace SYBAT\Api;

require_once __DIR__ . '/route/SearchPostsRoute.php';
require_once __DIR__ . '/route/UpdatePostTagsRoute.php';
require_once __DIR__ . '/route/UpdateSettingsRoute.php';

class Api {
	const NAMESPACE = 'sybat';

	/**
	 * Instance variable
	 *
	 * @var Api|null
	 **/
	private static $instance = null;

	/**
	 * Array of endpoint routes
	 *
	 * @var Array
	 **/
	private $routes = array();

	/**
	 * Create API
	 *
	 * @return Api
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Api();
		}

		return self::$instance;
	}

	public function __construct() {
		global $wpdb;

		$wpdb->hide_errors();

		$this->routes[] = new Route\SearchPostsRoute(self::NAMESPACE);
		$this->routes[] = new Route\UpdatePostTagsRoute(self::NAMESPACE);
		$this->routes[] = new Route\UpdateSettingsRoute(self::NAMESPACE);
	}
}
