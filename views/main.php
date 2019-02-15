<?php
if (!defined('WPINC')) {
	die;
}
//有整理過些惹...
//TODO:優化
if (!empty($_POST) && wp_verify_nonce($_REQUEST['_wpnonce'], 'mxp-fb2wp-main-setting-page')) {
	$id = $_POST['mxp_fb_app_id'];
	$secret = $_POST['mxp_fb_secret'];
	$token = $_POST['mxp_fb_app_access_token'];
	$vtoken = $_POST['mxp_fb_webhooks_verify_token'];
	$enable_log = $_POST['mxp_enable_debug'];
	$default_reply = $_POST['mxp_messenger_default_reply'];
	$post_enable = $_POST['mxp_fb2wp_post_enable'];
	$post_author = $_POST['mxp_fb2wp_post_author'];
	$post_category = $_POST['mxp_fb2wp_post_category'];
	$post_status = $_POST['mxp_fb2wp_post_status'];
	$comment_status = $_POST['mxp_fb2wp_post_comment_status'];
	$ping_status = $_POST['mxp_fb2wp_post_ping_status'];
	$post_type = $_POST['mxp_fb2wp_post_type'];
	$jssdk = $_POST['mxp_fb_enable_jssdk'];
	$sdk_local = $_POST['mxp_fb_jssdk_local'];
	$fb_api_version = $_POST['mxp_fb_api_version'];
	$auth_users = $_POST['mxp_fb2wp_auth_users'];
	$post_tags = $_POST['mxp_fb2wp_post_tags'];
	$post_title = $_POST['mxp_fb2wp_default_title'];
	$display_attachment = $_POST['mxp_fb2wp_default_display_attachment'];
	$display_embed = $_POST['mxp_fb2wp_default_display_embed'];
	$display_img_caption = $_POST['mxp_fb2wp_default_display_img_caption'];
	$display_img_width = $_POST['mxp_fb2wp_image_width'];
	$display_img_height = $_POST['mxp_fb2wp_image_height'];
	$display_vid_width = $_POST['mxp_fb2wp_video_width'];
	$display_vid_height = $_POST['mxp_fb2wp_video_height'];
	$post_footer = stripslashes($_POST['mxp_fb2wp_post_footer']);
	$no_post_tag = $_POST['mxp_fb2wp_no_post_tag'];
	$enable_fbquote = $_POST['mxp_fb_quote_enable'];
	$enable_fbsave = $_POST['mxp_fb_save_enable'];
	//$enable_fbsend = $_POST['mxp_fb_send_enable'];
	$enable_fbcomments = $_POST['mxp_fb_comments_enable'];
	$complete_remove = $_POST['mxp_complete_remove'];
	$page_id = $_POST['mxp_fb_page_id'];
	$fb_widget_place = $_POST['mxp_fb_widget_place'];
	$clear_cache = $_POST['mxp_fb_clear_url_cache'];
	$enable_messenger = $_POST['mxp_fb2wp_messenger_enable'];
	$msg_auth_users = $_POST['mxp_fb2wp_messenger_auth_users'];
	$enable_pass_thread = $_POST['mxp_fb2wp_messenger_enable_pass_thread'];
	$pass_thread_btn_text = $_POST['mxp_fb2wp_messenger_enable_pass_thread_btn_text'];
	$msg_embed = $_POST['mxp_fb_messenger_embed'];
	$greeting_dialog_delay = intval($_POST['mxp_fb_messenger_greeting_dialog_delay']);
	$logged_in_greeting = strip_tags($_POST['mxp_fb_messenger_logged_in_greeting']);
	$logged_out_greeting = strip_tags($_POST['mxp_fb_messenger_logged_out_greeting']);
	$theme_color = $_POST['mxp_fb_messenger_theme_color'];
	$mxp_remove_plugin_debug_log = (!isset($_POST['mxp_remove_plugin_debug_log']) || $_POST['mxp_remove_plugin_debug_log'] == "") ? "no" : $_POST['mxp_remove_plugin_debug_log'];
	$mxp_active_tab = $_POST['mxp_fb2wp_active_tab'];
	$section_title = stripslashes($_POST['mxp_fb_functions_section_title']);
	$comment_mirror_enable = $_POST['mxp_fb2wp_comment_mirror_enable'];
	$comment_mirror_approved = $_POST['mxp_fb2wp_comment_mirror_approved'];
	if (has_shortcode($post_footer, 'mxp_fb2wp_display_embed')) {
		//待處理
		echo "<script>alert('" . __('Do not contain shortcode [mxp_fb2wp_display_embed] in footer contents for synced posts.', 'fb2wp-integration-tools') . "');</script>";
		unset($post_footer);
	}
}
if (isset($id) && isset($secret) && isset($token) && isset($vtoken) && isset($enable_log) && isset($default_reply)
	&& isset($post_enable) && isset($post_author) && isset($post_category) && isset($post_status) && isset($comment_status)
	&& isset($ping_status) && isset($post_type) && isset($jssdk) && isset($sdk_local) && isset($fb_api_version)
	&& isset($auth_users) && isset($post_tags) && isset($post_title) && isset($display_attachment) && isset($display_embed)
	&& isset($display_img_caption) && isset($display_img_width) && isset($display_img_height) && isset($display_vid_width)
	&& isset($display_vid_height) && isset($post_footer) && isset($no_post_tag) && isset($enable_fbquote)
	&& isset($enable_fbsave) && isset($enable_fbcomments) && isset($complete_remove) && isset($page_id)
	&& isset($section_title) && isset($fb_widget_place) && isset($clear_cache) && isset($enable_messenger)
	&& isset($msg_auth_users) && isset($msg_embed) && isset($mxp_remove_plugin_debug_log) && isset($mxp_active_tab)
	&& isset($enable_pass_thread) && isset($pass_thread_btn_text) && isset($comment_mirror_enable)
	&& isset($comment_mirror_approved) && isset($theme_color) && isset($logged_out_greeting) && isset($logged_in_greeting)
	&& isset($greeting_dialog_delay)) {
	update_option("mxp_fb_app_id", $id);
	update_option("mxp_fb_secret", $secret);
	update_option("mxp_fb_app_access_token", $token);
	update_option("mxp_fb_webhooks_verify_token", $vtoken);
	update_option("mxp_enable_debug", $enable_log);
	update_option("mxp_messenger_default_reply", $default_reply);
	update_option("mxp_fb2wp_post_enable", $post_enable);
	update_option("mxp_fb2wp_post_author", $post_author);
	update_option("mxp_fb2wp_post_category", $post_category);
	update_option("mxp_fb2wp_post_status", $post_status);
	update_option("mxp_fb2wp_post_comment_status", $comment_status);
	update_option("mxp_fb2wp_post_ping_status", $ping_status);
	update_option("mxp_fb2wp_post_type", $post_type);
	update_option("mxp_fb_enable_jssdk", $jssdk);
	update_option("mxp_fb_jssdk_local", $sdk_local);
	update_option("mxp_fb_api_version", $fb_api_version);
	update_option("mxp_fb2wp_auth_users", $auth_users);
	update_option("mxp_fb2wp_post_tags", $post_tags);
	update_option("mxp_fb2wp_default_title", $post_title);
	update_option("mxp_fb2wp_default_display_attachment", $display_attachment);
	update_option("mxp_fb2wp_default_display_embed", $display_embed);
	update_option("mxp_fb2wp_default_display_img_caption", $display_img_caption);
	update_option("mxp_fb2wp_image_width", $display_img_width);
	update_option("mxp_fb2wp_image_height", $display_img_height);
	update_option("mxp_fb2wp_video_width", $display_vid_width);
	update_option("mxp_fb2wp_video_height", $display_vid_height);
	update_option("mxp_fb2wp_post_footer", $post_footer);
	update_option("mxp_fb2wp_no_post_tag", $no_post_tag);
	update_option("mxp_fb_quote_enable", $enable_fbquote);
	update_option("mxp_fb_save_enable", $enable_fbsave);
	update_option("mxp_fb_comments_enable", $enable_fbcomments);
	update_option("mxp_complete_remove", $complete_remove);
	update_option("mxp_fb_page_id", $page_id);
	update_option("mxp_fb_functions_section_title", $section_title);
	update_option("mxp_fb_widget_place", $fb_widget_place);
	update_option("mxp_fb_clear_url_cache", $clear_cache);
	update_option("mxp_fb2wp_messenger_enable", $enable_messenger);
	update_option("mxp_fb2wp_messenger_auth_users", $msg_auth_users);
	update_option("mxp_fb_messenger_embed", $msg_embed);
	update_option("mxp_fb_messenger_greeting_dialog_delay", $greeting_dialog_delay);
	update_option("mxp_fb_messenger_logged_in_greeting", $logged_in_greeting);
	update_option("mxp_fb_messenger_logged_out_greeting", $logged_out_greeting);
	update_option("mxp_fb_messenger_theme_color", $theme_color);
	update_option("mxp_remove_plugin_debug_log", $mxp_remove_plugin_debug_log);
	update_option("mxp_fb2wp_active_tab", $mxp_active_tab);
	update_option("mxp_fb2wp_messenger_enable_pass_thread", $enable_pass_thread);
	update_option("mxp_fb2wp_messenger_enable_pass_thread_btn_text", $pass_thread_btn_text);
	update_option("mxp_fb2wp_comment_mirror_enable", $comment_mirror_enable);
	update_option("mxp_fb2wp_comment_mirror_approved", $comment_mirror_approved);
	echo esc_html__('Updated successfully!', 'fb2wp-integration-tools');
}
$rest_url = '';

if (get_option("mxp_fb2wp_callback_url") == "ERROR") {

	$rest_url = esc_html__('The version of your WordPress too old ( v.%s ) and does not support REST API method. Please make sure to update your WordPress site to v4.7 (or later) version.', 'fb2wp-integration-tools');

} else {
	if (!is_ssl()) {
		//ref:https://developers.facebook.com/docs/graph-api/webhooks#setup
		/* translators: Asking the site owner to upgrade to HTTPS so that they can build secure callback url for Facebook API */
		$rest_url =
			esc_html__('Starting from v2.5, Facebook API does not support insecure callback URL. Please upgrade your website to HTTPS before using the plugin!', 'fb2wp-integration-tools');
	} else {
		$rest_url = get_rest_url(null, get_option("mxp_fb2wp_callback_url"));
	}
}
$tabs = array('fbapp' => '', 'webhooks' => '', 'messenger' => '', 'post_to_wp' => '', 'fb_plugin' => '', 'fb_ratings' => '', 'developer_function' => '');
$activebtn = get_option("mxp_fb2wp_active_tab", "fbapp");
foreach ($tabs as $key => $value) {
	if ($key == $activebtn) {
		$tabs[$key] = 'bar-item button tablink activebtn';
	} else {
		$tabs[$key] = 'bar-item button tablink ';
	}
}
?>
<style>
.Section {display:none;}
</style>
<div class="sidebar bar-block">
  <div class="container">
    <h2><?php esc_html_e('Features', 'fb2wp-integration-tools');?></h2>
  </div>
  <a href="javascript:void(0)" data-id="fbapp" class="<?php echo $tabs['fbapp']; ?>"><?php esc_html_e('Facebook App Settings', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="webhooks" class="<?php echo $tabs['webhooks']; ?>"><?php esc_html_e('Facebook Webhooks Setup', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="messenger" class="<?php echo $tabs['messenger']; ?>"><?php esc_html_e('Facebook Messenger Bot Setup', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="post_to_wp" class="<?php echo $tabs['post_to_wp']; ?>"><?php esc_html_e('Sync Posts to WordPress', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="fb_plugin" class="<?php echo $tabs['fb_plugin']; ?>"><?php esc_html_e('Facebook Plugins', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="fb_ratings" class="<?php echo $tabs['fb_ratings']; ?>"><?php esc_html_e('Facebook Page Ratings', 'fb2wp-integration-tools');?></a>
  <a href="javascript:void(0)" data-id="developer_function" class="<?php echo $tabs['developer_function']; ?>"><?php esc_html_e('Developer Tools', 'fb2wp-integration-tools');?></a>
</div>
<div id="fbapp" class="container Section">
		<h3><?php
/* translators: This "settings" is used for Facebook App Settings tab. */
printf('<a href="%s" target="_blank">Facebook App</a> ' . esc_html__('Settings', 'fb2wp-integration-tools') . '</h2>', 'https://developers.facebook.com');?></h3>
		<form action="" method="POST">
		<p><?php esc_html_e('App ID: ', 'fb2wp-integration-tools');?>
		<input type="text" value="<?php echo get_option("mxp_fb_app_id"); ?>" name="mxp_fb_app_id" size="20" id="fb_app_id" />
		</p>
		<p><?php esc_html_e('App Secret: ', 'fb2wp-integration-tools');?>
		<input type="text" value="<?php echo get_option("mxp_fb_secret"); ?>" name="mxp_fb_secret" size="36" id="fb_app_secret" />
		</p>
		<p><?php esc_html_e('Page ID: ', 'fb2wp-integration-tools');?>
		<input type="text" value="<?php echo get_option("mxp_fb_page_id"); ?>" name="mxp_fb_page_id" size="20" id="mxp_fb_page_id" />
		</p>
		<p><?php esc_html_e('Page Access Token: ', 'fb2wp-integration-tools');?>
		<input type="text" value="<?php echo get_option("mxp_fb_app_access_token"); ?>" name="mxp_fb_app_access_token" size="60" id="fb_app_access_token" />(<a href="https://tw.wordpress.org/plugins/fb2wp-integration-tools/faq/" target="_blank" ><?php esc_html_e('FAQ', 'fb2wp-integration-tools');?></a>)
		</p>
		<p><?php esc_html_e('Enable Facebook JavaScript SDK: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_enable_jssdk" value="yes" <?php checked('yes', get_option("mxp_fb_enable_jssdk", "yes"));?> checked="checked"><label>
		<?php
/* translators: Whether enabling Facebook JS SDK or not */
printf(esc_html__('Enable', 'fb2wp-integration-tools') . '&nbsp;' . esc_html__('(Recommended)', 'fb2wp-integration-tools'));?></label>
		<input type="radio" name="mxp_fb_enable_jssdk" value="no" <?php checked('no', get_option("mxp_fb_enable_jssdk"));?>><label>
		<?php
/* translators: Whether enabling Facebook JS SDK or not */
esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php
/* translators: Choose the language of Facebook JS SDK */
esc_html_e('SDK Locale: ', 'fb2wp-integration-tools');?>
		<?php
$fb2wp = Mxp_FB2WP::get_instance();
// $fblocals = $fb2wp['Mxp_FB2WP']->get_fb_locals()['locale'];
// if ($fblocals != "error") {
// 	echo '<select name="mxp_fb_jssdk_local">';
// 	for ($i = 0; $i < count($fblocals); ++$i) {
// 		$val = $fblocals[$i]['codes']['code']['standard']['representation'];
// 		$key = $fblocals[$i]['englishName'];
// 		echo '<option value="' . $val . '"' . selected(get_option("mxp_fb_jssdk_local", "zh_TW"), $val) . '>' . $key . '</option>';
// 	}
// 	echo '</select>';
// } else {
// 	echo '<input type="hidden" name="mxp_fb_jssdk_local" value="zh_TW"/>';
// 	echo "Facebook 語系檔案發生解析錯誤，請將下面訊息回報開發者： " . $fb2wp['Mxp_FB2WP']->get_fb_locals()['msg'];
// }
echo '<input type="text" value="' . get_option("mxp_fb_jssdk_local", get_locale()) . '" size="7" name="mxp_fb_jssdk_local"/>';
?>
		</p>
		<p><?php
/* translators: Choose the version of Facebook JS SDK */
esc_html_e('SDK Version: ', 'fb2wp-integration-tools');?>
		<input type="text" size="7" value="<?php echo get_option("mxp_fb_api_version", "v3.1"); ?>" name="mxp_fb_api_version">
		</p>
</div>
<div id="webhooks" class="container Section">
		<h3><?php esc_html_e('Facebook App Webhooks Setup', 'fb2wp-integration-tools');?></h3>
		<p><?php esc_html_e('Please fill in the following URL to Callback URL in Webhooks settings', 'fb2wp-integration-tools');?></p>
		<p>
		<input type="text" size="100" value="<?php global $wp_version;
printf($rest_url, $wp_version);?>" id="fb_app_webhooks_callback_url" disabled />
		</p>
		<p><?php esc_html_e('Subscribe to the following events: messages, messaging_postbacks, standby, messaging_handovers, conversations, feed, ratings.', 'fb2wp-integration-tools');?></p>
		<p><?php
/* translators: The verify token provided by site owners to confirm the request sent from Facebook */
esc_html_e('Verify Token: ', 'fb2wp-integration-tools');?>
		<input type="text" value="<?php echo get_option("mxp_fb_webhooks_verify_token"); ?>" name="mxp_fb_webhooks_verify_token">
		</p>
</div>
<div id="messenger" class="container Section">
		<h3><?php esc_html_e('Facebook Messenger Bot Setup', 'fb2wp-integration-tools');?></h3>
		<p><?php esc_html_e('Enable Messenger Bot: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_messenger_enable" value="open" <?php checked('open', get_option("mxp_fb2wp_messenger_enable", "open"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_messenger_enable" value="closed" <?php checked('closed', get_option("mxp_fb2wp_messenger_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('After enabling this feature, please go to Settings > Messenger Platform on your Facebook Page, check "Responses are partially automated, with some support by people," and set the app "Page Inbox 263902037430900" as your Secondary Receiver, and then the users may pass control of the conversation between Messenger Bots and Page managers.', 'fb2wp-integration-tools');?></p>
		<p><?php esc_html_e('Testing Facebook Accounts: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_messenger_auth_users" size="30" value="<?php echo get_option("mxp_fb2wp_messenger_auth_users", ""); ?>" /><br />
		<?php
/* translators: Use comma-separated format to decide which Facebook account to access this feature */
esc_html_e('Use commas to separate testing users\' Facebook user IDs, or leave blank to allow everyone to access this feature.', 'fb2wp-integration-tools');?>
		</p>
		<p><?php
/* translators: Default reply when there is no correspondent match. */
esc_html_e('Default reply: ', 'fb2wp-integration-tools');?>
		<textarea name="mxp_messenger_default_reply" rows="3" cols="30"><?php echo get_option("mxp_messenger_default_reply"); ?></textarea><br /><?php
esc_html_e('You may use [mxp_input_msg] as a placeholder of users input message. For example, "Oops, I don\'t understand what you mean by saying \'[mxp_input_msg]\'. Please try other commands."', 'fb2wp-integration-tools');?></p>
		<p><?php
/* translators: %1$s is the url of Handover Protocol instructions */
printf(__('Enable <a href="%1$s" target="%2$s">Handover Protocol</a>: ', 'fb2wp-integration-tools'), 'https://developers.facebook.com/docs/messenger-platform/handover-protocol', '_blank');?>
		<input type="radio" name="mxp_fb2wp_messenger_enable_pass_thread" value="yes" <?php checked('yes', get_option("mxp_fb2wp_messenger_enable_pass_thread", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_messenger_enable_pass_thread" value="no" <?php checked('no', get_option("mxp_fb2wp_messenger_enable_pass_thread"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php _e('Text shown on Handover Protocol switch button: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_messenger_enable_pass_thread_btn_text" size="30" value="<?php
echo get_option("mxp_fb2wp_messenger_enable_pass_thread_btn_text",
	/* translators: Default message shown on Handover Protocol switch button.*/
	esc_html__('Click here to inform the admin.', 'fb2wp-integration-tools'));
?>" />
		</p>
</div>
<div id="post_to_wp" class="container Section">
		<h3><?php esc_html_e('Sync Posts to WordPress', 'fb2wp-integration-tools');?></h3>
		<p><?php esc_html_e('Enable Sync Posts to WordPress', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_post_enable" value="open" <?php checked('open', get_option("mxp_fb2wp_post_enable", "open"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_post_enable" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		</p>
		<p><?php
/* translators: Default post author for posts synced back to WordPress. */
esc_html_e('Post Author: ', 'fb2wp-integration-tools');?>
		<?php wp_dropdown_users(array('name' => 'mxp_fb2wp_post_author', 'selected' => get_option("mxp_fb2wp_post_author", "1")));?>
		</p>
		<p><?php
/* translators: Default post categories for posts synced back to WordPress. */
esc_html_e('Post Category: ', 'fb2wp-integration-tools');?>
		<?php wp_dropdown_categories(array('name' => 'mxp_fb2wp_post_category', 'hide_empty' => 0, 'selected' => get_option("mxp_fb2wp_post_category", "1")));?>
		</p>
		<p><?php
/* translators: Default post visibility for posts synced back to WordPress. */
esc_html_e('Post Status & Visibility: ', 'fb2wp-integration-tools');?>
		<select name="mxp_fb2wp_post_status">
		<option value="publish" <?php selected(get_option("mxp_fb2wp_post_status"), "publish");?>><?php esc_html_e('Published', 'fb2wp-integration-tools');?></option>
		<option value="pending" <?php selected(get_option("mxp_fb2wp_post_status"), "pending");?>><?php esc_html_e('Pending Review', 'fb2wp-integration-tools');?></option>
		<option value="draft" <?php selected(get_option("mxp_fb2wp_post_status", "draft"), "draft");?>><?php esc_html_e('Draft', 'fb2wp-integration-tools');?></option>
		<option value="private" <?php selected(get_option("mxp_fb2wp_post_status"), "private");?>><?php esc_html_e('Private', 'fb2wp-integration-tools');?></option>
		</select>
		</p>
		<p><?php esc_html_e('Allow Comments: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_post_comment_status" value="open" <?php checked('open', get_option("mxp_fb2wp_post_comment_status", "open"));?>><label><?php esc_html_e('Allow', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_post_comment_status" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_comment_status"));?>><label><?php esc_html_e('Disallow', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Allow Pingbacks & Trackbacks: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_post_ping_status" value="open" <?php checked('open', get_option("mxp_fb2wp_post_ping_status", "open"));?>><label><?php esc_html_e('Allow', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_post_ping_status" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_ping_status"));?>><label><?php esc_html_e('Disallow', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Post Type: ', 'fb2wp-integration-tools');?>
		<?php
$ps = get_post_types(array('public' => true));
echo '<select name="mxp_fb2wp_post_type">';
foreach ($ps as $key => $value) {
	echo '<option value="' . $value . '"' . selected(get_option("mxp_fb2wp_post_type", "post"), $value) . '>' . $value . '</option>';
}
echo '</select>';
?>
		</p>
		<p><?php esc_html_e('Facebook accounts permitted to sync the posts: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_auth_users" size="30" value="<?php echo get_option("mxp_fb2wp_auth_users", ""); ?>" /><br />
		<?php
/* translators: Use comma-separated format to decide which Facebook account to access this feature */
esc_html_e('Use commas to separate testing users\' Facebook user IDs, or leave blank to allow everyone to access this feature.', 'fb2wp-integration-tools');?>
		</p>
		<p><?php esc_html_e('Post tags', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_post_tags" size="30" value="<?php echo get_option("mxp_fb2wp_post_tags", ""); ?>" /><?php
/* translators: Ask the admin to separate post tags for synced posts with commas. */
esc_html_e('Use comma-saparated format', 'fb2wp-integration-tools');?>
		</p>
		<p><?php esc_html_e('Escape syncing posts with this hashtag: ', 'fb2wp-integration-tools');?>#
		<input type="text" name="mxp_fb2wp_no_post_tag" size="10" value="<?php echo get_option("mxp_fb2wp_no_post_tag", ""); ?>" />
		(<?php esc_html_e('Do not append # to the defined hashtag.', 'fb2wp-integration-tools');?>)
		</p>
		<p><?php esc_html_e('Deafault title: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_default_title" size="30" value="<?php echo get_option("mxp_fb2wp_default_title", esc_html__('-Synced from Facebook', 'fb2wp-integration-tools')); ?>" /><br />
		(<?php esc_html_e('The posts will scrape the first line of the Facebook post as the post title by default, but Default Title will be used when the first line or the post is empty.', 'fb2wp-integration-tools');?>)
		</p>
		<p><?php esc_html_e('Display attachments by default: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_default_display_attachment" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_attachment", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_default_display_attachment" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_attachment"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Display image captions by default: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_default_display_img_caption" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_img_caption", "no"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_default_display_img_caption" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_img_caption", "no"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Default image width: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_image_width" size="7" value="<?php echo get_option("mxp_fb2wp_image_width", ""); ?>" />
		</p>
		<p><?php esc_html_e('Default image height: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_image_height" size="7" value="<?php echo get_option("mxp_fb2wp_image_height", ""); ?>" />
		</p>
		<p><?php esc_html_e('Default video width: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_video_width" size="7" value="<?php echo get_option("mxp_fb2wp_video_width", "320"); ?>" />
		</p>
		<p><?php esc_html_e('Default video height: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb2wp_video_height" size="7" value="<?php echo get_option("mxp_fb2wp_video_height", "240"); ?>" /></p>
		<?php //附件短碼影片使用部分，還有其他預設參數：video_controls, video_preload, video_loop, video_autoplay （列為TODO之後參數化）?>
		<p><?php esc_html_e('Display embedded posts by default: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_default_display_embed" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_embed", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_default_display_embed" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_embed"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Footer contents for synced posts (HTML, JavaScript, CSS and shortcodes supported): ', 'fb2wp-integration-tools');?>
		<textarea name="mxp_fb2wp_post_footer" rows="3" cols="40"><?php echo get_option("mxp_fb2wp_post_footer", ""); ?></textarea>
		</p>
		<h2><?php esc_html_e('Sync Comments Setup', 'fb2wp-integration-tools');?></h2>
		<p><?php esc_html_e('To sync comments of posts shared to Facebook page back to the original posts.', 'fb2wp-integration-tools');?></p>
		<p><?php esc_html_e('Sync comments back: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_comment_mirror_enable" value="yes" <?php checked('yes', get_option("mxp_fb2wp_comment_mirror_enable", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_comment_mirror_enable" value="no" <?php checked('no', get_option("mxp_fb2wp_comment_mirror_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Comments approved by default: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb2wp_comment_mirror_approved" value="yes" <?php checked('yes', get_option("mxp_fb2wp_comment_mirror_approved", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb2wp_comment_mirror_approved" value="no" <?php checked('no', get_option("mxp_fb2wp_comment_mirror_approved"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
</div>
<div id="fb_plugin" class="container Section">
		<h2><?php esc_html_e('Facebook Plugins', 'fb2wp-integration-tools');?></h2>
		<p><?php esc_html_e('Block title: ', 'fb2wp-integration-tools');?>
		<input type="text" name="mxp_fb_functions_section_title" value="<?php echo get_option("mxp_fb_functions_section_title", esc_attr__('<h3>Facebook Plugins</h3>', 'fb2wp-integration-tools')); ?>">
		<?php esc_html_e('(HTML, JavaScript and CSS supoorted)', 'fb2wp-integration-tools');?>
		</p>
		<p><?php esc_html_e('Enable Quote Plugin: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_quote_enable" value="yes" <?php checked('yes', get_option("mxp_fb_quote_enable", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_quote_enable" value="no" <?php checked('no', get_option("mxp_fb_quote_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Enable Save Button', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_save_enable" value="yes" <?php checked('yes', get_option("mxp_fb_save_enable", "yes"));?>><label><?php printf(esc_html__('Enable', 'fb2wp-integration-tools') . ' ' . /* translators: the size of Save Button */esc_html__('(Large)', 'fb2wp-integration-tools'));?></label>
		<input type="radio" name="mxp_fb_save_enable" value="yes1" <?php checked('yes1', get_option("mxp_fb_save_enable", "yes"));?>><label><?php printf(esc_html__('Enable', 'fb2wp-integration-tools') . ' ' . /* translators: the size of Save Button */esc_html__('(Small)', 'fb2wp-integration-tools'));?></label>
		<input type="radio" name="mxp_fb_save_enable" value="no" <?php checked('no', get_option("mxp_fb_save_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Enable Embedded Comments: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_comments_enable" value="yes" <?php checked('yes', get_option("mxp_fb_comments_enable", "yes"));?>><label><?php printf(esc_html__('Enable', 'fb2wp-integration-tools') . ' ' . /* translators: the mode of Embedded Comments */esc_html__('(Integrated mode)', 'fb2wp-integration-tools'));?></label>
		<input type="radio" name="mxp_fb_comments_enable" value="yes1" <?php checked('yes1', get_option("mxp_fb_comments_enable", "yes"));?>><label><?php printf(esc_html__('Enable', 'fb2wp-integration-tools') . ' ' . /* translators: the mode of Embedded Comments */esc_html__('(Single mode)', 'fb2wp-integration-tools'));?></label>
		<input type="radio" name="mxp_fb_comments_enable" value="no" <?php checked('no', get_option("mxp_fb_comments_enable"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Enable Customer Chat Plugin', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_messenger_embed" value="show" <?php checked('show', get_option("mxp_fb_messenger_embed", "fade"));?>><label><?php esc_html_e('Show the dialog', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_messenger_embed" value="fade" <?php checked('fade', get_option("mxp_fb_messenger_embed", "fade"));?>><label><?php esc_html_e('Show the dialog with delay', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_messenger_embed" value="hide" <?php checked('hide', get_option("mxp_fb_messenger_embed", "fade"));?>><label><?php esc_html_e('Hide the dialog', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_messenger_embed" value="no" <?php checked('no', get_option("mxp_fb_messenger_embed", "fade"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label><br />
		(<a href="https://mxp.tw/oK" target="_blank"><?php esc_html_e('How to embed Messenger Messenger Bot with FB2WP (Chinese)', 'fb2wp-integration-tools');?></a>)
		<p><?php esc_html_e('Delays of showing the dialog (sec): ', 'fb2wp-integration-tools');?>
			<input type="number" name="mxp_fb_messenger_greeting_dialog_delay" value="<?php echo get_option("mxp_fb_messenger_greeting_dialog_delay", "5"); ?>" maxlength="3" size="3"/>
		</p>
		<p><?php esc_html_e('Logged in greeting: ', 'fb2wp-integration-tools');?>
			<input type="text" name="mxp_fb_messenger_logged_in_greeting" value="<?php echo get_option("mxp_fb_messenger_logged_in_greeting", esc_attr__('Hello! How can we help you?', 'fb2wp-integration-tools')); ?>" size="40"/><br /><?php esc_html_e('(Maximum 80 characters)', 'fb2wp-integration-tools');?>
		</p>
		<p><?php esc_html_e('Logged out greeting: ', 'fb2wp-integration-tools');?>
			<input type="text" name="mxp_fb_messenger_logged_out_greeting" value="<?php echo get_option("mxp_fb_messenger_logged_out_greeting", esc_attr__('Hello! How can we help you?', 'fb2wp-integration-tools')); ?>" size="40"/><br /><?php esc_html_e('(Maximum 80 characters)', 'fb2wp-integration-tools');?>
		</p>
		<p><?php esc_html_e('Theme color: ', 'fb2wp-integration-tools');?>
			#<input type="text" name="mxp_fb_messenger_theme_color" value="<?php echo get_option("mxp_fb_messenger_theme_color", ""); ?>" maxlength="8" size="8"/><br />
			<?php esc_html_e('Hexadecimal color code without #', 'fb2wp-integration-tools');?>
		</p>
		</p>
		<p><?php esc_html_e('Placement of Facebook Plugins', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_widget_place" value="up" <?php checked('up', get_option("mxp_fb_widget_place", "down"));?>><label><?php esc_html_e('Before the content', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_widget_place" value="down" <?php checked('down', get_option("mxp_fb_widget_place", "down"));?>><label><?php esc_html_e('After the content', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Clear Facebook URL Cache after updating posts', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_fb_clear_url_cache" value="yes" <?php checked('yes', get_option("mxp_fb_clear_url_cache", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_fb_clear_url_cache" value="no" <?php checked('no', get_option("mxp_fb_clear_url_cache", "yes"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
</div>
<div id="fb_ratings" class="container Section">
	<h3><?php esc_html_e('Facebook Page Ratings', 'fb2wp-integration-tools');?></h3>
	<p><?php
esc_html_e('1. Please subscribe to "ratings" event in your Facebook App before start using Facebook Page Ratings feature.', 'fb2wp-integration-tools');?></p>
	<p><?php
esc_html_e('2. Use shortcode [mxp_fb2wp_display_ratings] to display synced reviews from Facebook wherever on your site.', 'fb2wp-integration-tools');?></p>
	<p><?php
esc_html_e('3. The shortcode attribute "limit" will load 20 reviews by default. You may define other value, but too many requests may cause unexpected issues. Use "uid" to select certain user\'s review. Set "display_embed" attribute to "yes" to display the review in embed mode.', 'fb2wp-integration-tools');?></p>
	<p><?php
esc_html_e('4. Developers may customize "fb2wp_display_ratings" to redefine the structure for better theme design.', 'fb2wp-integration-tools');?></p>
	<p><?php
esc_html_e('5. Press the following button to import current ratings of your Facebook Page (at most 100 reviews)', 'fb2wp-integration-tools');?> <br /><input type="button" name="import_ratings" id="import_ratings" class="button button-primary" <?php echo get_option("mxp_fb2wp_rating_import", "") == "lock" ? "value='" . esc_html__('Ratings imported', 'fb2wp-integration-tools') . "'" : "value='" . esc_html__('Import ratings', 'fb2wp-integration-tools') . "'"; ?> <?php echo get_option("mxp_fb2wp_rating_import", "") == "lock" ? "disabled" : ""; ?>></p>
</div>
<div id="developer_function" class="container Section">
		<h3><?php esc_html_e('Developer Tools', 'fb2wp-integration-tools');?></h3>
		<p><?php esc_html_e('Delete the plugin as well as all settings: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_complete_remove" value="yes" <?php checked('yes', get_option("mxp_complete_remove", "no"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_complete_remove" value="no" <?php checked('no', get_option("mxp_complete_remove", "no"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Enable saving log files: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_enable_debug" value="yes" <?php checked('yes', get_option("mxp_enable_debug", "yes"));?>><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		<input type="radio" name="mxp_enable_debug" value="no" <?php checked('no', get_option("mxp_enable_debug"));?>><label><?php esc_html_e('Disable', 'fb2wp-integration-tools');?></label>
		</p>
		<p> <?php esc_html_e('Eliminate current logs: ', 'fb2wp-integration-tools');?>
		<input type="radio" name="mxp_remove_plugin_debug_log" value="yes"><label><?php esc_html_e('Enable', 'fb2wp-integration-tools');?></label>
		</p>
		<p><?php esc_html_e('Current logs: ', 'fb2wp-integration-tools');?>
		<?php
$del = get_option("mxp_remove_plugin_debug_log", "no");
$logs = $fb2wp['Mxp_FB2WP']->get_plugin_logs($del);
update_option("mxp_remove_plugin_debug_log", "no");
if (count($logs) == 0) {
	_e('No logs available.', 'fb2wp-integration-tools');
}
echo '<ul>';
for ($i = 0; $i < count($logs); ++$i) {
	echo '<li><a target="_blank" href="' . $logs[$i] . '">' . $logs[$i] . '</a></li>';
}
echo '</ul>';
?>
		</p>
</div>
 <input type="hidden" value="<?php echo $activebtn; ?>" name="mxp_fb2wp_active_tab" id="mxp_fb2wp_active_tab" />
 <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('mxp-fb2wp-main-setting-page'); ?>"/>
<p><input type="submit" id="save" value="<?php /* translators: Save the settings */esc_html_e('Save', 'fb2wp-integration-tools');?>" class="button action" /></p>
</form>
<p><?php esc_html_e('Current version: ', 'fb2wp-integration-tools');
echo Mxp_FB2WP::$version;?></p>
<p><?php esc_html_e('Contact developer: ', 'fb2wp-integration-tools');?><a href="https://www.mxp.tw/contact/" target="blank">江弘竣（阿竣）Chun</a></p>
<p><?php esc_html_e('Donate developer: ', 'fb2wp-integration-tools');?><a href="https://mxp.tw/lw" target="blank"><?php esc_html_e('Do you think the plugin helpful? Buy me a cup of coffee!', 'fb2wp-integration-tools');?></a></p>