<?php 
    if (!defined('ABSPATH')) {
	    die();
    }
?>

<span id="sybat_message">
    <?php 
        printf(
            esc_html__(
                'If an affiliate link is included, the tag "%1$s" will be automatically added when the post is published/saved as draft.',
                'syb-ad-tagging'
            ),
            esc_html($context->settings->getAdTagName())
        );
    ?>
    <a href="<?php echo esc_url($context->optionPageUrl); ?>"><?php echo esc_html__('Settings'); ?></a>
</span>
