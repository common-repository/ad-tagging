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
        <h1><?php esc_html_e('Ad Tagging Settings', 'syb-ad-tagging'); ?></h1>

        <div v-if="message != null" class="notice notice-success is-dismissible">
            <p>{{ message }}</p>
        </div>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="ad_tag_name"><?php echo esc_html_x('Tag', 'taxonomy singular name'); ?></label></th>
                    <td>
                        <select v-model="adTagName">
                            <option>AD</option>
                            <option>PR</option>
                            <option>Sponsored</option>
                            <option>アフィリエイト広告</option>
                            <option>プロモーション</option>
                            <option>広告</option>
                            <option>宣伝</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="affiliate_urls"><?php esc_html_e('Affiliate URLs', 'syb-ad-tagging'); ?></label></th>
                    <td>
                        <textarea 
                            v-model="affiliateUrls" 
                            rows="10" class="large-text" placeholder="<?php esc_html_e('URL starting with https://', 'syb-ad-tagging'); ?>">
                        </textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="update_on_save"><?php esc_html_e('Update tags when posting', 'syb-ad-tagging'); ?></label></th>
                    <td>
                        <input type="checkbox" v-model="updateOnSave" :value="true">
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes'); ?>" 
                :disabled="!validateForm()"
                @click="updateSettings">
        </p>
    </div>
</div>
