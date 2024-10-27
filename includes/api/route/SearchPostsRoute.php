<?php

namespace SYBAT\Api\Route;

require_once __DIR__ . '/../../client/WordPressRestClient.php';
require_once __DIR__ . '/../../model/Settings.php';
use SYBAT\Client\WordPressRestClient;
use SYBAT\Model\Settings;

class SearchPostsRoute {

	public function __construct($namespace) {
		register_rest_route( $namespace, '/search-posts', [
			'methods' => 'GET',
			'callback' => [$this, 'searchPosts'],
			'permission_callback' => function() {
				return current_user_can('edit_others_posts');
			}
		] );

	}

	public function searchPosts(\WP_REST_Request $request) {
        $output = [];

        $settings = new Settings();
        $restClient = new WordPressRestClient();

        $allTags = $restClient->getAllTags();

        $restClient->traverseAllPosts(function($post) use (&$output, $allTags, $settings) {
            $filteredPost = $this->filterPost($post, $allTags, $settings);
            if (!is_null($filteredPost)) {
                array_push($output, $filteredPost);
            }
        });

		return new \WP_REST_Response($output, 200);
	}

    /**
     * @param array $post
     * @param array $allTags
     * @param Settings $settings
     */
    private function filterPost($post, $allTags, $settings) {
        $id = $post['id'];
        $content = $post['content']['rendered'];
        $tagIds = $post['tags'];
        
        if ($this->isAffiliateContent($content, $settings)) {
            $adTagName = $settings->getAdTagName();
            $tagNames = $this->convertTagIdsToNames($tagIds, $allTags);
            $new_post = [
                'id' => $id,
                'title' => $post['title'],
                'link' => $post['link'],
                'tags' => $tagNames,
                'affiliate' => true,
            ];
            if ($this->containsTag($tagNames, $adTagName)) {
                $new_post['new_tags'] = [];
                $new_post['tag_required'] = false;
            } else {
                $new_tags = $this->addTag($tagNames, $adTagName);
                $new_post['new_tags'] = $new_tags;
                $new_post['tag_required'] = true;
            }
            if (current_user_can('edit_post', $id)) {
                $new_post['editable'] = true;
            } else {
                $new_post['editable'] = false;
            }
            return $new_post;
        }

		return null;
	}

    private function convertTagIdsToNames($tagIds, $allTags) {
        $outTagNames = [];

        foreach ($tagIds as $tagId) {
            $tagName = $this->convertTagIdToName($tagId, $allTags);
            array_push($outTagNames, $tagName);
        }

        return $outTagNames;
    }

    private function convertTagIdToName($tagId, $allTags) {
        foreach ($allTags as $tag) {
            if ($tag['id'] == $tagId) {
                return $tag['name'];
            }
        }

        return null;
    }

    private function isAffiliateContent($content, $settings) {
        $links = $settings->getAffiliateUrlList();
        
        $pattern = '/';
        foreach ($links as $i => $link) {
            if ($i != 0) {
                $pattern .= '|';
            }
            $pattern .= preg_quote($link, '/');
            $pattern .= '|';
            $pattern .= preg_quote(str_replace('https://', '"//', $link), '/');
        }
        $pattern .= '/';
    
        // remove html comment
        $content = preg_replace('/<!--[\s\S]*?-->/s', '', $content);
    
        if (preg_match($pattern, $content) === 1) {
            return true;
        } else {
            return false;
        }
    }
    
    private function containsTag($tagNames, $tagName) {
        if ($tagNames && array_search($tagName, $tagNames, true) !== false) {
            return true;
        } else {
            return false;
        }
    }
    
    private function addTag($tags, $tag) {
        array_push($tags, $tag);
        return array_unique($tags);
    }

}
