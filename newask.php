<?php
/*
Plugin Name: 子比考试系统 (Zibll Theme NewAsk Admin)
Plugin URI: https://github.com/znc15/Zibll-Newpostask
Description: 为子比主题添加考试功能 (Add exam function to Zibll Theme)
Version: 1.0.0
Author: YangXiaoMian
Author URI: https://www.littlesheep.cc
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}
register_activation_hook(__FILE__, 'plugin_activate');

function plugin_activate() { 
	$sql1 = "
	CREATE TABLE `fl_ask_tm` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`type` varchar(255) NOT NULL,
	`name` longtext NOT NULL,
	`all_answer` longtext DEFAULT NULL,
	`answer` varchar(255) DEFAULT NULL,
	`tips` longtext DEFAULT NULL,
	`score` int(255) NOT NULL,
	PRIMARY KEY (`ID`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
	";

	$sql2 = "
	CREATE TABLE `wp_fl_meta` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`meta_id` varchar(255) CHARACTER SET utf8mb4 COLLATE=utf8mb4_unicode_520_ci NOT NULL,
	`meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE=utf8mb4_unicode_520_ci NOT NULL,
	`meta_var` longtext CHARACTER SET utf8mb4 COLLATE=utf8mb4_unicode_520_ci NOT NULL,
	PRIMARY KEY (`ID`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
	";

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if($wpdb->get_var("SHOW TABLES LIKE 'wp_fl_meta'") != 'wp_fl_meta'){
		dbDelta($sql2);
	}
	if($wpdb->get_var("SHOW TABLES LIKE 'fl_ask_tm'") != 'fl_ask_tm'){
		dbDelta($sql1);
	}
}

// 添加菜单挂钩
add_action('admin_menu', 'newask_menu');

function newask_menu(){
	add_menu_page('考试管理', '考试管理', 'administrator','newask', 'newask_page', 'dashicons-admin-tools', 90);
	add_submenu_page('newask', '添加题目', '添加题目', 'administrator', 'add_question', 'add_question_page');
	add_submenu_page('newask', '管理题目', '管理题目', 'administrator', 'manage_questions', 'manage_questions_page');
	add_submenu_page('newask', '检查更新', '检查更新', 'administrator', 'check_update', 'check_update_page');
}

function check_github_update() {
	$repo_url = 'https://api.github.com/repos/znc15/Zibll-Newpostask/releases/latest';
	$response = wp_remote_get($repo_url, array('headers' => array('User-Agent' => 'WordPress Plugin')));

	if (is_wp_error($response)) {
		return array('error' => '无法获取更新信息');
	}

	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	if (isset($data['tag_name']) && isset($data['html_url']) && isset($data['body'])) {
		return array(
			'tag_name' => $data['tag_name'],
			'html_url' => $data['html_url'],
			'body' => $data['body']
		);
	} else {
		return array('error' => '无法获取更新信息');
	}
}

function check_update_page() {
	$current_version = '1.0.0'; // 当前插件版本
	$latest_version_info = array();
	$latest_version = '';
	$download_url = '';
	$update_log = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_update'])) {
		$latest_version_info = check_github_update();
		if (isset($latest_version_info['tag_name'])) {
			$latest_version = $latest_version_info['tag_name'];
			$download_url = $latest_version_info['html_url'];
			$update_log = $latest_version_info['body'];
		} else {
			$latest_version = $latest_version_info['error'];
		}
	}

	echo '
	<div class="wrap">
		<h1>检查更新</h1>
		<p>当前版本: ' . esc_html($current_version) . '</p>';

	if ($latest_version) {
		if ($latest_version == $current_version) {
			echo '<p>当前为最新版本</p>';
		} else {
			echo '<p>最新版本: ' . esc_html($latest_version) . '</p>';
			if ($download_url) {
				echo '<p><a href="' . esc_url($download_url) . '" target="_blank" class="button button-primary">下载最新版本</a></p>';
			}
		}
		if ($update_log) {
			echo '<h2>更新日志</h2>';
			echo '<pre>' . esc_html($update_log) . '</pre>';
		}
	}

	echo '
		<form method="post">
			<input type="submit" name="check_update" id="check_update" class="button button-primary" value="检查更新">
		</form>
	</div>';
}

function newask_page(){
	if (!is_super_admin()) {
		wp_die('您不能访问此页面', '权限不足');
		exit;
	}

	$newask = get_fl_meta_key_all('newask');
	if(empty($newask)){
		echo '
		<div class="wrap">
			<h1>考卷管理</h1>
			<p>当前没有考卷</p>
		</div>';
		return;
	}

	echo '
	<div class="wrap">
		<h1>考卷管理</h1>
	';

	if($_GET['set'] == ''){
		if ($newask != ''){
			if ($_GET['pageed'] != '' && $_GET['pageed'] != '0') { //获取当前页码
				$page = $_GET['pageed'];
			}elseif($_GET['pageed'] == '0'){
				echo '
				<main role="main" class="container">
				<div class="zib-widget hot-posts">
				<p>暂无数据。</p>
				</div></main>';
				return;
			}else{
				$page = '1';
			}
			$syy = intval($page) - 1;
			$xyy = intval($page) + 1;
			$z = ceil(count($newask) / 20);
			if($page > $z){
				echo '
				<main role="main" class="container">
				<div class="zib-widget">
				<p>暂无数据。</p>
				</div></main>';
				return;
			}
			$html = '
		<link rel="stylesheet" id="_bootstrap-css" href="/wp-content/themes/zibll/css/bootstrap.min.css?ver=6.9.1" type="text/css" media="all">
		<link rel="stylesheet" id="_fontawesome-css" href="/wp-content/themes/zibll/css/font-awesome.min.css?ver=6.9.1" type="text/css" media="all">
		<link rel="stylesheet" id="_main-css" href="/wp-content/themes/zibll/css/main.min.css?ver=6.9.1" type="text/css" media="all">
		<link rel="stylesheet" id="_forums-css" href="/wp-content/themes/zibll/inc/functions/bbs/assets/css/main.min.css?ver=6.9.1" type="text/css" media="all">
		<main role="main" class="container">
		<div class="zib-widget hot-posts">
		<style>
		table tbody td {
			text-overflow: ellipsis;
			white-space: nowrap;
			overflow: hidden;		
		}
		.tablenav-pages {
			display: flex;
			justify-content: center;
			margin-top: 20px;
		}
		.tablenav-pages .pagination-links a,
		.tablenav-pages .pagination-links span,
		.tablenav-paging-text {
			margin: 5px;
		}
	</style>
		<table style="table-layout: fixed; border-collapse:separate; border-spacing:10px;">
		<tr>
		<th class="title-h-left">用户ID</th>
		<th class="title-h-left">昵称 · 邮箱</th>
		<th class="title-h-left">考试状态 · 最后分数</th>
		<th class="title-h-left">初考时间</th>
		<th class="title-h-left">更新时间</th>
		<th class="title-h-left">操作</th>
		</tr>';
			$html .= showpage($page);
			$html .= '</table>
			</div>
			<div style="text-align:center;">
				<div><b>Design By <a href="https://acg.la">ACG.LA</a> | Power By <a href="https://www.littlesheep.cc">LittleSheep</a></b></div>
			</main>';

			$html .= '<div class="tablenav-pages"><div class="pagination-links">';

			if($page != '1'){
				$html .= '<a class="first-page button" href="/wp-admin/admin.php?page=newask"><span class="screen-reader-text">首页</span><span aria-hidden="true">«</span></a>';
				$html .= '<a class="prev-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $syy . '"><span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span></a>';
			} else {
				$html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
				$html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
			}

			$html .= '<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第' . $page . '页，共<span class="total-pages">' . $z . '</span>页</span></span>';

			if($page != $z){
				$html .= '<a class="next-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $xyy . '"><span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span></a>';
				$html .= '<a class="last-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $z . '"><span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span></a>';
			} else {
				$html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
				$html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
			}

			$html .= '</div></div>';

			echo $html;
		}else{
			echo '
			<main role="main" class="container">
			<div class="zib-widget hot-posts">
			<p>暂无数据。</p>
			</div></main>';
		}
	}else{
		$uid = $_GET['set'];
		echo '
		<main role="main" class="container">
		<form action="/newask?action=ans_check" method="post">
		</form></div>';
	}
}

// 获取用户元数据
function get_fl_user_meta($meta_id, $meta_key){
	global $wpdb;
	$sql_ck = $wpdb->prepare("SELECT * FROM `wp_fl_meta` WHERE `meta_id` = %s AND `meta_key` = %s", $meta_id, $meta_key);
	$row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
	return $row_ck ? $row_ck['meta_var'] : '无数据';
}

// 获取所有元数据
function get_fl_meta_key_all($meta_key){
	global $wpdb;
	$sql_ck = $wpdb->prepare("SELECT * FROM `wp_fl_meta` WHERE `meta_key` = %s", $meta_key);
	$row_ck = $wpdb->get_results($sql_ck, ARRAY_A);
	return $row_ck;
}

// 显示页面
function showpage($page){
	global $wpdb;
	$newask = get_fl_meta_key_all('newask');
	$html = '';
	$s = $page * 20;
	$d = $s - 20;
	if($page == '1'){
		for($i = 0;count($newask) >= $i;$i++){
			if($i == 20){
				break;
			}
			$arr = $newask[$i];
			if($arr != ''){
				$user_info = get_userdata($arr['meta_id']);
				$zt = '';
				if($arr['meta_var'] == '1'){
					$zt = '已通过';
				}elseif($arr['meta_var'] == '-1'){
					$zt = '分数不达标';
				}elseif($arr['meta_var'] == '-2'){
					$zt = '封禁权限';
				}
				$uid = $arr['meta_id'];
				$sql_soc = $wpdb->prepare("SELECT * FROM `wp_fl_meta` WHERE `meta_id` = %s AND `meta_key` = 'newask_score'", $uid);
				$row_soc = $wpdb->get_row($sql_soc, ARRAY_A);
				$soc = $row_soc ? $row_soc['meta_var'] : '无数据';
				$html .= '
				<tr>
				<td width="10%">' . esc_html($arr['meta_id']) . '</td>
				<td width="20%">' . esc_html($user_info->user_nicename) . ' · ' . esc_html($user_info->user_email) . '</td>
				<td width="20%">' . esc_html($zt) . ' (' . esc_html($soc) . '分)</td>
				<td width="20%">' . esc_html(get_fl_user_meta($arr['meta_id'], 'newask_in_time')) . '</td>
				<td width="20%">' . esc_html(get_fl_user_meta($arr['meta_id'], 'newask_up_time')) . '</td>
				<td width="20%"><span class="but" data-clipboard-text="编辑"><a href="/wp-admin/admin.php?page=newask&set=' . esc_html($arr['meta_id']) . '">编辑</a></span> <span class="but" data-clipboard-text="查看试卷"><a href="/newask?action=ck&uid=' . esc_html($arr['meta_id']) . '">查看试卷</a></span></td>
				</tr>';
			}		
		}
	}else{
		for($i = $d;count($newask) >= $i;$i++){
			if($i >= $s){
				break;
			}
			$arr = $newask[$i];
			if($arr != ''){
				$user_info = get_userdata($arr['meta_id']);
				$zt = '';
				if($arr['meta_var'] == '1'){
					$zt = '已通过';
				}elseif($arr['meta_var'] == '-1'){
					$zt = '分数不达标';
				}elseif($arr['meta_var'] == '-2'){
					$zt = '封禁权限';
				}
				$uid = $arr['meta_id'];
				$sql_soc = $wpdb->prepare("SELECT * FROM `wp_fl_meta` WHERE `meta_id` = %s AND `meta_key` = 'newask_score'", $uid);
				$row_soc = $wpdb->get_row($sql_soc, ARRAY_A);
				$soc = $row_soc ? $row_soc['meta_var'] : '无数据';
				$html .= '
				<tr>
				<td width="10%">' . esc_html($arr['meta_id']) . '</td>
				<td width="20%">' . esc_html($user_info->user_nicename) . ' · ' . esc_html($user_info->user_email) . '</td>
				<td width="20%">' . esc_html($zt) . ' (' . esc_html($soc) . '分)</td>
				<td width="20%">' . esc_html(get_fl_user_meta($arr['meta_id'], 'newask_in_time')) . '</td>
				<td width="20%">' . esc_html(get_fl_user_meta($arr['meta_id'], 'newask_up_time')) . '</td>
				<td width="20%"><span class="but" data-clipboard-text="编辑"><a href="/wp-admin/admin.php?page=newask&set=' . esc_html($arr['meta_id']) . '">编辑</a></span> <span class="but" data-clipboard-text="查看试卷"><a href="/newask?action=ck&uid=' . esc_html($arr['meta_id']) . '">查看试卷</a></span></td>
				</tr>';
			}
		}
	}
	return $html;
}

function add_question_page(){
	if (!is_super_admin()) {
		wp_die('您不能访问此页面', '权限不足');
		exit;
	}

	global $wpdb;
	$edit_mode = false;
	$question = array(
		'type' => '',
		'name' => '',
		'all_answer' => '',
		'answer' => '',
		'tips' => '',
		'score' => ''
	);

	if(isset($_GET['edit'])){
		$edit_mode = true;
		$edit_id = intval($_GET['edit']);
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM fl_ask_tm WHERE ID = %d", $edit_id), ARRAY_A);
		if(!$question){
			echo '<div class="error"><p>题目不存在</p></div>';
			return;
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
		$type = sanitize_text_field($_POST['type']);
		$name = sanitize_textarea_field($_POST['name']);
		$all_answer = sanitize_textarea_field($_POST['all_answer']);
		$answer = sanitize_text_field($_POST['answer']);
		$tips = sanitize_textarea_field($_POST['tips']);
		$score = intval($_POST['score']);
		
		if($edit_mode){
			$wpdb->update(
				'fl_ask_tm',
				array(
					'type' => $type,
					'name' => $name,
					'all_answer' => $all_answer,
					'answer' => $answer,
					'tips' => $tips,
					'score' => $score
				),
				array('ID' => $edit_id)
			);
			echo '<div class="updated"><p>题目已更新</p></div>';
		}else{
			$wpdb->insert(
				'fl_ask_tm',
				array(
					'type' => $type,
					'name' => $name,
					'all_answer' => $all_answer,
					'answer' => $answer,
					'tips' => $tips,
					'score' => $score
				)
			);
			echo '<div class="updated"><p>题目已添加</p></div>';
		}
	}

	echo '
	<div class="wrap">
		<h1>' . ($edit_mode ? '编辑题目' : '添加题目') . '</h1>
		<form method="post">
			<table class="form-table">
				<tr>
					<th><label for="type">题目类型</label></th>
					<td><input type="text" name="type" id="type" class="regular-text" value="' . esc_attr($question['type']) . '" required></td>
				</tr>
				<tr>
					<th><label for="name">题目内容</label></th>
					<td><textarea name="name" id="name" class="regular-text" rows="5" required>' . esc_textarea($question['name']) . '</textarea></td>
				</tr>
				<tr>
					<th><label for="all_answer">所有选项</label></th>
					<td><textarea name="all_answer" id="all_answer" class="regular-text" rows="5" required>' . esc_textarea($question['all_answer']) . '</textarea></td>
				</tr>
				<tr>
					<th><label for="answer">正确答案</label></th>
					<td><input type="text" name="answer" id="answer" class="regular-text" value="' . esc_attr($question['answer']) . '" required></td>
				</tr>
				<tr>
					<th><label for="tips">提示</label></th>
					<td><textarea name="tips" id="tips" class="regular-text" rows="5" required>' . esc_textarea($question['tips']) . '</textarea></td>
				</tr>
				<tr>
					<th><label for="score">分数</label></th>
					<td><input type="number" name="score" id="score" class="regular-text" value="' . esc_attr($question['score']) . '" required></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="' . ($edit_mode ? '更新题目' : '添加题目') . '">
			</p>
		</form>
	</div>';
}

function manage_questions_page(){
	if (!is_super_admin()) {
		wp_die('您不能访问此页面', '权限不足');
		exit;
	}

	global $wpdb;
	$questions = $wpdb->get_results("SELECT * FROM fl_ask_tm", ARRAY_A);

	echo '
	<div class="wrap">
		<h1>管理题目</h1>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" id="id" class="manage-column column-id">ID</th>
					<th scope="col" id="type" class="manage-column column-type">题目类型</th>
					<th scope="col" id="name" class="manage-column column-name">题目内容</th>
					<th scope="col" id="all_answer" class="manage-column column-all_answer">所有选项</th>
					<th scope="col" id="answer" class="manage-column column-answer">正确答案</th>
					<th scope="col" id="tips" class="manage-column column-tips">提示</th>
					<th scope="col" id="score" class="manage-column column-score">分数</th>
					<th scope="col" id="actions" class="manage-column column-actions">操作</th>
				</tr>
			</thead>
			<tbody>';

	foreach($questions as $question){
		echo '
		<tr>
			<td>' . esc_html($question['ID']) . '</td>
			<td>' . esc_html($question['type']) . '</td>
			<td>' . esc_html($question['name']) . '</td>
			<td>' . esc_html($question['all_answer']) . '</td>
			<td>' . esc_html($question['answer']) . '</td>
			<td>' . esc_html($question['tips']) . '</td>
			<td>' . esc_html($question['score']) . '</td>
			<td>
				<a href="' . admin_url('admin.php?page=add_question&edit=' . $question['ID']) . '">编辑</a> |
				<a href="' . admin_url('admin.php?page=manage_questions&delete=' . $question['ID']) . '">删除</a>
			</td>
		</tr>';
	}

	echo '
			</tbody>
		</table>
	</div>';

	if(isset($_GET['delete'])){
		$delete_id = intval($_GET['delete']);
		$wpdb->delete('fl_ask_tm', array('ID' => $delete_id));
		echo '<div class="updated"><p>题目已删除</p></div>';
	}
}

