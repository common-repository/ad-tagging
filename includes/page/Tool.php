<?php

namespace SYBAT\Page;

require_once __DIR__ . '/BasePage.php';

class Tool extends BasePage {

	public function __construct($pluginDir) {
        parent::__construct($pluginDir);

		global $wpdb;

		$wpdb->hide_errors();
	}

    public function show() {
        wp_enqueue_script('vue-global', $this->getPluginUrl('assets/js/vue.global.js'), array(), '3.4.3', array());
        wp_enqueue_script('axios', $this->getPluginUrl('assets/js/axios.min.js'), array(), '1.1.3', array());

        wp_register_script(
            'sybat-tool', 
            $this->getPluginUrl('assets/js/page/tool.js'), 
            ['wp-i18n'], 
            null, 
            []);
        wp_enqueue_script('sybat-tool');

        wp_set_script_translations(
            'sybat-tool',
            'syb-ad-tagging', 
            $this->getTranslationDirectory());

        $context = [
            'nonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => admin_url(),
        ];

        wp_add_inline_script(
            'sybat-tool',
	    	'window[\'sybat\'] = ' . json_encode($context) . ';',
            'before'
        );
        
        require_once __DIR__ . '/../view/tool.php';
    }
   
}
