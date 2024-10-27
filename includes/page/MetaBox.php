<?php

namespace SYBAT\Page;

use SYBAT\Model\Post;
use SYBAT\Model\Settings;

require_once __DIR__ . '/BasePage.php';
require_once __DIR__ . '/Option.php';
require_once __DIR__ . '/../model/Post.php';
require_once __DIR__ . '/../model/Settings.php';

class MetaBox extends BasePage {

    private $pluginDir;
    private $postId;
    private $renderedContent;

    /**
     * @@aram $post WP_Post
     */
	public function __construct($pluginDir, $post) {
        parent::__construct($pluginDir);

        global $wpdb;
		$wpdb->hide_errors();

        $this->pluginDir = $pluginDir;
        $this->postId = $post->ID;

        // ショートコードを展開
        $this->renderedContent = apply_filters('the_content', $post->post_content);
	}

    public function show() {
        $settings = new Settings();
        $post = new Post($this->renderedContent);

        $optionPage = new Option($this->pluginDir);

        $context = json_encode([
            'nonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => admin_url(),
            'optionPageUrl'=> $optionPage->getUrl(),
            'updateOnSave' => $settings->getUpdateOnSave(),
            'affiliateContent' => $post->isAffiliateContent($settings),
            'postId' => $this->postId,
        ]);
        $context = json_decode($context);
        $context->settings = $settings;

        wp_nonce_field(basename( __FILE__ ), 'sybat_meta_box_nonce' );

        require_once __DIR__ . '/../view/metabox.php';
    }
   
    public function save() {
        // Check for a nonce, and ensure the request is valid
        if (!isset($_POST['sybat_meta_box_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sybat_meta_box_nonce'])),
                basename( __FILE__ ))) {
            return;
        }

        $settings = new Settings();
        $post = new Post($this->renderedContent);

        if ($settings->getUpdateOnSave() && $post->isAffiliateContent($settings)) {
            $tags = [$settings->getAdTagName()];
            wp_set_object_terms($this->postId, $tags, 'post_tag', true); 
            /*
            remove_action( 'save_post', 'sybat_save_meta_box' );
            
            wp_update_post([
                'ID' => $this->postId,
                'tags_input' => implode(',', $tags)
            ]);
            
            add_action('save_post', 'sybat_save_meta_box', 10, 2);
            */
        }
    }
}
