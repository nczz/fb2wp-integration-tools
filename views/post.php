<?php
if (!defined('WPINC')) {
	die;
}
?>
<h2><?php esc_html_e('Feature description','fb2wp-integration-tools'); ?></h2> 
<p><?php esc_html_e('When the system receives data sent by Facebook, it will detect certain events and display them here.'); ?></p> 
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