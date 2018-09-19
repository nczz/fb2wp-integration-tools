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
		echo "<script>alert('「轉發文章Footer內容」中 請勿包含「mxp_fb2wp_display_embed」shortcode');</script>";
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
	&& isset($section_title) && isset($fb_widget_place) && isset($clear_cache) && isset($enable_messenger) && isset($msg_auth_users) && isset($msg_embed) && isset($mxp_remove_plugin_debug_log) && isset($mxp_active_tab) && isset($enable_pass_thread) && isset($pass_thread_btn_text) && isset($comment_mirror_enable) && isset($comment_mirror_approved) && isset($theme_color) && isset($logged_out_greeting) && isset($logged_in_greeting) && isset($greeting_dialog_delay)) {
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
	echo "更新成功！";
}
$rest_url = '';

if (get_option("mxp_fb2wp_callback_url") == "ERROR") {
	global $wp_version;
	$rest_url = 'WordPress 版本過低( v' . $wp_version . ' )，不支援 REST API 方法，請更新( v4.7 以後版本 )後再使用！';
} else {
	if (!is_ssl()) {
		//ref:https://developers.facebook.com/docs/graph-api/webhooks#setup
		$rest_url = 'Facebook API 自 v2.5 版後，不支援非安全連線的回呼 URL，請將網站升級安全連線 HTTPS 後再使用！';
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
    <h5>功能選單</h5>
  </div>
  <a href="javascript:void(0)" data-id="fbapp" class="<?php echo $tabs['fbapp']; ?>">Facebook App 設定</a>
  <a href="javascript:void(0)" data-id="webhooks" class="<?php echo $tabs['webhooks']; ?>">Facebook Webhooks 設定</a>
  <a href="javascript:void(0)" data-id="messenger" class="<?php echo $tabs['messenger']; ?>">Facebook 自動回覆設定</a>
  <a href="javascript:void(0)" data-id="post_to_wp" class="<?php echo $tabs['post_to_wp']; ?>">文章同步回 WordPress 設定</a>
  <a href="javascript:void(0)" data-id="fb_plugin" class="<?php echo $tabs['fb_plugin']; ?>">Facebook 外掛功能</a>
  <a href="javascript:void(0)" data-id="fb_ratings" class="<?php echo $tabs['fb_ratings']; ?>">Facebook 粉絲頁評價</a>
  <a href="javascript:void(0)" data-id="developer_function" class="<?php echo $tabs['developer_function']; ?>">開發者功能</a>
</div>
<div id="fbapp" class="container Section">
		<h2><a href="https://developers.facebook.com" target="_blank">Facebook App</a> 設定</h2>
		<form action="" method="POST">
		<p>應用程式編號：
		<input type="text" value="<?php echo get_option("mxp_fb_app_id"); ?>" name="mxp_fb_app_id" size="20" id="fb_app_id" />
		</p>
		<p>應用程式密鑰：
		<input type="text" value="<?php echo get_option("mxp_fb_secret"); ?>" name="mxp_fb_secret" size="36" id="fb_app_secret" />
		</p>
		<p>粉絲頁編號：
		<input type="text" value="<?php echo get_option("mxp_fb_page_id"); ?>" name="mxp_fb_page_id" size="20" id="mxp_fb_page_id" />
		</p>
		<p>粉絲頁應用程式授權碼：
		<input type="text" value="<?php echo get_option("mxp_fb_app_access_token"); ?>" name="mxp_fb_app_access_token" size="60" id="fb_app_access_token" />(<a href="https://tw.wordpress.org/plugins/fb2wp-integration-tools/faq/" target="_blank" >Q&A</a>)
		</p>
		<p>啟用 Facebook JavaScript SDK：
		<input type="radio" name="mxp_fb_enable_jssdk" value="yes" <?php checked('yes', get_option("mxp_fb_enable_jssdk", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb_enable_jssdk" value="no" <?php checked('no', get_option("mxp_fb_enable_jssdk"));?>>否</input>(建議啟用)
		</p>
		<p>設定 SDK 語言：
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
echo '<input type="text" value="' . get_option("mxp_fb_jssdk_local", "zh_TW") . '" size="7" name="mxp_fb_jssdk_local"/>';
?>
		</p>
		<p>設定 SDK 版本：
		<input type="text" size="7" value="<?php echo get_option("mxp_fb_api_version", "v2.11"); ?>" name="mxp_fb_api_version">
		</p>
</div>
<div id="webhooks" class="container Section">
		<h2>Facebook App 設定 Webhooks</h2>
		<p>請將下列連結填入 App 應用程式 Webhooks 分頁中 Page 粉絲頁訂閱之回呼網址</p>
		<p>
		<input type="text" size="100" value="<?php echo $rest_url; ?>" id="fb_app_webhooks_callback_url" disabled />
		</p>
		<p>並訂閱：messages, messaging_postbacks, standby, messaging_handovers, conversations, feed, ratings 事件</p>
		<p>回呼驗證權杖：
		<input type="text" value="<?php echo get_option("mxp_fb_webhooks_verify_token"); ?>" name="mxp_fb_webhooks_verify_token">
		</p>
</div>
<div id="messenger" class="container Section">
		<h2>訊息自動回覆設定</h2>
		<p>是否啟用：
		<input type="radio" name="mxp_fb2wp_messenger_enable" value="open" <?php checked('open', get_option("mxp_fb2wp_messenger_enable", "open"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_messenger_enable" value="closed" <?php checked('closed', get_option("mxp_fb2wp_messenger_enable"));?>>否</input>
		</p>
		<p>啟用後，請至粉絲頁的「設定」，「Messenger 平台」中將「回覆有部分是自動操作，並以部分人工操作輔助」勾選以及「應用程式 Page Inbox 263902037430900」設定為「Secondary Receiver」，即可讓使用者選擇切換對話角色為機器人或粉絲頁管理員。</p>
		<p>限定 Facebook 使用者測試：
		<input type="text" name="mxp_fb2wp_messenger_auth_users" size="30" value="<?php echo get_option("mxp_fb2wp_messenger_auth_users", ""); ?>" />（逗點（,）分隔 Facebook 用戶訊息發送 ID，不填入則表示所有人皆授權許可）
		</p>
		<p>若無比對到訊息時的預設回覆：
		<textarea name="mxp_messenger_default_reply" rows="3" cols="30"><?php echo get_option("mxp_messenger_default_reply"); ?></textarea></br>（可使用 [mxp_input_msg] 關鍵字帶入原使用者輸入句，例如：「您好，無法識別【[mxp_input_msg]】這項指令，請重新輸入，謝謝！ 」）</p>
		<p>是否啟用<a href="https://developers.facebook.com/docs/messenger-platform/handover-protocol" target="_blank">交接模式</a>切換按鈕：
		<input type="radio" name="mxp_fb2wp_messenger_enable_pass_thread" value="yes" <?php checked('yes', get_option("mxp_fb2wp_messenger_enable_pass_thread", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_messenger_enable_pass_thread" value="no" <?php checked('no', get_option("mxp_fb2wp_messenger_enable_pass_thread"));?>>否</input>
		</p>
		<p>交接模式切換按鈕顯示文字：
		<input type="text" name="mxp_fb2wp_messenger_enable_pass_thread_btn_text" size="30" value="<?php echo get_option("mxp_fb2wp_messenger_enable_pass_thread_btn_text", "點擊此處後留言通知管理員"); ?>" />
		</p>
</div>
<div id="post_to_wp" class="container Section">
		<h2>文章同步回 WordPress 設定</h2>
		<p>是否啟用：
		<input type="radio" name="mxp_fb2wp_post_enable" value="open" <?php checked('open', get_option("mxp_fb2wp_post_enable", "open"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_post_enable" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_enable"));?>>否</input>
		</p>
		</p>
		<p>發文使用者：
		<?php wp_dropdown_users(array('name' => 'mxp_fb2wp_post_author', 'selected' => get_option("mxp_fb2wp_post_author", "1")));?>
		</p>
		<p>發文分類：
		<?php wp_dropdown_categories(array('name' => 'mxp_fb2wp_post_category', 'hide_empty' => 0, 'selected' => get_option("mxp_fb2wp_post_category", "1")));?>
		</p>
		<p>發文能見度狀態：
		<select name="mxp_fb2wp_post_status">
		<option value="publish" <?php selected(get_option("mxp_fb2wp_post_status"), "publish");?>>已發表</option>
		<option value="pending" <?php selected(get_option("mxp_fb2wp_post_status"), "pending");?>>待審中</option>
		<option value="draft" <?php selected(get_option("mxp_fb2wp_post_status", "draft"), "draft");?>>草稿</option>
		<option value="private" <?php selected(get_option("mxp_fb2wp_post_status"), "private");?>>私密</option>
		</select>
		</p>
		<p>允許迴響：
		<input type="radio" name="mxp_fb2wp_post_comment_status" value="open" <?php checked('open', get_option("mxp_fb2wp_post_comment_status", "open"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_post_comment_status" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_comment_status"));?>>否</input>
		</p>
		<p>允許通告：
		<input type="radio" name="mxp_fb2wp_post_ping_status" value="open" <?php checked('open', get_option("mxp_fb2wp_post_ping_status", "open"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_post_ping_status" value="closed" <?php checked('closed', get_option("mxp_fb2wp_post_ping_status"));?>>否</input>
		</p>
		<p>文章類型：
		<?php
$ps = get_post_types(array('public' => true));
echo '<select name="mxp_fb2wp_post_type">';
foreach ($ps as $key => $value) {
	echo '<option value="' . $value . '"' . selected(get_option("mxp_fb2wp_post_type", "post"), $value) . '>' . $value . '</option>';
}
echo '</select>';
?>
		</p>
		<p>限定 Facebook 使用者投稿：
		<input type="text" name="mxp_fb2wp_auth_users" size="30" value="<?php echo get_option("mxp_fb2wp_auth_users", ""); ?>" />（逗點（,）分隔 Facebook 使用者 ID，不填入則表示所有人皆授權許可）
		</p>
		<p>發文標籤：
		<input type="text" name="mxp_fb2wp_post_tags" size="30" value="<?php echo get_option("mxp_fb2wp_post_tags", ""); ?>" />（逗點（,）分隔）
		</p>
		<p>停止該篇轉發文章標籤：#
		<input type="text" name="mxp_fb2wp_no_post_tag" size="10" value="<?php echo get_option("mxp_fb2wp_no_post_tag", ""); ?>" />（輸入不需加#號）
		</p>
		<p>替代標題：
		<input type="text" name="mxp_fb2wp_default_title" size="30" value="<?php echo get_option("mxp_fb2wp_default_title", "-FB轉發文章"); ?>" />（文章抓取時使用FB發文的第一行視為標題，若第一行或內文為空，則帶入替代標題）
		</p>
		<p>預設顯示附件：
		<input type="radio" name="mxp_fb2wp_default_display_attachment" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_attachment", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_default_display_attachment" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_attachment"));?>>否</input>
		</p>
		<p>預設顯示圖片描述：
		<input type="radio" name="mxp_fb2wp_default_display_img_caption" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_img_caption", "no"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_default_display_img_caption" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_img_caption", "no"));?>>否</input>
		</p>
		<p>預設圖片寬度：
		<input type="text" name="mxp_fb2wp_image_width" size="7" value="<?php echo get_option("mxp_fb2wp_image_width", ""); ?>" />
		</p>
		<p>預設圖片高度：
		<input type="text" name="mxp_fb2wp_image_height" size="7" value="<?php echo get_option("mxp_fb2wp_image_height", ""); ?>" />
		</p>
		<p>預設影片寬度：
		<input type="text" name="mxp_fb2wp_video_width" size="7" value="<?php echo get_option("mxp_fb2wp_video_width", "320"); ?>" />
		</p>
		<p>預設影片高度：<input type="text" name="mxp_fb2wp_video_height" size="7" value="<?php echo get_option("mxp_fb2wp_video_height", "240"); ?>" /></p>
		<p>附件短碼影片使用部分，還有其他預設參數：video_controls, video_preload, video_loop, video_autoplay （列為TODO之後參數化）</p>
		<p>預設顯示嵌入文章：
		<input type="radio" name="mxp_fb2wp_default_display_embed" value="yes" <?php checked('yes', get_option("mxp_fb2wp_default_display_embed", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_default_display_embed" value="no" <?php checked('no', get_option("mxp_fb2wp_default_display_embed"));?>>否</input>
		</p>
		<p>轉發文章Footer內容：
		<textarea name="mxp_fb2wp_post_footer" rows="3" cols="40"><?php echo get_option("mxp_fb2wp_post_footer", ""); ?></textarea>（支援 HTML, JavaScript, CSS and shortcode）
		</p>
		<h2>留言同步回 WordPress 文章設定</h2>
		<p>文章分享至粉絲頁時，同步粉絲留言內容回網站該篇文章。</p>
		<p>是否啟用：
		<input type="radio" name="mxp_fb2wp_comment_mirror_enable" value="yes" <?php checked('yes', get_option("mxp_fb2wp_comment_mirror_enable", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_comment_mirror_enable" value="no" <?php checked('no', get_option("mxp_fb2wp_comment_mirror_enable"));?>>否</input>
		</p>
		<p>留言預設發佈：
		<input type="radio" name="mxp_fb2wp_comment_mirror_approved" value="yes" <?php checked('yes', get_option("mxp_fb2wp_comment_mirror_approved", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb2wp_comment_mirror_approved" value="no" <?php checked('no', get_option("mxp_fb2wp_comment_mirror_approved"));?>>否</input>
		</p>
</div>
<div id="fb_plugin" class="container Section">
		<h2>Facebook 外掛功能</h2>
		<p>區塊標題：
		<input type="text" name="mxp_fb_functions_section_title" value="<?php echo get_option("mxp_fb_functions_section_title", "<h3>Facebook 功能：</h3>"); ?>">（支援 HTML, JavaScript and CSS)
		</p>
		<p>啟用文章引言分享：
		<input type="radio" name="mxp_fb_quote_enable" value="yes" <?php checked('yes', get_option("mxp_fb_quote_enable", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb_quote_enable" value="no" <?php checked('no', get_option("mxp_fb_quote_enable"));?>>否</input>
		</p>
		<p>啟用文章儲存：
		<input type="radio" name="mxp_fb_save_enable" value="yes" <?php checked('yes', get_option("mxp_fb_save_enable", "yes"));?>>是(大按鈕) </input>
		<input type="radio" name="mxp_fb_save_enable" value="yes1" <?php checked('yes1', get_option("mxp_fb_save_enable", "yes"));?>>是(小按鈕) </input>
		<input type="radio" name="mxp_fb_save_enable" value="no" <?php checked('no', get_option("mxp_fb_save_enable"));?>>否</input>
		</p>
		<p>啟用文章留言：
		<input type="radio" name="mxp_fb_comments_enable" value="yes" <?php checked('yes', get_option("mxp_fb_comments_enable", "yes"));?>>是(共存模式) </input>
		<input type="radio" name="mxp_fb_comments_enable" value="yes1" <?php checked('yes1', get_option("mxp_fb_comments_enable", "yes"));?>>是(單一模式) </input>
		<input type="radio" name="mxp_fb_comments_enable" value="no" <?php checked('no', get_option("mxp_fb_comments_enable"));?>>否</input>
		</p>
		<p>網站嵌入粉絲頁 Messenger 顧客聊天：
		<input type="radio" name="mxp_fb_messenger_embed" value="show" <?php checked('show', get_option("mxp_fb_messenger_embed", "fade"));?>>展開對話框 </input>
		<input type="radio" name="mxp_fb_messenger_embed" value="fade" <?php checked('fade', get_option("mxp_fb_messenger_embed", "fade"));?>>延遲展開對話框 </input>
		<input type="radio" name="mxp_fb_messenger_embed" value="hide" <?php checked('hide', get_option("mxp_fb_messenger_embed", "fade"));?>>關閉對話框 </input>
		<input type="radio" name="mxp_fb_messenger_embed" value="no" <?php checked('no', get_option("mxp_fb_messenger_embed", "fade"));?>>不啟用 </input>(<a href="https://goo.gl/zXU8rH" target="_blank">參考使用 FB2WP 將臉書 Messenger 聊天機器人加入 WordPress 網站教學</a>)
		<p>Messenger 顧客聊天延遲展開對話框秒數：
			<input type="number" name="mxp_fb_messenger_greeting_dialog_delay" value="<?php echo get_option("mxp_fb_messenger_greeting_dialog_delay", "5"); ?>" maxlength="3" size="3"/>
		</p>
		<p>Messenger 顧客聊天登入狀態顯示訊息：
			<input type="text" name="mxp_fb_messenger_logged_in_greeting" value="<?php echo get_option("mxp_fb_messenger_logged_in_greeting", "你好，歡迎透過訊息聯絡我們！"); ?>" size="40"/>（最多 80 字元）
		</p>
		<p>Messenger 顧客聊天登出狀態顯示訊息：
			<input type="text" name="mxp_fb_messenger_logged_out_greeting" value="<?php echo get_option("mxp_fb_messenger_logged_out_greeting", "你好，歡迎透過訊息聯絡我們！"); ?>" size="40"/>（最多 80 字元）
		</p>
		<p>Messenger 顧客聊天主題顏色：
			#<input type="text" name="mxp_fb_messenger_theme_color" value="<?php echo get_option("mxp_fb_messenger_theme_color", ""); ?>" maxlength="8" size="8"/>（HEX 色碼格式，無需 # 字號）
		</p>
		</p>
		<p>外掛區塊顯示位置：
		<input type="radio" name="mxp_fb_widget_place" value="up" <?php checked('up', get_option("mxp_fb_widget_place", "down"));?>>內容上方 </input>
		<input type="radio" name="mxp_fb_widget_place" value="down" <?php checked('down', get_option("mxp_fb_widget_place", "down"));?>>內容下方 </input>
		</p>
		<p>更新文章時同步清除 Facebook 快取：
		<input type="radio" name="mxp_fb_clear_url_cache" value="yes" <?php checked('yes', get_option("mxp_fb_clear_url_cache", "yes"));?>>是 </input>
		<input type="radio" name="mxp_fb_clear_url_cache" value="no" <?php checked('no', get_option("mxp_fb_clear_url_cache", "yes"));?>>否 </input>
		</p>
</div>
<div id="fb_ratings" class="container Section">
	<h2>Facebook 粉絲頁評價</h2>
	<p>1. 使用粉絲頁評價同步功能前，請先記得於 Facebook App 中增加 「ratings」 的 Webhooks 事件訂閱。</p>
	<p>2. 前端輸出同步回網站的評價請使用「[mxp_fb2wp_display_ratings]」短碼，安插在任何希望呈現的角落。</p>
	<p>3. 短碼參數「limit」預設讀取 20 筆評價，可隨意指定數量（數值不建議過大，會有效能問題），「uid」指定某位 Facebook 使用者評價，最後是「display_embed」輸入「yes」值則會顯示該篇評價嵌入模式。</p>
	<p>4. 樣式可藉由「fb2wp_display_ratings」這個事件由開發者重新自由定義顯示結構，以符合主題設計。</p>
	<p>5. 匯入現有粉絲頁中的評價（最多 100 篇），請點擊此按鈕 > <input type="button" name="import_ratings" id="import_ratings" class="button button-primary" <?php echo get_option("mxp_fb2wp_rating_import", "") == "lock" ? "value='已匯入'" : "value='匯入'"; ?> <?php echo get_option("mxp_fb2wp_rating_import", "") == "lock" ? "disabled" : ""; ?>></p>
</div>
<div id="developer_function" class="container Section">
		<h2>開發者功能</h2>
		<p>刪除外掛時連帶全部設定資料：
		<input type="radio" name="mxp_complete_remove" value="yes" <?php checked('yes', get_option("mxp_complete_remove", "no"));?>>是 </input>
		<input type="radio" name="mxp_complete_remove" value="no" <?php checked('no', get_option("mxp_complete_remove", "no"));?>>否</input>
		</p>
		<p>Log文件記錄：
		<input type="radio" name="mxp_enable_debug" value="yes" <?php checked('yes', get_option("mxp_enable_debug", "yes"));?>>是 </input>
		<input type="radio" name="mxp_enable_debug" value="no" <?php checked('no', get_option("mxp_enable_debug"));?>>否</input>
		</p>
		<p>清除目前記錄檔案：
		<input type="radio" name="mxp_remove_plugin_debug_log" value="yes">是 </input>
		</p>
		<p>目前記錄檔：
		<?php
$del = get_option("mxp_remove_plugin_debug_log", "no");
$logs = $fb2wp['Mxp_FB2WP']->get_plugin_logs($del);
update_option("mxp_remove_plugin_debug_log", "no");
if (count($logs) == 0) {
	echo '無';
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
<p><input type="submit" id="save" value="儲存" class="button action" /></p>
</form>
<p>當前版本：<?php echo Mxp_FB2WP::$version; ?></p>
<p>聯絡作者：<a href="https://www.mxp.tw/contact/" target="blank">江弘竣（阿竣）</a></p>
<p>贊助作者：<a href="https://goo.gl/XQYSq1" target="blank">覺得有幫助嗎？請作者喝一杯咖啡吧！</a></p>