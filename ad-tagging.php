<?php
/**
 * Plugin Name: Ad Tagging
 * Plugin URI: 
 * Description: A plugin to add tag to affiliate posts.
 * Version: 0.1.3
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * Author: shinya-blogger
 * Author URI: https://note.com/shinya_blogger/
 * License: GPL2
 * Text Domain: syb-ad-tagging
 */
/*  Copyright 2023 shinya-blogger (email: shinya.blogger@gmail.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// for direct access
if (!defined('ABSPATH')) {
    die();
}

//add_action('admin_init', 'sybat_admin_init');
//
//function sybat_admin_init() {
//    load_plugin_textdomain('syb-ad-tagging', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
//}

add_action('admin_menu', 'sybat_menu');

function sybat_menu() {
    load_plugin_textdomain('syb-ad-tagging', false, dirname(plugin_basename( __FILE__ )) . '/languages/');

    $page_title = __('Ad Tagging', 'syb-ad-tagging');
    $menu_title = __('Ad Tagging', 'syb-ad-tagging');
    $capability = 'manage_options';
    $menu_slug = 'sybat';
    add_management_page($page_title, $menu_title, $capability, $menu_slug, 'sybat_admin_page');

    $page_title = __('Ad Tagging Settings', 'syb-ad-tagging');
    $menu_title = __('Ad Tagging Settings', 'syb-ad-tagging');
    add_options_page($page_title, $menu_title, $capability, $menu_slug, 'sybat_option_page');
}

function sybat_admin_page() {
    require_once __DIR__ . '/includes/page/Tool.php';
    $page = new SYBAT\Page\Tool(sybat_directory());
    $page->show();
}
function sybat_option_page() {
    require_once __DIR__ . '/includes/page/Option.php';
    $page = new SYBAT\Page\Option(sybat_directory());
    $page->show();
}

function sybat_directory() {
    return dirname(__FILE__);
}

add_action('rest_api_init', 'sybat_register_rest');

function sybat_register_rest() {
    require_once __DIR__ . '/includes/api/Api.php';

    SYBAT\Api\Api::init();

    remove_action( 'rest_api_init', 'sybat_register_reset' );
}


add_filter('plugin_action_links', 'sybat_add_settings_link', 10, 2);

function sybat_add_settings_link($links, $file) {

	if ($file === plugin_basename(__FILE__)) {
        require_once __DIR__ . '/includes/page/Option.php';
        $page = new SYBAT\Page\Option(sybat_directory());
        $url = $page->getUrl();

		$settings_link = '<a href="' . esc_url($url) . '">' . esc_html__('Settings') . '</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}



add_action('add_meta_boxes', 'sybat_add_meta_box');

function sybat_add_meta_box() {
    require_once __DIR__ . '/includes/model/Settings.php';
    $settings = new SYBAT\Model\Settings();
    
    if ($settings->getUpdateOnSave()) {
        add_meta_box(
            'sybat.meta_box', // ID of the meta box
            'Ad Tagging', // Title of the meta box
            'sybat_display_meta_box', // Callback function to display the meta box
            'post', // Post type where the meta box should appear
            'normal', // Context (where the meta box should appear, e.g., 'normal', 'side', or 'advanced')
        );
    }
}

function sybat_display_meta_box($post) {
    require_once __DIR__ . '/includes/page/MetaBox.php';
    $page = new SYBAT\Page\MetaBox(sybat_directory(), $post);
    $page->show();
}

add_action('save_post', 'sybat_save_meta_box', 10, 2);

function sybat_save_meta_box($post_id, $post) {
    require_once __DIR__ . '/includes/page/MetaBox.php';
    $page = new SYBAT\Page\MetaBox(sybat_directory(), $post);
    $page->save();
}




register_uninstall_hook(__FILE__, 'sybat_uninstall');

function sybat_uninstall() {
    require_once __DIR__ . '/includes/model/Settings.php';
    $settings = new SYBAT\Model\Settings();
    $settings->delete();

}

?>
