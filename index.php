<?php
/**
 * Plugin Name: FB2WP integration tools - Mxp.TW
 * Plugin URI: https://tw.wordpress.org/plugins/fb2wp-integration-tools/
 * Description: The best Facebook Webhooks integration plugin ever! This plugin integrates the following features: Facebook Reviews, Automated bots, Facebook-WordPress posts synchronization and Facebook Page plugins etc. This plugin also allows developers to use powerful Hooks to connect Facebook Page comments and messages.
 * Version: 1.8.1
 * Author: Chun
 * Author URI: https://www.mxp.tw/contact/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  fb2wp-integration-tools
 * Domain Path:  /languages/
 */

if (!defined('WPINC')) {
	die;
}

if (!class_exists('Mxp_FB2WP_API')) {
	include_once plugin_dir_path(__FILE__) . "rest_api.php";
}

class Mxp_FB2WP {
	static $version = '1.8.1';
	protected static $instance = null;
	protected static $rest_api = null;
	public $slug = 'mxp-fb2wp';

	/*
		Core Functions
	*/
	private function __construct() {
		//check if install or not
		$ver = get_option("mxp_fb2wp_db_version");
		if (!isset($ver) || $ver == "") {
			$this->install();
		} else if (version_compare(self::$version, $ver, '>')) {
			$this->update($ver);
		}
		$this->init();
	}

	public static function get_instance() {
		global $wp_version;
		// REST API (WP_REST_Controller) was included starting WordPress 4.7.
		if (!isset(self::$rest_api) && version_compare($wp_version, '4.7', '>=')) {
			self::$rest_api = new Mxp_FB2WP_API();
			add_action('rest_api_init', array(__CLASS__, 'register_facebook_webhooks'));
			update_option("mxp_fb2wp_callback_url", '/' . self::$rest_api->get_namespace_var() . '/' . self::$rest_api->get_rest_base_var());
		} else {
			update_option("mxp_fb2wp_callback_url", 'ERROR');
		}

		if (!isset(self::$instance) && is_super_admin()) {
			self::$instance = new self;
		}
		self::register_public_action();
		return array('Mxp_FB2WP' => self::$instance, 'Mxp_FB2WP_API' => self::$rest_api);
	}

	private function init() {
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
		add_action('admin_enqueue_scripts', array($this, 'load_assets'));
		add_action('admin_menu', array($this, 'create_plugin_menu'));
		add_action('transition_post_status', array($this, 'mxp_update_facebook_url_cache'), 10, 3);
		add_action('wp_ajax_mxp_messenger_settings_save', array($this, 'mxp_messenger_settings_save'));
		add_action('wp_ajax_mxp_import_fb_ratings', array($this, 'mxp_import_fb_ratings'));
		add_action('wp_ajax_mxp_debug_record_action', array($this, 'mxp_debug_record_action'));
		//FB 死活都修不好這個是怎樣勒？
		//$this->get_fb_locals();
	}

	public static function register_public_action() {
		if (get_option("mxp_fb_enable_jssdk", "yes") == "yes") {
			add_action('wp_head', array(__CLASS__, 'setting_fb_sdk'));
		}
		add_action('wp_head', array(__CLASS__, 'add_generator'));
		add_shortcode('mxp_fb2wp_display_attachment', array(__CLASS__, 'register_attachment_shortcode'));
		add_shortcode('mxp_fb2wp_display_embed', array(__CLASS__, 'register_embed_shortcode'));
		add_shortcode('mxp_fb2wp_display_ratings', array(__CLASS__, 'display_ratings_shortcode'));
		add_action('wp_footer', array(__CLASS__, 'register_facebook_quote'));
		add_action('wp_footer', array(__CLASS__, 'register_facebook_messenger_embed'));
		add_filter('the_content', array(__CLASS__, 'register_facebook_save'));
		add_action('comments_template', array(__CLASS__, 'register_facebook_comment'), 1);
		add_filter('comments_template', array(__CLASS__, 'overwrite_default_comment'));
		/**
		 * Load plugin textdomain. Thanks Eric!
		 *
		 * @since 1.7.8
		 */
		load_plugin_textdomain('fb2wp-integration-tools', false, basename(dirname(__FILE__)) . '/languages');
	}

	public function add_action_links($links) {
		$mxp_links = array(
			/* translators: To sponsor the original developer of the plugin that shows on the plugin list.*/
			'<a href="https://goo.gl/XQYSq1" target="blank"><font color=red>' . __('Donate', 'fb2wp-integration-tools') . '</font></a>',
		);
		return array_merge($links, $mxp_links);
	}
	private function install() {
		global $wpdb;
		$collate = '';

		if ($wpdb->has_cap('collation')) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
		CREATE TABLE {$wpdb->prefix}fb2wp_debug (
		  sid bigint(20) NOT NULL AUTO_INCREMENT,
		  created_time bigint(32) NOT NULL,
		  sender bigint(32) NOT NULL,
		  sender_name varchar(255) NULL,
		  action varchar(20) NOT NULL,
		  item varchar(20) NOT NULL,
		  post_id varchar(255) NOT NULL,
		  message longtext NULL,
		  source_json longtext NOT NULL,
		  PRIMARY KEY  (sid)
		) $collate;";
		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta($tables);
		add_option("mxp_fb2wp_db_version", self::$version);
	}

	private function update($ver) {
		include plugin_dir_path(__FILE__) . "update.php";
		$res = Mxp_Update::apply_update($ver);
		if ($res == true) {
			update_option("mxp_fb2wp_db_version", self::$version);
		} else {
			// v1.5.5 修正可能會導致更新失敗的錯誤：未定義 deactivate_plugins 方法
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins(plugin_basename(__FILE__));
			//更新失敗的TODO:聯絡我～回報錯誤
			wp_die(esc_html__('Oops, update failed. Please email to im@mxp.tw, and tell me in which version did you fail to update. You may check whether there is any error message in Console tab with Chrome DevTools.', 'fb2wp-integration-tools'), 'Q_Q|||');
		}

	}

	/*
		public methods
	*/
	public function create_plugin_menu() {
		add_menu_page(__('Mxp.TW FB Toolbox', 'fb2wp-integration-tools'), __('FB2WP Settings', 'fb2wp-integration-tools'), 'administrator', $this->slug, array($this, 'main_page_cb'), 'dashicons-admin-generic');
		add_submenu_page($this->slug, esc_html__('Message Settings', 'fb2wp-integration-tools'), __('Message Settings', 'fb2wp-integration-tools'), 'administrator', $this->slug . '-message', array($this, 'message_page_cb'));
		add_submenu_page($this->slug, esc_html__('Webhooks Logs', 'fb2wp-integration-tools'), __('Webhooks Logs', 'fb2wp-integration-tools'), 'administrator', $this->slug . '-post', array($this, 'post_page_cb'));
	}

	public function page_wraper($title, $cb) {
		echo '<div class="wrap" id="mxp"><h1>' . $title . '</h1>';
		call_user_func($cb);
		echo '</div>';
	}

	public function main_page_cb() {
		$this->page_wraper(esc_html__('Facebook Toolbox Settings', 'fb2wp-integration-tools'), function () {
			include plugin_dir_path(__FILE__) . "views/main.php";
		});
		wp_localize_script($this->slug . '-main-page', 'MXP_FB2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
			'importRat' => esc_html__('Importing Facebook ratings...', 'fb2wp-integration-tools'),
			'successMsg' => esc_html__('Imported successfully!', 'fb2wp-integration-tools'),
		));
		wp_enqueue_script($this->slug . '-main-page');
		wp_enqueue_style($this->slug . '-main-page-style');
	}

	public function message_page_cb() {
		$this->page_wraper(esc_html__('Message settings', 'fb2wp-integration-tools'), function () {
			include plugin_dir_path(__FILE__) . "views/message.php";
		});
		wp_localize_script($this->slug . '-message-page', 'MXP_FB2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
			'waitMe' => esc_html__('Loading...', 'fb2wp-integration-tools'),
			'removeItem' => esc_html__('Remove match', 'fb2wp-integration-tools'),
			/* translators: The input message sent from users. */
			'inputMatch' => esc_html__('Input match: ', 'fb2wp-integration-tools'),
			/* translators: The replying message sent from Automated bots. */
			'matchReply' => esc_html__('Replying message: ', 'fb2wp-integration-tools'),
			'errorMsg' => esc_html__('Errors occurred', 'fb2wp-integration-tools'),
			'successMsg' => esc_html__('Saved successfully!', 'fb2wp-integration-tools'),
		));
		wp_enqueue_script($this->slug . '-message-page');
		wp_enqueue_script($this->slug . '-loading-script');
		wp_enqueue_style($this->slug . '-loading-style');
	}

	public function post_page_cb() {
		$this->page_wraper(__('Webhooks Logs', 'fb2wp-integration-tools'), function () {
			include plugin_dir_path(__FILE__) . "views/post.php";
		});
		wp_localize_script($this->slug . '-post-page', 'MXP_FB2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
			'waitMe' => esc_html__('Loading...', 'fb2wp-integration-tools'),
			'removeBtn' => esc_html__('Remove this page', 'fb2wp-integration-tools'),
			'remove' => esc_html__('Remove', 'mxp-fb2wp'),
			'searchBtn' => esc_html__('Search', 'fb2wp-integration-tools'),
			'searchTerm' => esc_html__('Search terms', 'fb2wp-integration-tools'),
			'action' => esc_html__('Actions', 'mxp-fb2wp'),
			'time' => esc_html__('Time', 'fb2wp-integration-tools'),
			'object' => esc_html__('Objects', 'fb2wp-integration-tools'),
			'sender' => esc_html__('Targets', 'fb2wp-integration-tools'),
			'msg' => esc_html__('Messages', 'fb2wp-integration-tools'),
			'postBtn' => esc_html__('Publish', 'fb2wp-integration-tools'),
			'empty' => esc_html__('No contents', 'fb2wp-integration-tools'),
		));
		wp_enqueue_script($this->slug . '-post-page');
		wp_enqueue_script($this->slug . '-loading-script');
		wp_enqueue_style($this->slug . '-loading-style');
	}

	public function load_assets() {
		wp_register_script($this->slug . '-main-page', plugin_dir_url(__FILE__) . 'views/js/main.js', array('jquery'), self::$version, false);
		wp_register_script($this->slug . '-message-page', plugin_dir_url(__FILE__) . 'views/js/message.js', array('jquery'), self::$version, false);
		wp_register_script($this->slug . '-post-page', plugin_dir_url(__FILE__) . 'views/js/post.js', array('jquery'), self::$version, false);
		wp_register_script($this->slug . '-loading-script', plugin_dir_url(__FILE__) . 'views/js/waitMe.min.js', array('jquery'), self::$version, false);
		wp_register_style($this->slug . '-loading-style', plugin_dir_url(__FILE__) . 'views/css/waitMe.min.css', self::$version, false);
		wp_register_style($this->slug . '-main-page-style', plugin_dir_url(__FILE__) . 'views/css/main.css', self::$version, false);
	}

	public static function register_attachment_shortcode($atts) {
		extract(shortcode_atts(array(
			'id' => '',
			'class' => '',
			'src' => '',
			'mime_type' => '',
			'title' => '',
			'body' => '',
			'video_height' => '',
			'video_width' => '',
			'video_autoplay' => 'no',
			'video_loop' => 'no',
			'video_preload' => 'auto', //auto|metadata|none
			'video_controls' => 'yes',
			'display' => 'yes',
			'image_display_caption' => 'yes',
			'image_width' => '',
			'image_height' => '',
		), $atts, 'mxp_fb2wp_display_attachment'));
		if ($src == "" || $mime_type == "" || $display != "yes") {
			return '';
		}
		$title = base64_decode($title);
		$body = base64_decode($body);
		$type = explode("/", $mime_type);
		switch ($type[0]) {
		case 'image':
			$id = $id != '' ? 'id="' . esc_attr($id) . '"' : '';
			$image_width = $image_width != '' ? 'width="' . esc_attr($image_width) . '"' : 'width="' . esc_attr(get_option("mxp_fb2wp_image_width", "")) . '"';
			$image_height = $image_height != '' ? 'width="' . esc_attr($image_height) . '"' : 'width="' . esc_attr(get_option("mxp_fb2wp_image_height", "")) . '"';
			$html5 = "<figure {$id} class='mxp-fb2wp facebook-image " . esc_attr($class) . "'><img src='" . esc_url($src) . "' alt='" . esc_attr($title) . "' {$image_width} {$image_height} />";
			if ($body != "" && $image_display_caption == "yes") {
				$html5 .= "<figcaption>" . esc_html($body) . "</figcaption>";
			}
			$html5 .= "</figure>";
			return $html5;
			break;
		case 'video':
			$video_width = $video_width != '' ? esc_attr($video_width) : esc_attr(get_option("mxp_fb2wp_video_width", "320"));
			$video_height = $video_height != '' ? esc_attr($video_height) : esc_attr(get_option("mxp_fb2wp_video_height", "240"));
			$video_controls = $video_controls == 'yes' ? 'controls' : '';
			$video_autoplay = $video_autoplay == 'yes' ? 'autoplay' : '';
			$video_loop = $video_loop == 'yes' ? 'loop' : '';
			$video_preload = $video_preload != '' ? 'preload="' . esc_attr($video_preload) . '"' : '';
			$html5 = "<video width='{$video_width}' height='{$video_height}' alt='" . esc_attr($body) . "' title='" . esc_attr($title) . "' {$video_preload} {$video_controls} {$video_autoplay} {$video_loop}><source src='" . esc_url($src) . "' type='" . esc_attr($mime_type) . "'>您的瀏覽器不支援使用HTML5 video 標籤播放影片</video>";
			return $html5;
			break;
		default:
			return '';
			break;
		}

	}

	public static function register_embed_shortcode($atts) {
		extract(shortcode_atts(array(
			'sender' => '',
			'item' => '',
			'post_id' => '',
			'title' => '',
			'body' => '',
			'pid' => '',
			'display' => 'yes',
		), $atts, 'mxp_fb2wp_display_embed'));
		$meta = '';
		if ($post_id == "" || $display != "yes") {
			return $meta;
		}
		$posts = explode("_", $post_id);
		$footer = get_option("mxp_fb2wp_post_footer", "");
		if (has_shortcode($footer, 'mxp_fb2wp_display_embed')) {
			$footer = strip_shortcodes($footer);
		}
		return "<div class='fb-post' data-href='https://www.facebook.com/{$posts[0]}/posts/{$posts[1]}'></div>" . $meta . '<p></p>' . do_shortcode($footer);
	}

	//v1.5.0 新增粉絲頁評價同步功能，短碼為顯示評價用
	public static function display_ratings_shortcode($atts) {
		extract(shortcode_atts(array(
			'uid' => '',
			'limit' => '20',
			'display_embed' => 'no',
		), $atts, 'mxp_fb2wp_display_ratings'));
		global $wpdb;
		$data = array();
		if ($uid != "") {
			$uid = is_numeric($uid) ? $uid : 0;
			$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fb2wp_debug WHERE item = 'rating' AND sender={$uid}", ARRAY_A);
		} else {
			if ($limit != '') {
				$limit = intval($limit);
				$limit = "LIMIT {$limit}";
			}
			$data = $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}fb2wp_debug WHERE item = 'rating' ORDER BY sid  DESC {$limit}", ARRAY_A);
		}
		$html = '<ul id="fb2wp_display_ratings">';
		foreach ($data as $rating) {
			$html .= '<li>';
			$human_time = human_time_diff(time(), $rating['created_time']);
			$sender = $rating['sender'];
			$sender_name = $rating['sender_name'];
			$action = $rating['action'];
			$post_id = $rating['post_id'];
			$posts = explode("_", $post_id);
			$message = $rating['message'];
			if ($action == "import") {
				$message_link = "{$message}";
			} else {
				$message_link = "<a href='https://www.facebook.com/{$posts[0]}/posts/{$posts[1]}'>{$message}</a>";
			}
			$rating_num = json_decode($rating['source_json'], true);
			$rating_num = isset($rating_num['rating']) ? $rating_num['rating'] : "";
			$html .= "<a href='https://facebook.com/{$sender}'>{$sender_name}</a> ({$human_time} 前) 給予 {$rating_num} 分評價，「{$message_link}」";
			if ($display_embed == 'yes' && $action != 'import') {
				$html .= "<div class='fb-post' data-href='https://www.facebook.com/{$posts[0]}/posts/{$posts[1]}'></div>";
			}
			$html .= '</li>';
		}

		$html .= '</ul>';
		$custom_by_user = apply_filters('fb2wp_display_ratings', $data);
		return is_array($custom_by_user) == true ? $html : $custom_by_user;
	}
	// v1.4.3 新增FB引言功能
	public static function register_facebook_quote() {
		if (get_option("mxp_fb_quote_enable", "yes") == "yes") {
			echo '<div class="fb-quote"></div>';
		}
	}
	// v1.5.7 新增嵌入顧客聊天外掛
	public static function register_facebook_messenger_embed() {
		if (get_option("mxp_fb_page_id") != "") {
			$page_id = get_option("mxp_fb_page_id");
			$theme_color = get_option("mxp_fb_messenger_theme_color", ""); //主題顏色
			if ($theme_color == "") {
				$theme_color = '';
			} else {
				$theme_color = 'theme_color="#' . esc_attr($theme_color) . '"';
			}
			$logged_in_greeting = get_option("mxp_fb_messenger_logged_in_greeting",
				/* translators: Default Logged in greeting for Facebook Customer Chat Plugin. */
				esc_html__('Hello! How can we help you?', 'fb2wp-integration-tools'));
			if ($logged_in_greeting == "") {
				$logged_in_greeting = '';
			} else {
				$logged_in_greeting = 'logged_in_greeting="' . esc_attr($logged_in_greeting) . '"';
			}
			$logged_out_greeting = get_option("mxp_fb_messenger_logged_out_greeting",
				/* translators: Default Logged in greeting for Facebook Customer Chat Plugin. */
				esc_html__('Hello! How can we help you?', 'fb2wp-integration-tools'));
			if ($logged_out_greeting == "") {
				$logged_out_greeting = '';
			} else {
				$logged_out_greeting = 'logged_out_greeting="' . esc_attr($logged_out_greeting) . '"';
			}
			$greeting_dialog_delay = get_option("mxp_fb_messenger_greeting_dialog_delay", 5);
			if ($greeting_dialog_delay != "") {
				$greeting_dialog_delay = 'greeting_dialog_delay="' . intval($greeting_dialog_delay) . '"';
			} else {
				$greeting_dialog_delay = '';
			}
			switch (get_option("mxp_fb_messenger_embed", "fade")) {
			case 'show':
				echo '<div class="fb-customerchat" page_id="' . $page_id . '" greeting_dialog_display="show" ref="FB2WP" ' . $theme_color . ' ' . $logged_in_greeting . ' ' . $logged_out_greeting . '></div>';
				break;
			case 'fade':
				echo '<div class="fb-customerchat" page_id="' . $page_id . '" greeting_dialog_display="fade" ref="FB2WP" ' . $greeting_dialog_delay . ' ' . $theme_color . ' ' . $logged_in_greeting . ' ' . $logged_out_greeting . '></div>';
				break;
			case 'hide':
				echo '<div class="fb-customerchat" page_id="' . $page_id . '" greeting_dialog_display="hide" ref="FB2WP" ' . $theme_color . ' ' . $logged_in_greeting . ' ' . $logged_out_greeting . '></div>';
			default:
				//NOTHING TO DO!
				break;
			}
		}
	}
	// v1.4.3 新增FB儲存文章,傳送,留言功能
	public static function register_facebook_save($content) {
		global $wp_current_filter;
		if (get_post_type(get_the_ID()) != 'post' || is_page() || is_feed() || is_archive() || is_home() || in_array('get_the_excerpt', (array) $wp_current_filter) || 'the_excerpt' == current_filter()) {
			return $content;
		}
		$func = '';
		if (get_option("mxp_fb_save_enable", "yes") == "yes" || get_option("mxp_fb_save_enable", "yes") == "yes1") {
			if (get_option("mxp_fb_save_enable", "yes") == "yes") {
				$size = 'large';
			} else {
				$size = 'small';
			}
			$func .= '<p><div class="fb-save" data-size="' . $size . '"></div></p>';
		}
		// v1.5.0 新增 Facebook 小工具擺放位置的選項：文章內容上方、文章內容下方
		if (get_option("mxp_fb_widget_place", "down") == "down") {
			return $content . "<div id='mxp_fb_functions_section'>" . get_option("mxp_fb_functions_section_title", "</h3>" . __('Facebook features:', 'fb2wp-integration-tools') . "</h3>") . $func . "</div>";
		} else {
			return "<div id='mxp_fb_functions_section'>" . get_option("mxp_fb_functions_section_title", "</h3>" . __('Facebook features:', 'fb2wp-integration-tools') . "</h3>") . $func . "</div>" . $content;
		}
	}
	// v1.4.4.1 修正FB留言模組跟隨在任意有實作留言模板區塊文後
	public static function register_facebook_comment() {
		global $post;
		if (!(is_singular() && (have_comments() || 'open' == $post->comment_status))) {
			return;
		}
		if (get_option("mxp_fb_comments_enable", "yes") == "yes" || get_option("mxp_fb_comments_enable", "yes") == "yes1") {
			echo '<div id="mxp-fb2wp-comments"><p><div class="fb-comments" data-href="' . esc_url(get_permalink($post->ID)) . '" data-numposts="5"></div></p></div>';
		}
	}
	// v1.4.5 新增FB留言模組覆蓋方法
	public static function overwrite_default_comment($comment_template) {
		global $post;
		if (!(is_singular() && (have_comments() || 'open' == $post->comment_status))) {
			return;
		}
		if (get_option("mxp_fb_comments_enable", "yes") == "yes1") {
			return dirname(__FILE__) . '/views/fb-comments.php';
		}
		return $comment_template;
	}

	public static function register_facebook_webhooks() {
		self::$rest_api->register_routes();
	}

	public static function add_generator() {
		echo '<meta name="generator" content="FB2WP - ' . self::$version . ' Powered by Mxp.TW" />' . "\n";
	}

	public static function setting_fb_sdk() {

		?>
		<?php if (get_option("mxp_fb_app_id") != ""): ?>
			<meta property="fb:app_id" content="<?php echo get_option("mxp_fb_app_id"); ?>" />
		<?php endif;?>
		<?php if (get_option("mxp_fb_page_id") != ""): ?>
			<meta property="fb:pages" content="<?php echo get_option("mxp_fb_page_id"); ?>" />
		<?php endif;?>
			<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '<?php echo get_option("mxp_fb_app_id"); ?>',
	      xfbml      : true,
	      autoLogAppEvents: true,
	      version    : '<?php echo get_option("mxp_fb_api_version", "v3.1"); ?>'
	    });
	  };

	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/<?php echo get_option("mxp_fb_jssdk_local", get_locale()); ?>/sdk/xfbml.customerchat.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
	<?php

	}
	//取得語言標籤
	// public function get_fb_locals() {
	// 	$fb_locals = get_transient($this->slug . '-cache-fb-locals');
	// 	if (false === $fb_locals || $fb_locals == "") {
	// 		$fb_locals = wp_remote_get('https://www.facebook.com/translations/FacebookLocales.xml');
	// 		$response_code = wp_remote_retrieve_response_code($fb_locals);
	// 		if ($response_code != 200) {
	// 			$error_message = wp_remote_retrieve_body($fb_locals);
	// 			return array('locale' => 'error', 'msg' => $error_message);
	// 		}
	// 		$local_arr = json_decode(json_encode(simplexml_load_string($fb_locals['body'])), true);
	// 		set_transient($this->slug . '-cache-fb-locals', $local_arr, 4 * WEEK_IN_SECONDS);
	// 	}
	// 	return $fb_locals;
	// }

	public function get_plugin_logs($del) {
		$list = scandir(plugin_dir_path(__FILE__) . 'logs/');
		if ($list == false) {
			return array();
		}
		$logs = array();
		for ($i = 0; $i < count($list); ++$i) {
			$end = explode('.', $list[$i]);
			if ('txt' == end($end)) {
				if ($del == "no") {
					$logs[] = plugin_dir_url(__FILE__) . 'logs/' . $list[$i];
				} else {
					unlink(plugin_dir_path(__FILE__) . 'logs/' . $list[$i]);
				}
			}
		}
		return $logs;
	}

	public function mxp_messenger_settings_save() {
		$data = $_POST['data'];
		$nonce = $_POST['nonce'];
		$method = $_POST['method'];
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce')) {
			wp_send_json_error(array('data' => array('msg' => __('Bad request', 'fb2wp-integration-tools'))));
		}
		if (!isset($data) || $data == "") {
			update_option("mxp_messenger_msglist", serialize(array('match' => array(), 'fuzzy' => array())));
			wp_send_json_success(array('data' => $data));
		}
		if (isset($method) && $method == "get") {
			wp_send_json_success(unserialize(get_option("mxp_messenger_msglist")));
		}
		if (update_option("mxp_messenger_msglist", serialize($data))) {
			wp_send_json_success(array('data' => $data));
		} else {
			wp_send_json_error(array('data' => array('msg' => __('Unable to renew', 'fb2wp-integration-tools'))));
		}

	}

	public function mxp_update_facebook_url_cache($new_status, $old_status, $post) {
		// 發佈文章事件
		if ('publish' === $new_status && $post->post_type === 'post' && get_option("mxp_fb_clear_url_cache", "yes") == "yes") {
			$post_url = get_permalink($post->ID);
			self::$rest_api->update_facebook_url_cache($post_url);
		}
	}

	public function mxp_import_fb_ratings() {
		$nonce = $_POST['nonce'];
		$page_id = get_option("mxp_fb_page_id", "");
		$access_token = get_option("mxp_fb_app_access_token", "");
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce') || $page_id == "" || $access_token == "") {
			wp_send_json_error(array('data' => array('msg' => __('Invalid request parameters', 'fb2wp-integration-tools'))));
		}
		$data = self::$rest_api->import_ratings();
		if ($data === false) {
			wp_send_json_error(array('data' => array('msg' => __('Errors occurred. Please check the debugging log.', 'fb2wp-integration-tools'))));
			exit;
		}
		global $wpdb;
		$count = 1;
		for ($i = 0; $i < count($data); ++$i) {
			$created_time = strtotime($data[$i]['created_time']);
			$uid = isset($data[$i]['reviewer']['id']) ? $data[$i]['reviewer']['id'] : "";
			$uname = isset($data[$i]['reviewer']['name']) ? $data[$i]['reviewer']['name'] : "";
			$review_text = $data[$i]['review_text'];
			if ($uid != "") {
				$wrap =
				array(
					'created_time' => $created_time,
					'sender' => $uid,
					'sender_name' => $uname,
					'item' => 'rating',
					'action' => 'import',
					'post_id' => '959339774136469_1751946328209139',
					'message' => $review_text,
					'source_json' => json_encode(array(
						'comment_id' => '959339774136469_1751946328209139',
						'created_time' => $created_time,
						'item' => 'rating',
						'open_graph_story_id' => '959339774136469',
						'rating' => $data[$i]['rating'],
						'review_text' => $review_text,
						'reviewer_id' => $uid,
						'reviewer_name' => $uname,
						'verb' => 'import_by_fb2wp',
					)),
				);
				$res = $wpdb->insert($wpdb->prefix . "fb2wp_debug", $wrap, array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'));
				if (!$res) {
					if (self::$rest_api != null) {
						self::$rest_api->logger('debug' . '-db', json_encode($res) . PHP_EOL . $wpdb->last_query);
					}
				}
				$count += 1;
			}
		}
		update_option("mxp_fb2wp_rating_import", "lock");
		wp_send_json_success(array('data' => $wrap, 'count' => $count));
	}

	public function mxp_debug_record_action() {
		$method = $_POST['method'];
		$nonce = $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce') || !isset($method)) {
			wp_send_json_error(array('data' => array('msg' => __('Bad request', 'fb2wp-integration-tools'))));
		}
		$page = isset($_POST['page']) ? intval($_POST['page']) : 0;
		$sid = isset($_POST['sid']) ? explode(",", $_POST['sid']) : array();
		global $wpdb;
		switch ($method) {
		case 'get':
			$offset = 25;
			$now = $page * $offset;
			$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fb2wp_debug");
			$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fb2wp_debug ORDER BY sid DESC LIMIT {$now},{$offset}", ARRAY_A);
			$pages = ceil($count / $offset);
			wp_send_json_success(array('data' => $data, 'total_pages' => $pages, 'page' => $page));
			break;
		case 'post':
			$data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fb2wp_debug WHERE sid = {$sid[0]}", ARRAY_A);
			$fb2wp = Mxp_FB2WP::get_instance();
			$res = $fb2wp['Mxp_FB2WP_API']->sorry_i_am_late_post(json_decode($data['source_json'], true));
			if ($res) {
				wp_send_json_success(array('msg' => 'done'));
			} else {
				wp_send_json_error(array('msg' => __('Invalid request', 'fb2wp-integration-tools')));
			}
			break;
		case 'delete':
			if (count($sid) != 0) {
				for ($i = 0; $i < count($sid); ++$i) {
					$wpdb->delete("{$wpdb->prefix}fb2wp_debug", array('sid' => intval($sid[$i])));
				}
				wp_send_json_success();
			} else {
				wp_send_json_error(array('msg' => __('Invalid request', 'fb2wp-integration-tools')));
			}
			break;
		case 'search':
			$keyword = strip_tags($_POST['keyword']);
			$keywords = explode(' ', $keyword);
			$keywords = join('%%', $keywords);
			$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fb2wp_debug WHERE message LIKE '%{$keywords}%'", ARRAY_A);
			wp_send_json_success(array('data' => $data, 'total_pages' => 1, 'page' => 1));
			break;
		default:
			wp_send_json_error(array('msg' => __('Invalid request', 'fb2wp-integration-tools')));
			break;
		}

	}
}

add_action('plugins_loaded', array('Mxp_FB2WP', 'get_instance'));
