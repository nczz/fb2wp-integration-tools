<?php
if (!defined('WPINC')) {
	die;
}
?>
<h2><?php esc_html_e('Feature description','fb2wp-integration-tools'); //這裡我把 h1 也順便改成 h2 了?></h2> 
<p><?php esc_html_e('When the system receives data sent by Facebook, it will detect certain events and display them here.'); //系統收到來自 Facebook 的資料，偵測特定事件記錄在此呈現?></p> 
<p></p>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
<div id="table"></div>