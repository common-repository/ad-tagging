<?php

namespace SYBAT\Client;

class WordPressRestClient {

	public function __construct() {
	}

    /**
     * @param function $callback
     */
	public function traverseAllPosts($callback) {
        $request = new \WP_REST_Request('GET', '/wp/v2/posts');
        $request->set_param('_fields', 'id,title,content,tags,link');
        $this->traverseCore($request, 20, $callback);
    }

    public function getAllTags() {
        $allTags = [];

        // https://developer.wordpress.org/rest-api/reference/tags/#list-tags
        $request = new \WP_REST_Request('GET', '/wp/v2/tags');
        $request->set_param('_fields', 'id,name');
        $this->traverseCore($request, 100, function ($tag) use (&$allTags) {
            array_push($allTags, $tag);
        });
        
        return $allTags;
    }

    /**
     * @param WP_REST_Request $restRequest
     * @param int $perPage 
     * @param function $callback
     */
	private function traverseCore($restRequest, $perPage, $callback) {
        $totalPages = 1000;

        for ($page = 1; $page <= $totalPages; $page++) {
            $restRequest->set_param('per_page', $perPage);
            $restRequest->set_param('page', $page);
            $response = rest_do_request($restRequest);
            if ($response->is_error()) {
                $error = $response->as_error();
                $message = $error->get_error_message();
                error_log('error: ' . $message);
                wp_die('error: ' . $message);
                break;
            }
            $data = $response->get_data();
            foreach ($data as $entry) {
                $callback($entry);
            }
            // https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/
            $totalPages = intval($response->get_headers()['X-WP-TotalPages']);
        }
    }

	public function createTag($tagName) {
		// https://developer.wordpress.org/rest-api/reference/tags/#create-a-tag
        $request = new \WP_REST_Request('POST', '/wp/v2/tags');
		$request->add_header('Content-Type', 'application/json');
		$body = json_encode(['name' => $tagName]);
        $request->set_body($body);
        $response = rest_do_request($request);

        if ($response->is_error()) {
            $error = $response->as_error();
            $message = $error->get_error_message();
            error_log('error: ' . $message);
            wp_die('error: ' . $message);
        }
		$data = $response->get_data();

		return $data['id'];
	}
}
