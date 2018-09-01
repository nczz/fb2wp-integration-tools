<?php
if (!defined('WPINC')) {
	die;
}
?>
<h1>說明</h1>
<p>系統收到來自 Facebook 的資料，偵測特定事件記錄在此呈現</p>
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