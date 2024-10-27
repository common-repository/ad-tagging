<?php

namespace SYBAT\Api\Route;

require_once __DIR__ . '/../../client/WordPressRestClient.php';
require_once __DIR__ . '/../../model/Settings.php';
use SYBAT\Client\WordPressRestClient;
use SYBAT\Model\Settings;

class UpdatePostTagsRoute {

	public function __construct( $namespace ) {
		register_rest_route( $namespace, '/update-post-tags', [
			'methods' => 'POST',
			'callback' => [$this, 'updateTag'],
			'permission_callback' => function() {
				return current_user_can('edit_others_posts');
			}
		] );
	}

	public function updateTag(\WP_REST_Request $request) {
		$json = $request->get_json_params();
		$idList = $json['id'];

        $settings = new Settings();
        $adTagName = $settings->getAdTagName();
		
		$adTagId = $this->registerTag($adTagName);

		foreach ($idList as $postId) {
			$this->addPostTag($postId, $adTagId);
		}
	}

	private function registerTag($tagName) {
		$wpRestClient = new WordPressRestClient();
		$allTags = $wpRestClient->getAllTags();

        foreach ($allTags as $tag) {
            if ($tag['name'] == $tagName) {
                return $tag['id'];
            }
        }

		$tagId = $wpRestClient->createTag($tagName);

		return $tagId;

	}

	private function addPostTag($postId, $adTagId) {
		// get post tags
        $request = new \WP_REST_Request('GET', '/wp/v2/posts/' . $postId);
        $request->set_param('_fields', 'tags');
        $response = rest_do_request($request);

        if ($response->is_error()) {
            $error = $response->as_error();
            $message = $error->get_error_message();
			error_log('error: ' . $message);
            wp_die('error: ' . $message);
        }
		
		// new tags
        $data = $response->get_data();
		$tags = $data['tags'];
		array_push($tags, $adTagId);
		$tags = array_unique($tags);
	
		// update post tags
        $request = new \WP_REST_Request('POST', '/wp/v2/posts/' . $postId);
		$request->add_header('Content-Type', 'application/json');
		$body = json_encode(['tags' => $tags]);
        $request->set_body($body);
        $response = rest_do_request($request);

        if ($response->is_error()) {
            $error = $response->as_error();
            $message = $error->get_error_message();
			error_log('error: ' . $message);
            wp_die('error: ' . $message);
        }
	}
}
