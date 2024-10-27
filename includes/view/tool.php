<?php 
    if (!defined('ABSPATH')) {
	    die();
    }
?>

<style type="text/css">
[v-cloak] {
  display: none;
}
</style>
<div id="sybat-app" v-cloak>
    <div class="wrap">
        <h1><?php esc_html_e('Ad Tagging', 'syb-ad-tagging'); ?></h1>

        <p>
            <?php 
                $kses_allow_link = array(
                    'a' => array(
                        'href' => true,
                        'v-on:click' => true,
                    ),
                );
                    
                echo wp_kses(
                    __(
                        'Tag names and affiliate URLs can be changed in the <a href="#" v-on:click="openSetting">Ad Tagging settings</a>.',
                        'syb-ad-tagging'
                    ),
                    $kses_allow_link                    
                );
            ?>
        </p>

        <div v-if="message != null" class="notice notice-success is-dismissible">
            <p>{{ message }}</p>
        </div>

        <p>
            <input type="button" 
                class="button"
                :class="{ 'button-primary': posts == null || posts.length == 0 }"
                value="<?php esc_html_e('Search for affiliate posts', 'syb-ad-tagging'); ?>"
                :disabled="processingSearch"
                :style="{cursor: processingSearch ? 'progress': ''}"
                @click="searchPosts">
            &nbsp;
            <input type="button" class="button button-primary" 
                value="<?php esc_html_e('Update tags on checked posts', 'syb-ad-tagging'); ?>"
                v-show="posts && posts.length > 0"
                :disabled="checkedPosts.length == 0 || processingUpdate"
                :style="{cursor: processingUpdate ? 'progress': ''}"
                @click="updateTag">
        </p>

        <div v-if="posts != null">
            <p><?php esc_html_e('Number of posts:', 'syb-ad-tagging'); ?> {{ posts.length }}</p>

            <table class="wp-list-table widefat fixed striped table-view-list posts">
                <thead>
                    <tr>
                        <th class="check-column"></th>
                        <th style="width: 3em">ID</th>
                        <th><?php esc_html_e('Title'); ?></th>
                        <th><?php esc_html_e('Tags before change', 'syb-ad-tagging'); ?></th>
                        <th><?php esc_html_e('Tags after change', 'syb-ad-tagging'); ?></th>
                    </tr>
                </thead>
                <tr v-for="post in posts">
                    <th class="check-column">
                        <input type="checkbox" 
                            :value="post.id" 
                            v-model="checkedPosts"
                            :disabled="!(post.editable && post.tag_required)" />
                    </th>
                    <td>{{ post.id }}</td>
                    <td><a :href="post.link">{{ post.title.rendered }}</a></td>
                    <td>{{ post.tags.join('、') }}</td>
                    <td>{{ post.new_tags.join('、') }}</td>
                </tr>
            </table>

            <p><?php esc_html_e('Number of posts:', 'syb-ad-tagging'); ?> {{ posts.length }}</p>

        </div>
    </div>
</div>


