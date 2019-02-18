<?php

if (!defined('ABSPATH')) {
    exit;
}

ob_start('final_output');

function final_output($content)
{
    return apply_filters('final_output', $content);
}

add_filter('final_output', function ($html) {
    return replaceDomains($html);
});

function replaceDomains(string $html) : string
{
    $genealabsCdnRewriterOptions = get_option('genealabs_cdn_rewriter_settings');

    if (!$genealabsCdnRewriterOptions) {
        return $html;
    }

    $domain = rtrim($genealabsCdnRewriterOptions["cdn_edge_url"], '/') . '/';

    return preg_replace('/((?:http|https)?\:\/\/\/?[^\s]*?\/uploads\/)(.*?[\"\',])/i', "{$domain}$2", $html);
}

/********************
 * Admin Menu Items
 * *****************/
add_action('admin_menu', function () {
    add_options_page('CDN URL Rewriter', 'CDN URL Rewriter', 'manage_options', 'genealabs_cdn_rewriter_options', 'genealabsCdnRewriterOptionsPage');
});

add_action('admin_init', function () {
    register_setting('genealabs_cdn_rewriter_settings_group', 'genealabs_cdn_rewriter_settings');
});

function genealabsCdnRewriterOptionsPage()
{
    $genealabsCdnRewriterOptions = get_option('genealabs_cdn_rewriter_settings');

    ob_start();
?>
    <div class="wrap">
    	<h1>CDN URL Rewriter</h1>
    	<form method="post" action="options.php">
            <?php settings_fields('genealabs_cdn_rewriter_settings_group'); ?>
            <p>
                <label class="description" for="genealabs_cdn_rewriter_settings[cdn_edge_url]">
                    <?php _e('Enter URL of the "uploads" folder in your CDN for this site', 'genealabs_cdn_rewriter'); ?>
                </label>
            </p>
            <p>
                <input
                    id="genealabs_cdn_rewriter_settings[cdn_edge_url]"
                    type="text"
                    name="genealabs_cdn_rewriter_settings[cdn_edge_url]"
                    value="<?php echo $genealabsCdnRewriterOptions['cdn_edge_url']; ?>"
                    style="width: 500px;"
                >
            </p>
            <p>
                <strong>Example:</strong> https://yourbucket.region.cdn.digitaloceanspaces.com/yoursite/uploads
            </p>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'genealabs_cdn_rewriter'); ?>" />
			</p>
            <p>Clear the above field(s) or disable the plugin to restore original functionality.</p>
		</form>
	</div>
<?php
    echo ob_get_clean();
}
