<?php
if (!defined('WPINC')) {
	die;
}

//更新方法都寫這，方法必須要回傳 true 才算更新完成。
class Mxp_Update {
	public static $version_list = array('1.3.4', '1.3.5', '1.3.6', '1.3.7', '1.3.8', '1.3.9', '1.4.0', '1.4.1', '1.4.2', '1.4.3', '1.4.3.1', '1.4.4', '1.4.5', '1.4.6', '1.4.7', '1.4.7.1', '1.4.7.2', '1.4.7.3', '1.4.7.4', '1.4.8', '1.4.9', '1.5.0', '1.5.1', '1.5.2', '1.5.3', '1.5.4', '1.5.5', '1.5.6', '1.5.7', '1.5.7.1', '1.5.8', '1.5.9', '1.5.9.1', '1.6.0', '1.6.0.1', '1.7.0', '1.7.0.1', '1.7.0.4', '1.7.1', '1.7.2', '1.7.3', '1.7.4', '1.7.5', '1.7.6', '1.7.7', '1.7.8', '1.7.9', '1.8.0', '1.8.1');

	public static function apply_update($ver) {
		$index = array_search($ver, self::$version_list);
		if ($index === false) {
			echo "<script>console.log('update version: {$ver}, in index: {$index}');</script>";
			return false;
		}
		for ($i = $index + 1; $i < count(self::$version_list); ++$i) {
			$new_v = str_replace(".", "_", self::$version_list[$i]);
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				echo "<script>console.log('mxp_update_to_v{$new_v}');</script>";
			}
			if (call_user_func(array(__CLASS__, "mxp_update_to_v{$new_v}")) === false) {
				//echo "<script>console.log('current version: {$ver}, new version: {$new_v}');</script>";
				//NO MORE TALK!
				return false;
			}
		}
		return true;
	}
	/**
	 * 經過前面一段混亂的緊湊開發，從 2016-12-21 v0.0.1 版到 2017-01-05 歷經 133 次的 commit
	 * 更新線上測試版本 v1.1.6 到最當前現在最新版 v1.3.4，來準備提交！
	 */
	public static function mxp_update_to_v1_3_4() {
		// global $wpdb;
		// $collate = '';

		// if ($wpdb->has_cap('collation')) {
		// 	$collate = $wpdb->get_charset_collate();
		// }
		// $wpdb->query("blahblah~");
		// $tables = "";
		// if (!function_exists('dbDelta')) {
		// 	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// }
		// dbDelta($tables);
		return true;
	}
	public static function mxp_update_to_v1_3_5() {
		//更新外掛描述頁面資訊
		return true;
	}
	public static function mxp_update_to_v1_3_6() {
		//更新簡寫陣列（[]）的寫法，向下相容PHP版本
		//新增功能：tag 中包含完整分類字眼就將發文加入該分類
		return true;
	}
	public static function mxp_update_to_v1_3_7() {
		//新增功能：FB發文使用自訂標籤（#tag）停止該篇同步發文
		return true;
	}
	public static function mxp_update_to_v1_3_8() {
		//優化一些寫法
		return true;
	}
	public static function mxp_update_to_v1_3_9() {
		//解決 PHP Deprecated:  Non-static method Mxp_FB2WP::get_instance() should not be called statically 警示
		return true;
	}
	public static function mxp_update_to_v1_4_0() {
		//改良更新方法，確保各版本升級時不會有問題。
		return true;
	}
	public static function mxp_update_to_v1_4_1() {
		//移除 Microdata JSON-LD 支援，避免造成 Google Search Console 結構化資料判斷錯誤
		return true;
	}
	public static function mxp_update_to_v1_4_2() {
		//新增上傳的圖片自動設定為該發文的特色圖片，相容 schemapress 所產生的 JSON-LD 資料
		//修正 Messenger Webhook 傳來資料的判斷式
		return true;
	}
	public static function mxp_update_to_v1_4_3() {
		//修正後台輸出因html標籤，導致顯示錯誤，避免被自己XSS
		//新增FB引言功能
		//新增FB儲存文章,傳送,留言功能
		//修正前台文章附件內容輸出，提高安全性
		//新增移除外掛是否刪除設定選項
		return true;
	}
	public static function mxp_update_to_v1_4_3_1() {
		//修正版本比對函式中參數要為字串
		//修正後台設定選項失靈
		return true;
	}
	public static function mxp_update_to_v1_4_4() {
		//新增問與答，關於設定同步功能部份
		//新增設定「粉絲頁編號」
		//新增 fb:pages, fb:app_id 的 head meta 值
		return true;
	}
	public static function mxp_update_to_v1_4_5() {
		//修正FB留言模組跟隨在任意有實作留言模板區塊文後，透過後台內建管理開通留言與否設定
		//新增Facebook小工具上方描述設定
		//新增Facebook儲存外掛大小按鈕設定
		return true;
	}
	public static function mxp_update_to_v1_4_6() {
		//終於把待辦事項中第一項「將使用者所輸入的訊息參數化」給完成
		//修正一些錯字小問題
		return true;
	}
	public static function mxp_update_to_v1_4_7() {
		//延伸 Messenger 自動回覆功能彈性，可程式化設定 `fb2wp_match_respond_call`, `fb2wp_fuzzy_respond_call` 兩組事件，強化回覆內容彈性
		//補強說明文件
		//更新快照圖片
		return true;
	}
	public static function mxp_update_to_v1_4_7_1() {
		//修正FB訊息 hook 事件處理方法
		return true;
	}
	public static function mxp_update_to_v1_4_7_2() {
		//寫新功能發現舊功能的BUG，更新了錯字問題
		//強化 fb2wp_match_respond_call 與 fb2wp_fuzzy_respond_call 兩個事件的完整性
		return true;
	}
	public static function mxp_update_to_v1_4_7_3() {
		//向下支援4.7以下版本部分功能，避免無 WP_REST_Controller 類別產生致命錯誤
		//整理 main.php 程式碼
		return true;
	}
	public static function mxp_update_to_v1_4_7_4() {
		//為了避免「限定FB使用者投稿」功能誤會，預將粉絲頁編號設定為開放
		//修正問與答內容
		return true;
	}
	public static function mxp_update_to_v1_4_8() {
		//修正 DEBUG 模式下顯示的錯誤資訊
		//因應 Facebook Webhooks 這次[故障](https://developers.facebook.com/bugs/463793280620151/)與[語系文件遺失](https://developers.facebook.com/bugs/1836827343245862/)補上追蹤原始請求紀錄和錯誤判斷
		return true;
	}
	public static function mxp_update_to_v1_4_9() {
		//新增 `fb2wp_comment_event` hook 方法，讓粉絲頁留言也能捕捉，使開發者能藉此方法建立自動回覆機制
		//移除 Facebook 太久都沒修好的「語系」資料，改為手動！！！
		//更新後端請求 FB 的 API 版本為 `v2.10`
		return true;
	}
	public static function mxp_update_to_v1_5_0() {
		// 新增同步粉絲頁評價功能，使用 `[mxp_fb2wp_display_ratings]` 短碼在網站中顯示新同步的評價
		// 修正選單用詞
		// 新增贊助連結
		// 修正設定頁樣式，感謝[小豬](https://piglife.tw/)協助補完
		// 根據外掛開發指標，強化外掛頁面安全性，禁止對外直接存取檔案
		// 新增 Facebook 小工具擺放位置的選項：文章內容上方、文章內容下方
		return true;
	}
	public static function mxp_update_to_v1_5_1() {
		// 修正版本支援至 4.8.3 以及確保 main.js 有更新上去！！！（眼神死）（一定是太久沒更新都生疏了）
		return true;
	}
	public static function mxp_update_to_v1_5_2() {
		// 確保 main.js 有更新上去！！！（眼神死）（一定是太久沒更新都生疏了）x2
		return true;
	}
	public static function mxp_update_to_v1_5_3() {
		// 新增粉絲頁評價匯入功能
		return true;
	}
	public static function mxp_update_to_v1_5_4() {
		// 修正方法引用的寫法避免警告出現
		// 新增發文或更新文章時同步清除 Facebook 快取的功能，有更新文章時就不用擔心臉書還在快取舊文了！
		return true;
	}
	public static function mxp_update_to_v1_5_5() {
		// 修正可能會導致更新失敗的錯誤：未定義 deactivate_plugins 方法
		// 新增掌控粉絲頁訊息最後回覆的過濾事件 `fb2wp_messenger_full_respond_call` ，可以在此事件任意包裝要回覆的內容
		// 更新 Facebook Messenger Platform 2.2 明年五月必須更新的 `messaging_type` 條件
		// 調整架構為適合多訊息回覆模式，最大彈性支援回覆訊息種類與方法
		return true;
	}
	public static function mxp_update_to_v1_5_6() {
		// 新增了一個早該新增的功能：確認是否啟用訊息功能
		// 新增訊息功能白名單機制，用來App被公開後的私測
		return true;
	}
	public static function mxp_update_to_v1_5_7() {
		// 新增 Facebook [顧客聊天外掛](https://developers.facebook.com/docs/messenger-platform/reference/web-plugins/#customer_chat)功能
		// 東修西修一下，優化外掛（改預設不顯示同步回的圖片描述、程式碼整理、除錯紀錄時間格式化）
		return true;
	}
	public static function mxp_update_to_v1_5_7_1() {
		// 改太多東西就是會有漏網之魚，太靠杯惹，是魔咒膩？
		return true;
	}
	public static function mxp_update_to_v1_5_8() {
		// 改錯字
		// 補上讓機器人休息，不要在訊息中插嘴的「bye bot」與喚醒的「hi bot」指令
		return true;
	}
	public static function mxp_update_to_v1_5_9() {
		// 實作 [pass_thread_control](https://developers.facebook.com/docs/messenger-platform/handover-protocol/pass-thread-control#page_inbox) 機制，達成機器人與真人切換的目的
		// 新增 `fb2wp_messenger_postback_respond` 事件，支援程式化採用 `postback` 後的回應
		// 新增預設實作切換機器人與管理員的功能的 `postback` ，機器人是真正的不再插嘴，而不是靜音惹！（前一版的更新是支援沒做粉絲頁設定用的）
		return true;
	}
	public static function mxp_update_to_v1_5_9_1() {
		// 修正 PHP5.4 向下相容問題
		return true;
	}
	public static function mxp_update_to_v1_6_0() {
		// 移除 2018/02/05 失效 的傳送功能
		delete_option("mxp_fb_send_enable");
		// 新增訊息交接模式的控制項
		return true;
	}
	public static function mxp_update_to_v1_6_0_1() {
		// 感謝 [Alex](https://profiles.wordpress.org/alexclassroom/) 回報粉絲頁回傳格式差異性問題
		// 解決 API 回傳 JSON 格式差異性問題
		// 更新 API 請求版本統一為： v2.11
		// 解決短碼產生格式有誤問題
		// 新增判斷 Facebook 圖片失聯重新請求過的方法
		// 新增預防使用者於逗點分隔名單中多輸入空白的方法
		return true;
	}
	public static function mxp_update_to_v1_7_0() {
		// 新增快取功能，解決重複訂閱或是 Facebook 重複發送請求導致匯入重複內容方法
		// 新增 Facebook 發文轉貼文章，留言同步回網站功能
		return true;
	}
	public static function mxp_update_to_v1_7_0_1() {
		// 補上「更新留言」案例也同步回網站
		// 修正快取功能
		return true;
	}
	public static function mxp_update_to_v1_7_0_4() {
		// 奇妙的「修正留言」通知 BUG 解了，但還有粉絲頁版本不同所延伸回傳資料不同的問題，此版本新增相容性修正。
		// 優化下載 Facebook 抓圖機制程式碼
		// 更新說明文件中的 Screenshots
		return true;
	}
	public static function mxp_update_to_v1_7_1() {
		// 根據顧客聊天 API 規格修正 Facebook 外掛 Ref: https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin
		$update_option = get_option("mxp_fb_messenger_embed");
		if ($update_option == "yes_and_open") {
			update_option("mxp_fb_messenger_embed", "fade");
		} else if ($update_option == "yes") {
			update_option("mxp_fb_messenger_embed", "show");
		}
		return true;
	}
	public static function mxp_update_to_v1_7_2() {
		//例行更新，感謝 @alexclassroom 提出使用流程描述的修正以及一些小細節調整與更新。
		return true;
	}
	public static function mxp_update_to_v1_7_3() {
		//更新儲存設定時發生的 Undefined index 問題
		return true;
	}
	public static function mxp_update_to_v1_7_4() {
		//感謝 Kdiag Haci 網友提交可能的安全性風險。已完成修復設定頁的 CSRF issue
		return true;
	}
	public static function mxp_update_to_v1_7_5() {
		//新增 Webhooks 紀錄搜尋功能，省得一頁一頁找！
		//更新支援的 Graph API 版本到 v3.1
		return true;
	}
	public static function mxp_update_to_v1_7_6() {
		//修正 Facebook JS SDK 留言外掛參數讀取頁面路徑為必填
		//提升使用者預設 JS SDK 版本 v3.1，避免早期 v2.x 版的支援問題
		//如果有問題，我應該會收到 feedback 吧（？），不想被主動提升版本可以自己設定回去，其實設定都是自己可以來的，我幫你一把而已。
		update_option("mxp_fb_api_version", "v3.1");
		return true;
	}
	public static function mxp_update_to_v1_7_7() {
		//修正 Facebook 客戶聊天外掛替換 SDK 路徑
		return true;
	}
	public static function mxp_update_to_v1_7_8() {
		//擴充 i18n 功能
		//部分介面修正
		return true;
	}
	public static function mxp_update_to_v1_7_9() {
		//增加 Markdown 外掛功能的支援度
		//介面翻譯方法修正
		return true;
	}
	public static function mxp_update_to_v1_8_0() {
		//增加對 WordPress 5.0.2支援度
		//介面翻譯方法修正
		return true;
	}
	public static function mxp_update_to_v1_8_1() {
		//更新對 WordPress 5.1 支援說明
		//補上移除外掛時完整移除內容的擦屁股程式碼
		return true;
	}
}