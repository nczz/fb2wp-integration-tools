<?php
if (!defined('WPINC')) {
	die;
}
?>
<h1>說明</h1>
<p>系統比對流程為先精準再模糊，依序由上往下，請自行規劃好對話動線。</p>
<h1>精準比對(比對訊息來源完全一致才觸發)</h1>
<p><div id="match"></div></p>
<p><button id="add_match" class="button button-primary">新增</button> | <button id="save_match" class="button button-primary">儲存</button></p>
<h1>模糊比對(支援正規表示法)</h1>
<p><div id="fuzzy"></div></p>
<p><button id="add_fuzzy" class="button button-primary">新增</button> | <button id="save_fuzzy" class="button button-primary">儲存</button></p>