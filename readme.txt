=== FB2WP integration tools ===
Contributors: mxp, no249a002
Donate link: https://mxp.tw/jT
Tags: Mxp.TW, FB2WP, Facebook, Webhooks, FB, 同步, 發佈, 轉發, 機器人, 自動回覆訊息, API, sync, synchronize, 粉絲頁, Facebook, Page, Messenger, webhook, generate, auto, bot
Requires at least: 4.7
Requires PHP: 5.4
Tested up to: 5.1
Stable tag: 1.8.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

透過此外掛來當您粉絲頁的小管家，包含自動回覆訊息、同步圖文回網站等豐富整合功能！


== Description ==

### 外掛功能項目：

1. 粉絲頁內容同步回 WordPress 網站（Facebook -> WordPress）
2. 粉絲頁訊息機器人自動回覆功能
3. 粉絲頁評價同步回 WordPress 網站
4. Facebook 外掛：文章留言模組、網站粉絲頁 Messenger 顧客聊天整合、文章收藏
5. 發文自動更新 Facebook 暫存快取
6. 支援 Facebook Webhooks 事件程式化：留言捕捉、心情按讚事件捕捉
7. 支援指定 SDK 語言和版本使用

教學介紹文：

- [如何利用 FB2WP integration tools 讓粉絲團發文同步至我們的網站上](https://goo.gl/BDxOfx) By 一群棒子
- [用 FB2WP 將臉書 Messenger 聊天機器人加入 WordPress 網站](https://diary.taskinghouse.com/posts/install-facebook-messenger-chat-bot-in-wordpress-website-with-fb2wp/) By Victor Hung's Diary 

### 程式開發捕捉事件:

1. `fb2wp_match_respond_call` 捕捉完全比對的訊息呼叫
2. `fb2wp_fuzzy_respond_call` 捕捉模糊比對的訊息呼叫
3. `fb2wp_messenger_full_respond_call` 捕捉全部完整的訊息呼叫
4. `fb2wp_messenger_postback_respond` 捕捉 Postback 的訊息呼叫
5. `fb2wp_comment_event` 捕捉粉絲頁留言的訊息呼叫
6. `fb2wp_display_ratings` 客製化顯示粉絲頁評價內容

### 外掛使用短碼:

1. `mxp_fb2wp_display_attachment` 用於顯示同步回網站的附件內容
2. `mxp_fb2wp_display_embed` 用於嵌入顯示同步回網站的該篇塗鴉牆發文
3. `mxp_fb2wp_display_ratings` 用於顯示評價內容


### 需求(Requirements):

1. WordPress 4.7 以上版本（部分功能需要）
2. 網站 HTTPS 加密連線（部分功能需要）

### 待辦清單(TODO):

1. 寫文件
2. UI 修正

== Installation ==

* 一般

> 進入網站後台，「外掛」->「安裝外掛」搜尋此外掛名稱

* 進階

1. 上傳外掛至外掛目錄 `wp-content/plugins/` 下。 Upload the plugin files to the `/wp-content/plugins/` directory
2. 於後台外掛功能處啟動外掛。 Activate the plugin through the 'Plugins' screen in WordPress
3. 啟用後在後台選單可找到「FB工具箱設定」進行參數調整。 Use the 「FB工具箱設定」 screen to configure the plugin
4. 完成。 Done


== Screenshots ==

1. **Facebook App設定** - 設定一組 App 資訊，作為網站端與 Facebook 溝通的橋樑。

2. **Facebook Webhook設定** - Facebook 與 網站 端溝通的連結。

3. **Messenger 自動回覆設定** - 針對 Messenger 應答機器人的全域設定。

4. **文章同步設定** - 設定粉絲頁這邊通知網站有新文章時的相關設定。

5. **Facebook 外掛功能** - 網站與 Facebook 的相關小外掛整合功能設定處。

6. **Facebook 粉絲頁評價** - 匯入過去用戶對粉絲頁的評價至網站，往後可以透過外掛呼叫內容並使用。

7. **開發者功能** - 若發生障礙時，此處留存的紀錄檔將可以協助作者或是有開發經驗的你完成疑難排解。

== Frequently Asked Questions ==

= 設定中的 「粉絲頁應用程式授權碼」、 「請將下列連結填入 App Messenger, Webhooks 之回呼網址」、 「回呼驗證權杖」 這三個欄位要如何填寫？ =

粉絲頁應用程式授權碼 這個是需要搭配使用 FB APP 產生

參考之前[寫過的一篇文章](https://mxp.tw/oJ)

1. 申請一個 Facebook App，輸入名稱信箱跟用途

2. 進控制面板後左側 新增產品 ，把 Webhooks 與 Messenger 加入

首先在 webhooks 那個設定裡可以看到一個 新訂閱內容 的按鈕在右邊欄，點下去，選擇 Page （粉絲頁）

填入外掛產生的 回呼網址 （callback url）、 與你自己設定的 驗證權杖 （verify token），最後是勾選如外掛說的 訂閱欄位 <messages, messaging_postbacks, standby, messaging_handovers, conversations, feed, ratings>

其中 `messages` 開頭的事件已改在 App 的 `Messenger` 分頁中設定！

都確定後就驗證儲存！

回到 粉絲頁應用程式授權碼

需在 Messenger 這功能設定頁裡選擇 權杖產生 跟設定 Webhooks 訂閱綁定 （這邊都是選擇要訂閱哪個粉絲頁就可以了），在權杖產生那邊會生成一組很長的授權碼，把它貼回到外掛設定頁中，存檔，搞定！

= 怎麼粉絲頁上修改不會跟著修改？ =

此外掛只會抓取「新增」事件來加入網站內容，其餘操作僅是記錄

= 粉絲頁上同步到網站後，行間距變很大？ =

外掛會根據你的發文空行，去轉換成 `p` 標籤！使用上不建議在粉絲頁上使用連續空行排版，會造成網站顯示行距過大。這部分建議文字可以打多一點後再使用連續空行，閱讀上較不會造成問題。可以參考作者的筆記粉絲頁：[一介資男](https://www.facebook.com/a.tech.guy/) 搭配網站練習！

= 一次新增相簿照片超過一百張會怎樣？ =

運氣好是會抓完一百張，發文。運氣不好（主機連線速度太慢）會導致超時被終止，沒反應！

運氣好定義： PHP 執行時間設的夠長

= Facebook 外掛功能區塊介紹 =

- 文章引言 ：啟動全站內容將可以被瀏覽者重點文字選取後分享於 Facebook 中 
- 文章儲存 ：儲存該篇文章於 Facebook ，可在[我的珍藏](https://www.facebook.com/saved/)裡找到
- 文章留言 ：網站文章或其他類型內容下方的留言功能，有分兩種模式，共存模式即為與原本留言功能共存，單一模式為僅保留 Facebook 留言功能。
- Messenger 顧客聊天 ：全站頁面右下角嵌入 Facebook Messenger 聊天外掛

= 粉絲頁過去的文章怎麼辦，能同步回網站嗎？ =

這部分很遺憾外掛只能做到「從安裝外掛啟用之後」的文章，粉絲頁上舊文章經過測試，可以使用這款「[Facebook Fanpage import](https://tw.wordpress.org/plugins/facebook-fanpage-import/)」外掛進行匯入！

要注意的一點是伺服器上 Cronjob 記得要設定哦～ 如果網站一開始流量太低，可能會有漏抓、跳拍問題（定期抓取功能可能有時候失靈）。

= 粉絲頁評價功能為何只能匯入一次？ =

再一次也只會匯入一樣的資料，為了避免各位手癢、手誤，就限制匯入一次的機制囉！相信有 100 筆評價，搭配短碼使用，可以創造不少火花了～

= 可以客製化粉絲頁評價的顯示格式嗎？ =

當然可以，透過 `fb2wp_display_ratings` 事件，你將會得到一個原始陣列資料，愛怎麼客製就怎麼客製化囉！ 記得善用短碼的參數 `uid` 可以設定顯示某個人的評價 `limit` 為預設顯示20筆評價 `display_embed` 設定為 `yes` 則能嵌入 Facebook 的評價訊息哦！

= 網站上 Facebook 留言外掛功能消失的除錯流程 =

外掛開發遵守系統設定，首先需要檢查文章是否設定為關閉留言（允許迴響被打勾），再來是確定FB工具箱設定中「Facebook 外掛功能」處的「啟用文章留言」功能是否被選擇為「否」，以上都確認無誤還是發生留言外掛無作用的問題，請聯絡作者我處理！（外掛這邊開詢問不會通知作者，效率較慢哦！）

= 碰到問題怎回報？ =

可以透過粉絲頁、網站或是個人臉書找到我。

臉書：[點此](https://www.facebook.com/mxp.tw)

粉絲頁： [點此](https://www.facebook.com/a.tech.guy)

網站：[聯絡我](https://www.mxp.tw/contact/)

== Changelog ==

= 1.8.1 =

* 修正幾個問題與把外掛支援版本更新

= 1.8.0 =

* 增加對 WordPress 5.0.2 支援度
* 介面翻譯方法修正

= 1.7.9 =

* 增加 Markdown 外掛功能的支援度
* 介面翻譯方法修正

= 1.7.8 =

* 擴充 i18n 功能
* 部分介面修正

= 1.7.7 =

* 修正 Facebook 客戶聊天外掛替換 SDK 路徑

= 1.7.6 =

* 修正 Facebook JS SDK 留言外掛參數讀取頁面路徑為必填
* 提升使用者預設 JS SDK 版本 v3.1，避免早期 v2.x 版的支援問題
* 如果有問題，我應該會收到 feedback 吧（？），不想被主動提升版本可以自己設定回去，其實設定都是自己可以來的，我幫你一把而已。

= 1.7.5 =

* 新增 Webhooks 紀錄搜尋功能，省得一頁一頁找！
* 更新支援的 Graph API 版本到 v3.1

= 1.7.4 =

* 感謝 Kdiag Haci 網友提交可能的安全性風險。已完成修復設定頁的 CSRF issue

= 1.7.3 =

* 更新儲存設定時發生的 Undefined index 問題

= 1.7.2 =

* 例行更新，感謝 @alexclassroom 提出使用流程描述的修正以及一些小細節調整與更新。

= 1.7.1 =

* 依照 Facebook 文件更新外掛規格，讓顧客聊天外掛功能完整！（可更新顯示字眼、設定顯示模式、樣式）

= 1.7.0.4 =

* 奇妙的「修正留言」通知 BUG 解了，但還有粉絲頁版本不同所延伸回傳資料不同的問題，此版本新增相容性修正。
* 優化下載 Facebook 抓圖機制程式碼
* 更新說明文件中的 Screenshots

= 1.7.0.1 =

* 補上「更新留言」案例也同步回網站
* 修正快取功能，讓同一人的留言討論串也能確定匯入

= 1.7.0 =

* 新增快取功能，解決重複訂閱或是 Facebook 重複發送請求導致匯入重複內容方法
* 新增 Facebook 發文轉貼文章，留言同步回網站功能
* 更新常見問題之「Facebook 留言工具消失除錯方法」

= 1.6.0.1 =

* 感謝 [Alex](https://profiles.wordpress.org/alexclassroom/) 回報粉絲頁回傳格式差異性以及外掛說明文件問題
* 解決 API 回傳 JSON 格式差異性問題
* 更新 API 請求版本統一為： v2.11
* 解決短碼產生格式有誤問題
* 新增判斷 Facebook 圖片失聯重新請求過的方法
* 新增預防使用者於逗點分隔名單中多輸入空白的方法

= 1.6.0 =

* 移除 2018/02/05 失效 的[傳送功能](https://developers.facebook.com/docs/plugins/send-button)
* 新增訊息交接模式的控制項

= 1.5.9.1 =

* 修正 PHP5.4 向下相容問題
* 有用戶 Feedback 問題我就來更新，再小都要更新！ 真要給自己一個愛的鼓勵（毆）
* 歡迎有任何相關問題都跟我說拉～

= 1.5.9 =

* 實作 [pass_thread_control](https://developers.facebook.com/docs/messenger-platform/handover-protocol/pass-thread-control#page_inbox) 機制，達成機器人與真人切換的目的，本次實作需搭配粉絲頁上的設定，詳情請看外掛設定頁！
* 新增 `fb2wp_messenger_postback_respond` 事件，支援程式化採用 `postback` 後的回應
* 新增預設實作切換機器人與管理員的功能的 `postback` ，機器人是真正的不再插嘴，而不是靜音惹！（前一版的更新是支援沒做粉絲頁設定用的）

= 1.5.8 =

* 改前版的錯字(囧～)
* 補上讓機器人休息，不要在訊息中插嘴的「bye bot」與喚醒的「hi bot」指令

= 1.5.7.1 =

* 解決更新之漏網小魚Q_Q

= 1.5.7 =

* 新增 Facebook [顧客聊天外掛](https://developers.facebook.com/docs/messenger-platform/reference/web-plugins/#customer_chat)功能
* 東修西修一下，優化外掛（改預設不顯示同步回的圖片描述、程式碼整理、除錯紀錄時間格式化）

= 1.5.6 =

* 新增了一個早該新增的功能：確認是否啟用訊息功能
* 新增訊息功能白名單機制，用來App被公開後的私測

= 1.5.5 =
* 修正可能會導致更新失敗的錯誤：未定義 deactivate_plugins 方法
* 新增掌控粉絲頁訊息最後回覆的過濾事件 `fb2wp_messenger_full_respond_call` ，可以在此事件任意包裝要回覆的內容
* 更新 Facebook Messenger Platform 2.2 明年五月必須更新的 `messaging_type` 條件
* 調整架構為適合多訊息回覆模式，最大彈性支援回覆訊息種類與方法

= 1.5.4 =
* 修正方法引用的寫法避免警告出現
* 新增發文或更新文章時同步清除 Facebook 快取的功能，有更新文章時就不用擔心臉書還在快取舊文了！

= 1.5.3 =
* 新增匯入粉絲頁評價功能，最多匯入 100 筆！

= 1.5.0 =
* 新增同步粉絲頁評價功能，使用 `[mxp_fb2wp_display_ratings]` 短碼在網站中顯示新同步的評價
* 修正選單用詞
* 新增贊助連結
* 修正設定頁樣式，感謝[小豬](https://piglife.tw/)協助補完
* 根據外掛開發指標，強化外掛頁面安全性，禁止對外直接存取檔案
* 新增 Facebook 小工具擺放位置的選項：文章內容上方、文章內容下方

= 1.4.9 =
* 新增 `fb2wp_comment_event` 事件方法，讓粉絲頁留言也能捕捉，使開發者能藉此方法建立自動回覆機制（註：要自動回覆需要 publish_pages, manage_pages 權限）
* 移除 Facebook 太久都沒修好的「語系」資料，改為手動！！！
* 更新後端請求 FB 的 API 版本為 `v2.10`

= 1.4.8 =
* 修正 DEBUG 模式下顯示的錯誤資訊
* 因應 Facebook Webhooks 這次[故障](https://developers.facebook.com/bugs/463793280620151/)與[語系文件遺失](https://developers.facebook.com/bugs/1836827343245862/)補上追蹤原始請求紀錄和錯誤判斷

= 1.4.7.4 =
* 為了避免「限定FB使用者投稿」功能誤會，預將粉絲頁編號設定為開放
* 修正問與答內容

= 1.4.7.3 =
* 向下支援4.7以下版本部分功能，避免無 WP_REST_Controller 類別產生致命錯誤
* 把設定頁面的程式碼整理了一下ＱＱ

= 1.4.7.2 =
* 寫新功能發現舊功能的BUG，更新了錯字問題
* 強化 `fb2wp_match_respond_call` 與 `fb2wp_fuzzy_respond_call` 兩個事件的完整性

= 1.4.7.1 =
* 修正FB訊息 hook 事件處理方法

= 1.4.7 =
* 延伸 Messenger 自動回覆功能彈性，可程式化設定 `fb2wp_match_respond_call`, `fb2wp_fuzzy_respond_call` 兩組事件，強化回覆內容彈性
* 補強說明文件
* 更新快照圖片

= 1.4.6 =
* 終於把待辦事項中第一項「將使用者所輸入的訊息參數化」給完成
* 修正一些錯字小問題

= 1.4.5 =
* 修正FB留言模組跟隨在任意有實作留言模板區塊文後，透過後台內建管理開通留言與否設定
* 新增Facebook小工具上方描述設定
* 新增Facebook儲存外掛大小按鈕設定

= 1.4.4 =
* 新增問與答，關於設定同步功能部份
* 新增設定「粉絲頁編號」
* 新增 fb:pages, fb:app_id 的 head meta 值

= 1.4.3.1 =
* 修正版本比對問題
* 修正後台設定選項失靈

= 1.4.3 =
* 修正後台輸出因html標籤，導致顯示錯誤，避免被自己XSS
* 新增FB引言功能
* 新增FB儲存文章,傳送,留言功能
* 修正前台文章附件內容輸出，提高安全性
* 新增移除外掛是否刪除設定選項

= 1.4.2 =
* 新增上傳的圖片自動設定為該發文的特色圖片，相容 schemapress 所產生的 JSON-LD 資料
* 修正 Messenger Webhook 傳來資料的判斷式

= 1.4.1 =
* 移除 Microdata JSON-LD 支援，避免造成 Google Search Console 結構化資料判斷錯誤

= 1.4.0 =
* 改良更新方法，確保各版本升級時不會有問題。

= 1.3.9 =
* 解決 `PHP Deprecated:  Non-static method Mxp_FB2WP::get_instance() should not be called statically` 警示

= 1.3.8 =
* 優化一些寫法

= 1.3.7 =
* 新增FB發文使用自訂標籤（#tag）停止該篇同步發文

= 1.3.6 =
* 更新簡寫陣列（[]）的寫法，向下相容PHP版本
* 新增功能：tag 中包含完整分類字眼就將發文加入該分類

= 1.3.5 =
* 更新外掛描述頁面資訊

= 1.3.4 =
* 2017.01.05
* 提交

更早版本略- -

== Upgrade Notice ==

無