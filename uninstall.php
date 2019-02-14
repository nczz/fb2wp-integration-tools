<?php
/*
 * plugin should create a file named ‘uninstall.php’ in the base plugin folder. This file will be called, if it exists,
 * during the uninstall process bypassing the uninstall hook.
 * ref: https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}
if (get_option("mxp_complete_remove", "no") == "yes") {
	global $wpdb;
	$wpdb->query("DROP TABLE {$wpdb->prefix}fb2wp_debug");
	delete_option("mxp_fb2wp_db_version");
	delete_option("mxp_fb_app_id");
	delete_option("mxp_fb_secret");
	delete_option("mxp_fb_app_access_token");
	delete_option("mxp_fb_enable_jssdk");
	delete_option("mxp_fb_jssdk_local");
	delete_option("mxp_fb_api_version");
	delete_option("mxp_enable_debug");
	delete_option("mxp_fb2wp_callback_url");
	delete_option("mxp_messenger_msglist");
	delete_option("mxp_messenger_default_reply");
	delete_option("mxp_fb2wp_post_enable");
	delete_option("mxp_fb2wp_post_author");
	delete_option("mxp_fb2wp_post_category");
	delete_option("mxp_fb2wp_post_status");
	delete_option("mxp_fb2wp_post_comment_status");
	delete_option("mxp_fb2wp_post_ping_status");
	delete_metadata(get_option("mxp_fb2wp_post_type", "post"), 0, 'mxp_fb2wp_post_id', '', true);
	delete_metadata(get_option("mxp_fb2wp_post_type", "post"), 0, 'mxp_fb2wp_item', '', true);
	delete_metadata(get_option("mxp_fb2wp_post_type", "post"), 0, 'mxp_fb2wp_sender', '', true);
	delete_option("mxp_fb2wp_post_type");
	delete_option("mxp_fb2wp_auth_users");
	delete_option("mxp_fb2wp_default_title");
	delete_option("mxp_fb2wp_post_tags");
	delete_option("mxp_fb2wp_default_display_attachment");
	delete_option("mxp_fb2wp_default_display_embed");
	delete_option("mxp_fb2wp_image_width");
	delete_option("mxp_fb2wp_image_height");
	delete_option("mxp_fb2wp_video_width");
	delete_option("mxp_fb2wp_video_height");
	delete_option("mxp_fb2wp_post_footer");
	delete_option("mxp_fb2wp_no_post_tag");
	delete_option("mxp_fb_quote_enable");
	delete_option("mxp_fb_save_enable");
	delete_option("mxp_fb_send_enable");
	delete_option("mxp_fb_comments_enable");
	delete_option("mxp_complete_remove");
	delete_option("mxp_fb_page_id"); //add from 1.4.4
	delete_option("mxp_fb_functions_section_title"); //add from 1.4.5
	delete_option("mxp_fb_widget_place"); //add from 1.5.0
	delete_option("mxp_fb2wp_rating_import"); //add from 1.5.3
	delete_option("mxp_fb_clear_url_cache"); //add from 1.5.4
	delete_option("mxp_fb2wp_messenger_enable"); //add from 1.5.6
	delete_option("mxp_fb2wp_messenger_auth_users"); //add from 1.5.6
	delete_option("mxp_fb_messenger_embed"); //add from 1.5.7
	delete_option("mxp_remove_plugin_debug_log"); //add from 1.5.7
	delete_option("mxp_fb2wp_active_tab"); //add from 1.5.7
	delete_option("mxp_fb2wp_messenger_enable_pass_thread"); // add from 1.6.0
	delete_option("mxp_fb2wp_messenger_enable_pass_thread_btn_text"); // add from 1.6.0
	delete_option("mxp_fb2wp_comment_mirror_enable"); // add from 1.7.0
	delete_option("mxp_fb2wp_comment_mirror_approved"); // add from 1.7.0
	delete_option("mxp_fb_messenger_greeting_dialog_delay"); // add from 1.7.1
	delete_option("mxp_fb_messenger_logged_in_greeting"); // add from 1.7.1
	delete_option("mxp_fb_messenger_logged_out_greeting"); // add from 1.7.1
	delete_option("mxp_fb_messenger_theme_color"); // add from 1.7.1
}