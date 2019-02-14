<?php
if (!defined('WPINC')) {
	die;
}

//v1.4.7.3 向下支援4.7以下版本部分功能，避免無類別產生致命錯誤
if (class_exists('WP_REST_Controller')) {
	class Mxp_FB2WP_API extends WP_REST_Controller {
		/**
		 * Endpoint namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'mxp_fb2wp/v1';

		/**
		 * Route base.
		 *
		 * @var string
		 */
		protected $rest_base = 'webhook';

		public $fb_messenger_api = 'https://graph.facebook.com/v3.1/me/messages';

		public $fb_graph_api = 'https://graph.facebook.com/v3.1';

		public function get_namespace_var() {
			return $this->namespace;
		}

		public function get_rest_base_var() {
			return $this->rest_base;
		}

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {
			$namespace = $this->namespace;
			$base = $this->rest_base;
			register_rest_route($namespace, '/' . $base, array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => array($this, 'get_items'),
					'permission_callback' => array($this, 'get_items_permissions_check'),
				),
				array(
					'methods' => WP_REST_Server::CREATABLE,
					'callback' => array($this, 'create_item'),
					'permission_callback' => array($this, 'create_item_permissions_check'),
				),
			));
		}

		/**
		 * 處理驗證要求：https://developers.facebook.com/docs/graph-api/webhooks#setupget
		 */
		public function get_items($request) {
			$challenge = $request->get_param("hub_challenge");
			$verify_token = $request->get_param("hub_verify_token");
			//驗證訂閱
			if ($verify_token === get_option("mxp_fb_webhooks_verify_token")) {
				echo $challenge;
				exit;
			}
			return /* translators: Message that shows up when somebody fails to verify the request via Facebook.*/
			array('msg' => esc_html__('Oops, I believe that you are just testing.', 'fb2wp-integration-tools'));
			//WP REST API 在 callback 這邊如果使用 return  就會被包裝成 JSON 格式
		}

		/**
		 * 接收webhook 請求
		 */
		public function create_item($request) {
			$json = $request->get_json_params();
			$body = $request->get_body();
			$events = $json['entry'];
			$is_page = $json['object'] == 'page' ? true : false;
			if ($is_page) {
				//Messenger 部分，至少都會有個傳送者
				$sender = isset($json['entry'][0]['messaging'][0]['sender']['id']) ? $json['entry'][0]['messaging'][0]['sender']['id'] : "";
				//最常見是訊息回覆，但也有可能出現貼圖或是傳送檔案
				//未來考慮支援 quick_reply 的 playload
				$message = isset($json['entry'][0]['messaging'][0]['message']['text']) ? $json['entry'][0]['messaging'][0]['message']['text'] : "";
				$postback = isset($json['entry'][0]['messaging'][0]['postback']) ? $json['entry'][0]['messaging'][0]['postback']['payload'] : "";
				$pass_thread_control = isset($json['entry'][0]['messaging'][0]['pass_thread_control']) ? $json['entry'][0]['messaging'][0]['pass_thread_control']['new_owner_app_id'] : "";
				if ($pass_thread_control != "") {
					//如果是把掌控權pass回來的就不回傳訊息了
					return array('msg' => 'Done!');
				}
				//TODO:高負載情況可能一次很多訊息(現在只會抓第一筆訊息回) > 2017/11/10 沒看到這項規定了！？
				//ref: https://developers.facebook.com/docs/messenger-platform/webhook-reference#batching
				if ($sender != "" && $postback != "") {
					$this->messenger_request_postback($sender, $postback);
					return array('msg' => 'Done!');
				}
				if ($sender != "") {
					$this->messenger_request($sender, $message);
					return array('msg' => 'Done!');
				}
				//Webhooks 訂閱事件處理部分
				for ($i = 0; $i < count($events); ++$i) {
					$hook = $events[$i]['changes'][0]['field'];
					if (isset($events[$i]['standby'])) {
						$hook = 'standby';
					}
					if (isset($events[$i]['messaging'])) {
						$hook = 'messaging';
					}
					switch ($hook) {
					case 'feed':
						$event = $events[$i]['changes'][0]['value'];
						$wrap = $this->parsing_event($event);
						if (count($wrap) != 0) {
							$this->fb2wp_log($wrap);
						}
						break;
					// v1.5.0 修正新增粉絲頁評價同步
					case 'ratings':
						$ratings_value = $events[$i]['changes'][0]['value'];
						$wrap = array(
							'created_time' => $ratings_value['created_time'],
							'sender' => $ratings_value['reviewer_id'],
							'sender_name' => $ratings_value['reviewer_name'],
							'item' => 'rating',
							'action' => $ratings_value['verb'],
							'post_id' => $ratings_value['comment_id'],
							'message' => $ratings_value['review_text'],
							'source_json' => json_encode($ratings_value),
						);
						$this->fb2wp_log($wrap);
						$this->logger('debug-rating-hook', $body);
						break;
					// v1.5.9 新增訊息事件紀錄(需加訂閱 standby 事件才能捕捉這個事件)
					case 'standby':
						$this->logger('debug-standby-hook', json_encode($events[$i]['standby']));
						break;
					case 'messaging':
						$this->logger('debug-messaging-hook', json_encode($events[$i]['messaging']));
						break;
					default:
						$this->logger('debug-unknow-page-hook', $body);
						break;
					}
				}
			} else {
				$this->logger('debug-isnotpage', $body);
			}
			return array('msg' => /* translators: Messages shown when any exception happens receiving Webhook data. */esc_html__('Technically, you would not see this message.', 'fb2wp-integration-tools'));
		}

		/**
		 * Check if a given request has access to get items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 * @return WP_Error|bool
		 */
		public function get_items_permissions_check($request) {
			//return true; <--use to make readable by all
			return true;
		}

		/**
		 * 驗證請求
		 */
		public function create_item_permissions_check($request) {
			$body = $request->get_body();
			$verify = 'sha1=' . hash_hmac('sha1', $body, get_option("mxp_fb_secret"));
			$signature = $request->get_header('X_HUB_SIGNATURE');
			$this->logger('request_source', 'Body:' . PHP_EOL . print_r($request->get_body(), true) . PHP_EOL . 'Headers:' . PHP_EOL . print_r($request->get_headers(), true));
			if ($verify != $signature) {
				$this->logger('request_verify', $verify . '|' . $signature);
				return false;
			}
			return true;
		}

		/**
		 * Prepare the item for create or update operation
		 *
		 * @param WP_REST_Request $request Request object
		 * @return WP_Error|object $prepared_item
		 */
		protected function prepare_item_for_database($request) {
			return array();
		}

		/**
		 * Prepare the item for the REST response
		 *
		 * @param mixed $item WordPress representation of the item.
		 * @param WP_REST_Request $request Request object.
		 * @return mixed
		 */
		public function prepare_item_for_response($item, $request) {
			return array();
		}

		/**
		 * Get the query params for collections
		 *
		 * @return array
		 */
		public function get_collection_params() {
			return array();
		}

		private function parsing_message($sender, $msg) {
			$obj = unserialize(get_option("mxp_messenger_msglist"));
			$match_resp = "";
			if (isset($obj['match'])) {
				$m = $obj['match'];
				if (is_array($m)) {
					for ($i = 0; $i < count($m); ++$i) {
						$key = $m[$i]['key'];
						$value = urldecode($m[$i]['value']);
						if ($key == $msg) {
							//1.4.7 新增 hook
							$match_resp = array(
								"type" => "match",
								"value" => $value,
								"key" => $key,
								"msg" => apply_filters('fb2wp_match_respond_call', $value, $key, $msg),
							);
						}
					}
				}

			}
			// 精準比對下有出線結果會以精準為主
			if (isset($obj['fuzzy']) && !isset($match_resp['type'])) {
				$f = $obj['fuzzy'];
				if (is_array($f)) {
					for ($i = 0; $i < count($f); ++$i) {
						$key = $f[$i]['key'];
						$value = urldecode($f[$i]['value']);
						if (preg_match("/{$key}/i", $msg)) {
							//1.4.7 新增 hook
							$match_resp = array(
								"type" => "fuzzy",
								"value" => $value,
								"key" => $key,
								"msg" => apply_filters('fb2wp_fuzzy_respond_call', $value, $key, $msg),
							);
						}
					}
				}

			}
			// v1.5.5 修正明年五月的條件
			// Beginning May 7, 2018 the messaging_type property will be required and all messages sent without it will not be delivered.
			// Ref: https://developers.facebook.com/docs/messenger-platform/send-messages#messaging_types
			$resp_data = array('recipient' => array('id' => $sender), 'messaging_type' => 'RESPONSE');
			if ($match_resp === "") {
				$nomatch_msg = $this->message_nomatch($msg);
				if ($nomatch_msg == "") {
					//沒比對到資料或回覆為空，就回傳讀寫中的狀態，考慮是否要加TODO
					//ref: https://developers.facebook.com/docs/messenger-platform/send-api-reference/sender-actions
					$resp_data['sender_action'] = 'typing_on';
				} else {
					// v1.5.9 新增按鈕切換回真人回覆(需加訂閱 messaging_postbacks 事件才能捕捉這個事件)
					// ref: https://developers.facebook.com/docs/messenger-platform/send-messages/template/button
					// ref: https://developers.facebook.com/docs/messenger-platform/reference/webhook-events/messaging_postbacks/?locale=zh_TW
					if (get_option("mxp_fb2wp_messenger_enable_pass_thread", "yes") == "yes") {
						$btn_text = get_option("mxp_fb2wp_messenger_enable_pass_thread_btn_text", "點擊此處後留言通知管理員");
						$btn_text = ($btn_text == "" ? "點擊此處後留言通知管理員" : $btn_text);
						$resp_data = array(
							'recipient' => array(
								'id' => $sender,
							),
							'messaging_type' => 'RESPONSE',
							'message' => array(
								'attachment' => array(
									'type' => 'template',
									'payload' => array(
										"text" => $nomatch_msg,
										'template_type' => 'button',
										"buttons" => array(
											0 => array(
												"type" => "postback",
												"payload" => "PASS_THREAD_TO_PAGE",
												"title" => $btn_text,
											),
										),
									),
								),
							),
						);
					} else {
						$resp_data['message'] = array('text' => $nomatch_msg);
					}
				}
			} else if (isset($match_resp['type']) && isset($match_resp['msg'])) {
				$resp_data['message'] = array('text' => $match_resp['msg']);
			} else {
				$resp_data['sender_action'] = 'typing_on';
			}
			$resp_data_set = array();
			$resp_data_set[] = $resp_data;
			// v1.5.5 新增掌控最後訊息回覆的過濾事件fb2wp_messenger_full_respond_call，可以在此事件任意包裝要回覆的內容
			$passbyfilter = apply_filters('fb2wp_messenger_full_respond_call', $resp_data_set, $match_resp, $msg);
			return isset($passbyfilter[0]['recipient']['id']) ? $passbyfilter : $resp_data_set;
		}

		public function message_nomatch($msg) {
			$nomatch = get_option("mxp_messenger_default_reply", "「{$msg}」無法識別指令");
			$detect = false;
			if (strpos($nomatch, '[mxp_input_msg]') !== false) {
				$detect = true;
			}
			if ($detect) {
				return str_replace('[mxp_input_msg]', $msg, $nomatch);
			} else {
				return $nomatch;
			}
		}
		// v1.5.9 新增 postback 事件處理方法
		private function messenger_request_postback($sender, $postback) {
			$this->logger('messenger_postback_request', $postback);
			$resp_data = array('recipient' => array('id' => $sender), 'messaging_type' => 'RESPONSE');
			$msg = "";
			switch ($postback) {
			case 'PASS_THREAD_TO_PAGE':
				//切換給粉絲頁人工查收留言
				//ref https://developers.facebook.com/docs/messenger-platform/handover-protocol/pass-thread-control#page_inbox
				$api = $this->fb_graph_api . '/me/pass_thread_control?access_token=' . get_option("mxp_fb_app_access_token");
				$data = array(
					'recipient' => array(
						'id' => $sender,
					),
					'target_app_id' => 263902037430900,
					'metadata' => 'String to pass to secondary receiver app',
				);
				$response = wp_remote_post($api, array(
					'method' => 'POST',
					'timeout' => 5,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
					'body' => json_encode($data),
					'cookies' => array(),
				)
				);
				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					$this->logger('messenger_request_pass_thread_control_error', $error_message);
					$resp_data['message'] = array('text' => __('Errors occurred. Please try again later.', 'fb2wp-integration-tools'));
				} else {
					$this->logger('messenger_request_pass_thread_control_success', json_encode($response));
					$resp_data['message'] = array('text' => __('We have already switched back to Manual Reply', 'fb2wp-integration-tools'));
				}
				break;
			default:
				$resp_data['message'] = array('text' => /* translators: Messages shown when exception happens receiving the postback from Facebook Messenger.*/__('Your technician should be in trouble.')); //看到這個訊息代表工程師要倒楣惹QQ
				break;
			}
			apply_filters('fb2wp_messenger_postback_respond', $resp_data, $sender, $postback, $msg);
		}
		/**
		 * 完整的回覆參考文件：https://developers.facebook.com/docs/messenger-platform/webhook-reference/message
		 * 要做完實在有點雜，先以訊息為主
		 */
		private function messenger_request($sender, $message) {
			$api = $this->fb_messenger_api . '?access_token=' . get_option("mxp_fb_app_access_token");
			// v1.5.6 新增白名單機制，用來App被公開後的私測
			$msg_auth_users = get_option("mxp_fb2wp_messenger_auth_users", "");
			$auth_users_arr = array();
			if (trim($msg_auth_users) != "") {
				$auth_users_arr = explode(',', $msg_auth_users);
				array_walk($auth_users_arr, function (&$str) {
					$str = trim($str);
				});
			}
			$resp = array();
			if ($msg_auth_users == "" || in_array($sender, $auth_users_arr)) {
				if (get_transient('fb2wp_' . $sender . '_bot_sleep') === false) {
					$resp = $this->parsing_message($sender, $message);
				}
			}
			// v1.5.8 新增機器人暫時休息一下的功能
			$sleep_status = 0; //0=>no,1=>yes,2=>return
			if ($message == "bye bot" && get_transient('fb2wp_' . $sender . '_bot_sleep') === false) {
				set_transient('fb2wp_' . $sender . '_bot_sleep', 'yes', 12 * HOUR_IN_SECONDS);
				$sleep_status = 1;
			}
			if ($message == "hi bot" && get_transient('fb2wp_' . $sender . '_bot_sleep') !== false) {
				delete_transient('fb2wp_' . $sender . '_bot_sleep');
				$sleep_status = 2;
			}
			if ($sleep_status != 0) {
				$resp_data = array('recipient' => array('id' => $sender), 'messaging_type' => 'RESPONSE');
				if ($sleep_status == 1) {
					$resp_data['message'] = array('text' => __('OK. Automated bots will be shut down temporarily in the next 12 hours.\n\nIn case you want to reactivate Automated bots, please type "hi bot"', 'fb2wp-integration-tools'));
				} else if ($sleep_status == 2) {
					$resp_data['message'] = array('text' => __('Congratulations, Automated bot is reactivated.'));
				}
				$resp = array(0 => $resp_data);
			}
			$this->logger('messenger_request', json_encode($resp));
			// v1.5.6 新增控制項關閉此項服務（好像早該要有這選項了吼！？）
			if (get_option("mxp_fb2wp_messenger_enable", "open") == "open") {
				// v1.5.5 新增多訊息回覆模式
				for ($i = 0; $i < count($resp); $i++) {
					$response = wp_remote_post($api, array(
						'method' => 'POST',
						'timeout' => 5,
						'redirection' => 5,
						'httpversion' => '1.1',
						'blocking' => true,
						'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
						'body' => json_encode($resp[$i]),
						'cookies' => array(),
					)
					);

					if (is_wp_error($response)) {
						$error_message = $response->get_error_message();
						$this->logger('messenger_request_error_loop_' . $i, $error_message);
					} else {
						//都正確就不要再囉唆惹！
						$this->logger('messenger_request_success_loop_' . $i, json_encode($response));
					}
				}
			}
		}

		private function parsing_event($event) {
			$item = $event['item'];
			$action = $event['verb'];
			$sender = isset($event['sender_id']) ? $event['sender_id'] : 0000000000;
			$sender_name = isset($event['sender_name']) ? $event['sender_name'] : 'none';
			$created_time = isset($event['created_time']) ? $event['created_time'] : time();
			if (isset($event['from'])) {
				$sender = $event['from']['id'];
				$sender_name = $event['from']['name'];
			}
			$post_id = $event['post_id'];
			//補上快取機制，避免重複請求造成網站資料重複匯入
			if (false === ($send = get_transient('event_cache_' . $sender . '_' . $item . '_' . $action . '_' . $post_id . '_' . $created_time))) {
				set_transient('event_cache_' . $sender . '_' . $item . '_' . $action . '_' . $post_id . '_' . $created_time, 'no_repeat', 30 * MINUTE_IN_SECONDS);
			}
			if ($send !== false) {
				return array();
			}
			$published = isset($event['published']) ? $event['published'] : -1;
			$message = isset($event['message']) ? $event['message'] : "";

			$this->logger('debug' . '-' . $item, json_encode($event));
			switch ($item) {
			case 'status':
				//粉絲頁主專屬功能
				//粉絲頁狀態更新(僅粉絲頁主有權限)
				//$event['published'] -> 1=>發佈,0=>排程(未發佈),發佈時會同時送出兩筆，一筆無此欄位資訊，一筆改為已發布
				//可能會突然來個帶圖的狀態文就會有 $event['photos'] 的陣列放圖片連結
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'share':
				//訪客或粉絲頁主分享連結都會到這(分享FB內部文章、連結不一定會有內容，API v2.4 以後還無法取得單篇內容)
				//$event['link'];
				//$event['share_id'];
				//$event['published']; //1=>發佈,0=>排程(未發佈),發佈時會同時送出兩筆，一筆無此欄位資訊，一筆改為已發布
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'photo': //訪客或粉絲頁主PO圖片都會到這
				//$event['link']; //單圖檔連結
				//$event['published']; //粉絲頁主發佈才有的狀態
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'album':
				//粉絲頁主專屬功能
				//ref: https://graph.facebook.com/v2.8/album_id 撈相簿描述
				$api = $this->fb_graph_api . '/' . $event['album_id'] . '?fields=name,description,link&access_token=' . get_option("mxp_fb_app_access_token");
				$response = wp_remote_post($api, array(
					'method' => 'GET',
					'timeout' => 5,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
					'cookies' => array(),
				)
				);

				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					$this->logger('request_error', $error_message);
					return array();
				}
				$res = json_decode($response['body'], true);
				if (!isset($res['name'])) {
					return array();
				}
				$post_id = $event['sender_id'] . '_' . $event['album_id'];
				$name = $res['name'];
				$des = $res['description'];
				$message = "{$name}\n{$des}";
				$event['message'] = $message; //存回去
				$event['post_id'] = $post_id; //存回去
				//ref: https://developers.facebook.com/docs/graph-api/reference/v2.8/album/photos 撈相簿的所有（？）圖塞文章
				//撈回100張單一頁我想差不多是極限（很懶得寫翻頁跟不超時的部分啊）
				$api = $this->fb_graph_api . '/' . $event['album_id'] . '/photos?limit=100&fields=name,source&access_token=' . get_option("mxp_fb_app_access_token");
				$response = wp_remote_post($api, array(
					'method' => 'GET',
					'timeout' => 5,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
					'cookies' => array(),
				)
				);

				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					$this->logger('request_error', $error_message);
					return array();
				}
				$res = json_decode($response['body'], true);
				if (!isset($res['data'])) {
					return array();
				}
				$event['link'] = $res['data'];
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'video':
				//可能放影片又不留言的，message 會無回傳值
				$video_id = $event['video_id'];
				$link = $event['link']; //影片連結
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'post':
				//訪客一般發文或針對發文的操作
				return array(
					'created_time' => $created_time,
					'sender' => $sender,
					'sender_name' => $sender_name,
					'item' => $item,
					'action' => $action,
					'post_id' => $post_id,
					'message' => $message,
					'source_json' => json_encode($event),
				);
				break;
			case 'like':
			case 'reaction':
				//打心情
				$post_id = $event['post_id'];
				$reaction_type = $event['reaction_type'];
				//後續有想到怎麼連結WP再說，可能是在迴響下面做自動推文（！？）或是變成簡單的評分機制（！？）
				return array();
				break;
			case 'comment':
				$this->fb2wp_comment_mirror($event);
				apply_filters('fb2wp_comment_event', $event);
				return array();
				break;
			default:
				//DO NOTHING! JUST LOG
				$this->logger('debug' . '-unknow-type-' . $item, json_encode($event));
				return array();
				break;
			}
		}
		// v1.7.0 新功能，同步文章留言功能
		public function fb2wp_comment_mirror($event) {
			//確認是否開啟留言同步功能
			$enable = get_option("mxp_fb2wp_comment_mirror_enable", "yes");
			$comment_id = $event['comment_id'];
			$post_id = $event['post_id'];
			$sender_name = $event['sender_name'];
			$sender_id = $event['sender_id'];
			if (isset($event['from'])) {
				$sender_id = $event['from']['id'];
				$sender_name = $event['from']['name'];
			}
			$created_time = $event['created_time'];
			$message = isset($event['message']) ? $event['message'] : '';
			if (false === ($send = get_transient('comment_mirror_cache_' . $sender_id . '_' . $event['verb'] . '_' . $comment_id . '_' . $created_time))) {
				//做暫存避免重複留言
				set_transient('comment_mirror_cache_' . $sender_id . '_' . $event['verb'] . '_' . $comment_id . '_' . $created_time, 'no_repeat', 30 * MINUTE_IN_SECONDS);
			}
			//不是新增或修改留言、沒有訊息、物件不包含發文資訊者都不作用
			if ($enable != 'yes' || $send !== false || in_array($event['verb'], array('add', 'edited')) != true || $message == '') {
				return false;
			}
			// 下面這兩個參數還不一定是每個請求過來都會帶... 踏奶奶的勒～
			$post = isset($event['post']) ? $event['post'] : '';
			$post_type = isset($post['type']) ? $post['type'] : '';
			if ($post_type != '' && $post_type != 'link') {
				return false;
			}
			//API 查詢是否有連結
			$api = $this->fb_graph_api . '/' . $post_id . '?fields=link&access_token=' . get_option("mxp_fb_app_access_token");
			$response = wp_remote_post($api, array(
				'method' => 'GET',
				'timeout' => 5,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
				'cookies' => array(),
			)
			);

			if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				$this->logger('fb2wp_comment_mirror_request_error', $error_message);
				return false;
			} else {
				$res = json_decode($response['body'], true);
				if (isset($res['link'])) {
					//先檢查連結是否縮網址
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $res['link']);
					curl_setopt($ch, CURLOPT_HEADER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_NOBODY, true);
					$headers = curl_exec($ch); // $headers will contain all headers
					$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); //the last effective URL
					curl_close($ch);
					//確認連結是否是對應某篇文章
					$wp_post_id = url_to_postid($url); // 神方法！ https://codex.wordpress.org/Function_Reference/url_to_postid
					if ($wp_post_id == 0) {
						return false; //沒找到對應文章
					}
					$comment_data = array(
						'comment_post_ID' => $wp_post_id,
						'comment_author' => $sender_name,
						// 'comment_author_email' => 'admin@admin.com',
						'comment_author_url' => 'https://facebook.com/' . $sender_id,
						'comment_content' => esc_html($message),
						'comment_parent' => 0,
						'comment_author_IP' => '127.0.0.1',
						'comment_agent' => 'By FB2WP comment mirror method',
						'comment_date' => date('Y-m-d H:i:s', time()),
						'comment_approved' => get_option("mxp_fb2wp_comment_mirror_approved", "yes") == 'yes' ? 1 : 0,
					);
					//針對文章新增一筆留言
					$cm_id = wp_insert_comment($comment_data);
					$this->logger('fb2wp_comment_mirror_data_success', json_encode($comment_data));
					return true;
				} else {
					// 找不到連結，就不做事，但紀錄一下，看是發生什麼事惹！
					// 看起來最可能會跑到這都是修改留言，然後詭異的 Webhook 回傳 post_id 被移除導致錯誤
					$this->logger('fb2wp_comment_mirror_data_error', json_encode($response));
					return false;
				}
			}

		}

		private function fb2wp_log($event) {
			global $wpdb;
			if (!is_array($event) || count($event) == 0) {
				return false;
			}
			//FB來的 post_id 參數會被 insert 裡的方法自動轉為數字儲存，會有問題，需要特別設定型別
			$res = $wpdb->insert($wpdb->prefix . "fb2wp_debug", $event, array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'));
			//記錄錯誤
			if (!$res) {
				$this->logger('debug' . '-db', json_encode($res) . PHP_EOL . $wpdb->last_query);
			}
			//判斷是否允許發文以及發文身份是否有授權
			if (get_option("mxp_fb2wp_post_enable", "open") == "open" && $event['action'] == "add") {
				$sender = $event['sender'];
				$sender_name = isset($event['sender_name']) ? $event['sender_name'] : "";
				$item = $event['item'];
				$post_id = $event['post_id'];
				$message = isset($event['message']) ? $event['message'] : "";
				$obj = json_decode($event['source_json'], true);
				$link = isset($obj['link']) ? $obj['link'] : "";
				if ($item == "status" && isset($obj['photos'])) {
					$link = array();
					for ($i = 0; $i < count($obj['photos']); ++$i) {
						$link[] = array('source' => $obj['photos'][$i], 'name' => $message);
					}
				}
				$published = isset($obj['published']) ? $obj['published'] : -1;
				if ($item == "post" || $item == "photo") {
					$published = 1;
				}
				$auth_users = get_option("mxp_fb2wp_auth_users", "");
				$auth_users_arr = array();
				if (trim($auth_users) != "") {
					$auth_users_arr = explode(',', $auth_users);
					array_walk($auth_users_arr, function (&$str) {
						$str = trim($str);
					});
				}
				//v1.4.7.4 為了避免「限定FB使用者投稿」功能誤會，預將粉絲頁編號設定為開放
				if (get_option("mxp_fb_page_id") != "") {
					$auth_users_arr[] = get_option("mxp_fb_page_id");
				}
				if ($auth_users == "" || in_array($sender, $auth_users_arr)) {
					if ($published == 1) {
						$this->save_to_post($sender, $sender_name, $item, $post_id, $message, $link, false);
					}

				}
			}

		}
		/**
		 * ref: https://developers.facebook.com/docs/messenger-platform/webhook-reference#response
		 * 判斷伺服器跟主機系統是哪家來決定用來射後不理的方法
		 */
		private function make_quick_response_to_facebook() {
			$detect = "NONE";
			$server = $_SERVER['SERVER_SOFTWARE'];
			if (preg_match('/nginx/i', $server)) {
				$detect = "METHOD_A";
			} else if (preg_match('/apache/i', $server)) {
				$detect = "METHOD_B";
			} else {
				$detect = "WTF";
			}
			//WINDOWS不支援多執行緒的做法
			// if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			// 	$detect = "WIN";
			// } else {
			// 	$detect = "NOTWIN";
			// }
			//中斷請求連線自己搞，還是會佔線，先測試看是否要開到多執行緒，但一般人不會ＰＯ這麼頻繁影片吧！！
			switch ($detect) {
			case 'METHOD_A':
				fastcgi_finish_request();
				break;
			case 'METHOD_B':
			case 'WTF':
				ob_start();
				header("Connection: close");
				header("Content-Encoding: none");
				echo "got it";
				$size = ob_get_length();
				header("Content-Length: {$size}");
				ob_end_flush();
				flush();
			default:
				break;
			}
		}

		private function save_to_post($sender, $sender_name, $item, $post_id, $message, $link, $late) {
			if (!$late) {
				$this->make_quick_response_to_facebook();
			}
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$message_arr = explode("\n", $message);
			//處理文章標題，預設就是發文第一行，若無則呼叫設定的參數
			$title = $message_arr[0] == "" ? current_time("Y-m-d H:i:s") . get_option("mxp_fb2wp_default_title", "-FB轉發文章") : wp_strip_all_tags($message_arr[0]);
			//支援 Markdown 寫法的處理
			$post_content_filtered = "";
			$markdown_active = false;
			if (!function_exists('is_plugin_active')) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if (!function_exists('post_type_supports')) {
				include_once ABSPATH . 'wp-includes/post.php.';
			}
			if (is_plugin_active('wp-markdown/wp-markdown.php') || is_plugin_active('wp-githuber-md/githuber-md.php') || post_type_supports(get_option("mxp_fb2wp_post_type", "post"), 'wpcom-markdown')) {
				$markdown_active = true;
			}
			//處理文章內文
			$body = "";
			for ($i = 1; $i < count($message_arr); ++$i) {
				if ($markdown_active == true) {
					$body .= $message_arr[$i] . "\n";
					$post_content_filtered .= $message_arr[$i] . "\n";
				}
				if ($message_arr[$i] != "" && $markdown_active == false) {
					$body .= "<p>" . $message_arr[$i] . "</p>";
				}
			}
			//處理文章標籤
			preg_match_all('/#([\p{Pc}\p{N}\p{L}\p{Mn}]+)/u', $message, $tags);
			if (count($tags[1]) == 0 && get_option("mxp_fb2wp_post_tags", "") != "") {
				$tags[1] = explode(',', get_option("mxp_fb2wp_post_tags"));
			}
			$new_tags = $tags[1];
			//判斷標籤是否有提示需要略過發文
			$pass_tag = get_option("mxp_fb2wp_no_post_tag", "");
			if ($late == false && in_array($pass_tag, $new_tags)) {
				return false;
			}
			//處理舊文同步時圖片失連問題，呼叫API重新取得
			if ($late == true && $link != "") {
				$api = $this->fb_graph_api . '/' . $post_id . '/attachments?access_token=' . get_option("mxp_fb_app_access_token");
				$response = wp_remote_post($api, array(
					'method' => 'GET',
					'timeout' => 5,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
					'cookies' => array(),
				)
				);

				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					$this->logger('save_to_post_link_source_request_error', $error_message);
					$link = "";
				} else {
					$res = json_decode($response['body'], true);
					$data = $res['data'][0];
					$type = $data['type'];
					if ($type == 'photo') {
						$link = $data['media']['image']['src'];
					}
					if ($type == 'album') {
						$subattachments = $data['subattachments']['data'];
						$link = array();
						for ($i = 0; $i < count($subattachments); $i++) {
							$link[] = array('source' => $subattachments[$i]['media']['image']['src']);
						}

					}
				}
			}
			//處理標籤同步分類
			$cats = array();
			$cats[] = get_option("mxp_fb2wp_post_category", "1");
			$exist_cats = get_categories(array('taxonomy' => 'category'));
			foreach ($exist_cats as $exist_cat) {
				for ($i = 0; $i < count($new_tags); ++$i) {
					if ($exist_cat->name == $new_tags[$i]) {
						$cats[] = $exist_cat->term_id;
					}
				}
			}
			//建立一篇文章
			$post_data = array(
				'post_title' => $title,
				'post_content' => $body,
				'post_content_filtered' => $post_content_filtered,
				'post_status' => get_option("mxp_fb2wp_post_status", "draft"),
				'post_author' => get_option("mxp_fb2wp_post_author", "1"),
				'post_category' => $cats,
				'tags_input' => $new_tags,
				'comment_status' => get_option("mxp_fb2wp_post_comment_status", "open"),
				'ping_status' => get_option("mxp_fb2wp_post_ping_status", "open"),
				'post_type' => get_option("mxp_fb2wp_post_type", "post"),
			);

			$pid = wp_insert_post($post_data);
			if (is_wp_error($pid)) {
				$this->logger('insert_post_error', print_r($pid, true));
				return false;
			}
			//處理嵌入文章短碼
			$embed_shortcode = '[mxp_fb2wp_display_embed sender="' . $sender . '" item="' . $item . '" post_id="' . $post_id . '" display="' . get_option("mxp_fb2wp_default_display_embed", "yes") . '" title="' . base64_encode(str_replace(array('\'', '"'), '', wp_strip_all_tags($title))) . '" body="' . base64_encode(str_replace(array('\'', '"'), '', wp_strip_all_tags($body))) . '" pid="' . $pid . '"]';
			//加入 post metadata
			add_post_meta($pid, 'mxp_fb2wp_post_id', $post_id);
			add_post_meta($pid, 'mxp_fb2wp_item', $item);
			add_post_meta($pid, 'mxp_fb2wp_sender', $sender);
			//判斷是否有附加檔案，並上傳
			$filename = array();
			$upload_file = array();
			if ($link != "" && $item != "share") {
				if (!is_array($link)) {
					$filename[] = basename(parse_url($link, PHP_URL_PATH));
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $link);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					$data = curl_exec($ch);
					curl_close($ch);
					$upload_file[] = wp_upload_bits($filename[0], null, $data);
				} else {
					for ($i = 0; $i < count($link); ++$i) {
						$filename[$i] = basename(parse_url($link[$i]['source'], PHP_URL_PATH));
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, $link[$i]['source']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
						$data = curl_exec($ch);
						curl_close($ch);
						$upload_file[] = wp_upload_bits($filename[$i], null, $data);
					}
				}
				//如果上傳沒失敗，就附加到剛剛那篇文章
				$origin_body = $body;
				$origin_title = $title;
				$set_feature_image = true;
				for ($i = 0; $i < count($upload_file); ++$i) {
					if (!$upload_file[$i]['error']) {
						$wp_filetype = wp_check_filetype($filename[$i], null);
						$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_parent' => $pid,
							'post_author' => get_option("mxp_fb2wp_post_author", "1"),
							'post_title' => preg_replace('/\.[^.]+$/', '', $filename[$i]),
							'post_content' => is_array($link) && isset($link[$i]['name']) ? $link[$i]['name'] : $origin_title . $origin_body,
							'post_status' => 'inherit',
						);
						$attachment_id = wp_insert_attachment($attachment, $upload_file[$i]['file'], $pid);
						if (!is_wp_error($attachment_id)) {
							//產生附加檔案中繼資料
							$attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file[$i]['file']);
							wp_update_attachment_metadata($attachment_id, $attachment_data);
							//將圖像的附加檔案設為特色圖片
							$type = explode("/", $wp_filetype['type']);
							if ($set_feature_image == true && $type[0] == 'image') {
								set_post_thumbnail($pid, $attachment_id);
								$set_feature_image = false;
							}
							//更新剛剛那篇文章內容，加載附件更新文章
							$body .= "\n<p>[mxp_fb2wp_display_attachment src=\"" . $upload_file[$i]['url'] . '" mime_type="' . $wp_filetype['type'] . '" title="' . base64_encode(str_replace(array('\'', '"'), '', wp_strip_all_tags(is_array($link) && isset($link[$i]['name']) ? $link[$i]['name'] : $origin_title))) . '" body="" display="' . get_option("mxp_fb2wp_default_display_attachment", "yes") . '" image_display_caption="' . get_option("mxp_fb2wp_default_display_img_caption", "no") . '"]</p>';
						}
					}
				}
			}
			$body .= "\n<p>" . $embed_shortcode . "</p>";
			$update_attachment_post = array(
				'ID' => $pid,
				'post_content' => $body,
			);
			$upid = wp_update_post($update_attachment_post);
			if (is_wp_error($upid)) {
				$this->logger('update_post_error', print_r($upid, true));
				return false;
			}
			return true;
		}

		public function sorry_i_am_late_post($event) {
			$wrap = $this->parsing_event($event);
			if (count($wrap) != 0 && $wrap['action'] == "add") {
				$sender = $wrap['sender'];
				$sender_name = isset($wrap['sender_name']) ? $wrap['sender_name'] : "";
				$item = $wrap['item'];
				$post_id = $wrap['post_id'];
				$message = isset($wrap['message']) ? $wrap['message'] : "";
				$obj = json_decode($wrap['source_json'], true);
				$link = isset($obj['link']) ? $obj['link'] : "";
				if ($item == "status" && isset($obj['photos'])) {
					$link = array();
					for ($i = 0; $i < count($obj['photos']); ++$i) {
						$link[] = array('source' => $obj['photos'][$i], 'name' => $message);
					}
				}
				$published = isset($obj['published']) ? $obj['published'] : -1;
				$allow_post = array('post', 'status', 'share', 'photo', 'video');
				if (in_array($item, $allow_post)) {
					$published = 1;
				}
				$auth_users = get_option("mxp_fb2wp_auth_users", "");
				$auth_users_arr = array();
				if (trim($auth_users) != "") {
					$auth_users_arr = explode(',', $auth_users);
					array_walk($auth_users_arr, function (&$str) {
						$str = trim($str);
					});
				}
				if ($auth_users == "" || in_array($sender, $auth_users_arr)) {
					if ($published == 1) {
						return $this->save_to_post($sender, $sender_name, $item, $post_id, $message, $link, true);
					}

				}
			}
			return false;
		}

		public function update_facebook_url_cache($url) {
			$app_id = get_option("mxp_fb_app_id", "");
			$app_secret = get_option("mxp_fb_secret", "");
			if ($app_id == "" || $app_secret == "") {
				return false;
			}
			$api = $this->fb_graph_api . "/?access_token={$app_id}|{$app_secret}";
			$response = wp_remote_post($api, array(
				'method' => 'POST',
				'timeout' => 5,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
				'body' => json_encode(array(
					'id' => $url,
					'scrape' => 'true',
				)),
				'cookies' => array(),
			)
			);
			$this->logger('update_facebook_url_cache', json_encode($response));
		}

		public function import_ratings() {
			$page_id = get_option("mxp_fb_page_id", "");
			$access_token = get_option("mxp_fb_app_access_token", "");
			if ($page_id == "" || $access_token == "") {
				return false;
			}
			$api = $this->fb_graph_api . "/{$page_id}/ratings?access_token={$access_token}&limit=100";
			$response = wp_remote_post($api, array(
				'method' => 'GET',
				'timeout' => 5,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
				'cookies' => array(),
			)
			);

			if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				$this->logger('mport_fb_ratings_request_error', $error_message);
				return false;
			}
			$res = json_decode($response['body'], true);
			$this->logger('mport_fb_ratings_request_success', $response['body']);
			if (isset($res['data'])) {
				return $res['data'];
			} else {
				$this->logger('mport_fb_ratings_decode_error', json_encode($res));
				return false;
			}

		}
		public function logger($file, $data) {
			if (get_option("mxp_enable_debug", "yes") == "yes") {
				file_put_contents(
					plugin_dir_path(__FILE__) . 'logs/' . md5(get_option("mxp_fb_secret")) . "-{$file}.txt",
					'===' . date('Y-m-d H:i:s', time()) . '===' . PHP_EOL . $data . PHP_EOL,
					FILE_APPEND
				);
			}
		}
	}
}