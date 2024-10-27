<?php

namespace SYBAT\Page;

use SYBAT\Model\Settings;

require_once __DIR__ . '/BasePage.php';
require_once __DIR__ . '/../model/Settings.php';

class Option extends BasePage {

	public function __construct($pluginDir) {
        parent::__construct($pluginDir);
        
		global $wpdb;

		$wpdb->hide_errors();
	}

    public function show() {
        wp_enqueue_script('vue-global', $this->getPluginUrl('assets/js/vue.global.js'), array(), '3.4.3', array());
        wp_enqueue_script('axios', $this->getPluginUrl('assets/js/axios.min.js'), array(), '1.1.3', array());
        
        wp_register_script(
            'sybat-option', 
            $this->getPluginUrl('assets/js/page/option.js'), 
            ['wp-i18n'], 
            null, 
            []);
        wp_enqueue_script('sybat-option');

        wp_set_script_translations(
            'sybat-option',
            'syb-ad-tagging', 
            $this->getTranslationDirectory());

        $settings = new Settings();

        $context = [
            'nonce' => wp_create_nonce('wp_rest'),
            'adTagName' => $settings->getAdTagName(),
            'affiliateUrlList' => $settings->getAffiliateUrlList(),
            'updateOnSave' => $settings->getUpdateOnSave(),
        ];

        wp_add_inline_script(
            'sybat-option',
	    	'window[\'sybat\'] = ' . json_encode($context) . ';',
            'before'
        );

        require_once __DIR__ . '/../view/option.php';
    }

    public function getUrl() {
        $url = add_query_arg(
			[
				'page' => 'sybat',
            ],
			admin_url('options-general.php')
		);
        return $url;
    }
   
}
