<?php
/*
Plugin Name: 子比考试系统 (Zibll Theme NewAsk Admin)
Plugin URI: https://github.com/znc15/Zibll-Newpostask
Description: 为子比主题添加考试功能 (Add exam function to Zibll Theme)
Version: 1.3.0
Author: YangXiaoMian
Author URI: https://www.littlesheep.cc
*/
error_reporting(E_ALL & ~E_NOTICE);

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

INSERT INTO `fl_ask_tm` (`ID`, `type`, `name`, `all_answer`, `answer`, `tips`, `score`) VALUES
(1, '标题规范题', '以下图包投稿类标题哪个是正确的？', '【Fantia/图包/无修正】JIMA大佬22.10-23.01作品合集（36P/560M）|【Fantia/图包/无修正】JIMA大佬22.10-23.01作品合集（560M） |【Fantia/图包/无修正】JIMA大佬22.10-23.01作品合集（36P）|【Fantia/图包/无修正】JIMA大佬22.10-23.01作品合集', 'A', '规范中表明了投稿标题格式应为：【分类】资源名称【数量/大小】，非图片/视频类投稿因标注大小，求物类因标注分类。', 5),
(2, '内容规范题', '以下投稿所设置的解压密码是否存在违规？如果有，请选出违规密码。', 'boluoyyds|acg.la|flcy.us|pornhub.com', 'D', '投稿规范中说明了解压密码推荐使用acg.la或本站网址，禁止直接使用其他网站的网址为解压密码，可以自行更改解压码为acg.la后压缩上传 （如果你是搬运，最好经过原主同意）', 10),
(3, '内容规范题', '用以下哪种网盘进行分享的文件是不符合本站要求的？', '百度网盘|谷歌网盘|磁力链接|城通网盘', 'D', '本提请参考投稿内容规范，规范内明确注明了网盘链接分享规范。', 5),
(4, '内容规范题', '以下哪种情况下，打码合格？', '可以看到乳头形状的投稿|色块、图片遮挡|用国家领导人的照片等涉及政治敏感信息的图片进行打码的投稿|半透明马赛克打码的投稿', 'B', '根据预览规范第四条规定：打码 不打码的一次警告二次封禁 敏感的三点一点都不能露，露出部分X头、鲍鱼都是违规打码！ 建议使用图片、圣光遮挡，一般的马赛克容易被打回。', 10),
(5, '基本常识题', 'RJ号/DMM编号实质是什么？', '并无实质作用|DLsite/DMM发售作品时给予作品的唯一编号（也位于链接处），可以作为方便审核确认站内是否撞车的依据|等同于商业作的发售日期|卡小白投稿的没用玩意', 'B', '请自行GOOGLE了解。', 5),
(6, '内容规范题', '以下哪种Q群的宣传可以附在本站投稿正文中?', '某某官方资源发布群|UP自己加入的某个游戏的交流群|汉化组的工作交流群、工作群（带广告性质的收费群除外）|UP自己建立的资源内部发布群', 'C', '本站禁止发布任何带有收费或广告性质的内容，群组推广。', 10),
(7, '内容规范题', '以下哪种投稿的正文简介符合本站的最低要求', '与资源内容相关的极为简短的介绍，如“纯爱、调教、NTR”等|没有提到任何与资源本身内容相关的话题，只是写了一些UP自己近期身边的事|整个正文只有“。。。。。。。。。。。”或者“游戏还行，能玩”|整个正文中只有“解压密码为：acg.la”', 'A', '遵从心意。', 5),
(8, '查重规范题', '以下哪些情况下，投稿不视作撞车/可以正常通过？(送分题)', '同一位UP连续投稿的同一个本子的不同汉化组汉化的版本|站内已有该资源，另一位UP投稿的该资源的度盘分流档|由不同UP投稿的同一个本子的不同汉化组汉化的版本或原资源已经被爆，由另一位UP投的符合站内相关规定的补档投稿|复制粘贴，修改标题直接发布', 'C', '送分题。', 5),
(9, '内容规范题', '以下哪些选项是本站不接受投稿的资源类型？', '岛国大片/AV/未成年|全年龄游戏|全年龄图包|全年龄COS', 'A', '本站禁止发布任何岛国，不知名女优，性爱视频，尤其是未成年，一旦发布，删除并永久封号，绝不姑息。', 5),
(10, '分类规范题', '请选出以下给出选项中分类存在错误的选项', '动画区：【脸肿字幕组】THE ANIMATION 【2V/150M】|图画区：【图包】gawd大佬三月合集【100P/550M】|图画区：【动画】vnk一月MMD新作【100P/550M】|GAME区：【3D】老滚5自整理MOD大合集【80G】', 'C', '分类规范在投稿规范中已明确标明。', 5),
(11, '隐藏内容题', '以下哪个隐藏方式禁止使用？', '评论可见|积分阅读|登录可见|不隐藏', 'A', '请查阅投稿规范。', 5),
(12, '积分设置题', '以下哪些资源积分设置不合理？', '【动画】vnk大佬一月合集【5V/200M】：6积分|【图包】vnk大佬一月合集【50P/200M】：1积分|【RPG】家出少女1.2.10汉化版【200M】：20积分|【动画】vnk大佬所有作品合集【50V/20G】：20积分', 'C', '单个资源（包括单个资源和作者某月的合集）最高限制设置6积分，超过不通过，合集资源（资源作者发布的所有作品合集）最高限制设置20积分，超过不通过！', 10),
(13, '预览规范题', '关于预览图，以下说法正确的是？', '投稿时可以只设置封面不插入预览图至正文|投稿时可以不上传预览图或封面|预览图可以不打码直接上传|投稿时应正确设置封面并将预览图插入至正文，否则会不通过。', 'D', '请查阅投稿规范。', 10),
(14, '标题规范题', '汉化者名字未知时，汉化者一栏应当如何填写？', '直接不写汉化者也可以|匿名汉化者|中国翻译|汉化者不明 或 未知汉化', 'A', '未知汉化资源可以不标明汉化作品来源。', 5),
(15, '内容规范题', '关于投稿以下哪种说法是正确的？', '发布自购资源时需要带上自购证明至正文|搬运他人作品时可以不标注作品来源|发布资源合购信息，邀请大家一起合购|发布资源时只使用诚通网盘作为资源主链接。', 'A', '请查阅投稿规范。', 5),
(16, '内容规范题', '关于资源链接，以下哪些链接是正确的？', 'xxxxxx.com/：7天有效期|xxxxxx.com/：30天有效期|xxxxxx.com/：1天有效期|xxxxxx.com/：永久有效', 'D', '本站明令禁止使用短效链接。', 5),
(17, '压缩规范题', '用以下哪种压缩软件压缩的文件不符合要求的', 'WINRAR|Bandzip|7-ZIP|快压', 'D', '禁止使用任何国产恶意收费压缩软件。', 5);
    ";

    $sql2 = "
    CREATE TABLE `wp_fl_meta` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `meta_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
        `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
        `meta_var` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
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
	add_submenu_page('newask', '导入题库', '导入题库', 'administrator', 'import_questions', 'import_questions_page');
	add_submenu_page('newask', '设置', '设置', 'administrator', 'exam_settings', 'exam_settings_page');
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

    if ($_GET['set'] == '') {
        // 遍历考试数据
        foreach ($newask as $arr) {
            // 获取用户ID
            $user_id = $arr['meta_id'];

            // 扣除积分和余额
            $points_cost = get_option('points_balance_cost', 0);
            $balance_cost = get_option('balance_cost', 0); 

            // 扣除积分
            $current_points = intval(get_user_meta($user_id, 'points', true));
            if ($current_points >= $points_cost && $points_cost > 0) {
                update_user_meta($user_id, 'points', $current_points - $points_cost);
            }

            // 扣除余额
            $current_balance = floatval(get_user_meta($user_id, 'balance', true));
            if ($current_balance >= $balance_cost && $balance_cost > 0) {
                update_user_meta($user_id, 'balance', $current_balance - $balance_cost);
            }

            // 如果考试通过，并且启用了自动升级为会员功能
            if ($arr['meta_var'] == '1' && get_option('enable_vip_upgrade', '0') == '1') {
                // 获取会员级别和有效期设置
                $vip_level = get_option('vip_level', 1); // 默认级别为 1
                $vip_exp_date = get_option('vip_exp_date', 'Permanent'); // 默认到期时间为永久

                // 更新用户的 wp_usermeta 表中的 vip_level 和 vip_exp_date
                update_user_meta($user_id, 'vip_level', $vip_level);
                update_user_meta($user_id, 'vip_exp_date', $vip_exp_date);
            }
        }
    }


	if ($_GET['set'] == ''){
		if ($newask != ''){
			if ($_GET['pageed'] != '' && $_GET['pageed'] != '0') { //获取当前页码
				$page = $_GET['pageed'];
			} elseif($_GET['pageed'] == '0'){
				echo '
				<main role="main" class="container">
				<div class="zib-widget hot-posts">
				<p>暂无数据。</p>
				</div></main>';
				return;
			} else {
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
		<link rel="stylesheet" id="_bootstrap-css" href="/wp-content/themes/zibll/css/bootstrap.min.css?ver=7.7" type="text/css" media="all">
		<link rel="stylesheet" id="_fontawesome-css" href="/wp-content/themes/zibll/css/font-awesome.min.css?ver=7.7" type="text/css" media="all">
		<link rel="stylesheet" id="_main-css" href="/wp-content/themes/zibll/css/main.min.css?ver=7.7" type="text/css" media="all">
		<link rel="stylesheet" id="_forums-css" href="/wp-content/themes/zibll/inc/functions/bbs/assets/css/main.min.css?ver=7.7" type="text/css" media="all">
		<h2>考卷管理</h2>
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
			</div>
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_selected'])) {
		if (!empty($_POST['question_ids'])) {
			$ids_to_delete = implode(',', array_map('intval', $_POST['question_ids']));
			$wpdb->query("DELETE FROM fl_ask_tm WHERE ID IN ($ids_to_delete)");
			echo '<div class="updated"><p>选定的题目已删除</p></div>';
		}
	}

	$questions = $wpdb->get_results("SELECT * FROM fl_ask_tm", ARRAY_A);

	echo '
	<div class="wrap">
		<h1>管理题目</h1>
		<form method="post">
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column">
						<input type="checkbox" id="cb-select-all-1">
					</th>
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
			<th scope="row" class="check-column">
				<input type="checkbox" name="question_ids[]" value="' . esc_attr($question['ID']) . '">
			</th>
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
		<p class="submit">
			<input type="submit" name="delete_selected" id="delete_selected" class="button button-primary" value="删除选定项">
		</p>
		</form>
	</div>';

	if(isset($_GET['delete'])){
		$delete_id = intval($_GET['delete']);
		$wpdb->delete('fl_ask_tm', array('ID' => $delete_id));
		echo '<div class="updated"><p>题目已删除</p></div>';
	}
}

add_action('admin_footer', 'manage_questions_page_js');

function manage_questions_page_js() {
	echo '
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#cb-select-all-1").click(function(){
			var checkedStatus = this.checked;
			$("input[name=\'question_ids[]\']").each(function(){
				$(this).prop("checked", checkedStatus);
			});
		});
	});
	</script>';
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
	require_once plugin_dir_path(__FILE__) . 'includes/Parsedown.php'; // 包含 Parsedown 库

	$current_version = '1.3.0'; // 当前插件版本
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
			$Parsedown = new Parsedown();
			$update_log_html = $Parsedown->text($update_log); // 将 Markdown 解析为 HTML
			echo '<h2>更新日志</h2>';
			echo '<div>' . $update_log_html . '</div>';
		}
	}

	echo '
		<form method="post">
			<input type="submit" name="check_update" id="check_update" class="button button-primary" value="检查更新">
		</form>
	</div>';
}

//会员给予
function exam_settings_page() {
    // 检查权限
    if (!is_super_admin()) {
        wp_die('您不能访问此页面', '权限不足');
        exit;
    }

    // 处理表单提交
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
        $enable_vip_upgrade = isset($_POST['enable_vip_upgrade']) ? '1' : '0';
        $vip_level = intval($_POST['vip_level']); // 获取设置的会员级别
        $vip_exp_date = sanitize_text_field($_POST['vip_exp_date']); // 获取设置的会员到期时间
        $require_points = isset($_POST['require_points']) ? '1' : '0'; // 新增：是否需要积分
        $require_balance = isset($_POST['require_balance']) ? '1' : '0'; // 新增：是否需要余额
        // 新增：保存积分和余额扣除设置
        $points_balance_cost = intval($_POST['points_balance_cost']);
        $balance_cost = intval($_POST['balance_cost']);
        
        update_option('enable_vip_upgrade', $enable_vip_upgrade);
        update_option('vip_level', $vip_level); // 保存会员级别
        update_option('vip_exp_date', $vip_exp_date); // 保存会员有效期
        update_option('require_points', $require_points); // 保存积分设置
        update_option('require_balance', $require_balance); // 保存余额设置
        // 新增：保存扣除设置
        update_option('points_balance_cost', $points_balance_cost);
        update_option('balance_cost', $balance_cost);
        
        // 新增：保存考试介绍内容，允许 HTML
        $exam_intro = wp_kses_post($_POST['exam_intro']); // 允许 HTML
        update_option('exam_intro', $exam_intro); // 保存考试介绍内容

        echo '<div class="updated"><p>设置已保存</p></div>';
    }

    // 获取当前设置
    $enable_vip_upgrade = get_option('enable_vip_upgrade', '0');
    $vip_level = get_option('vip_level', 1); // 默认级别为 1
    $vip_exp_date = get_option('vip_exp_date', 'Permanent'); // 默认到期时间为永久
    $require_points = get_option('require_points', '0'); // 获取积分设置
    $require_balance = get_option('require_balance', '0'); // 获取余额设置
    // 新增：获取扣除设置
    $points_balance_cost = get_option('points_balance_cost', 0);
    $balance_cost = get_option('balance_cost', 0);
    $exam_intro = get_option('exam_intro', ''); // 获取考试介绍内容

    // 新增：设置默认内容
    if (empty($exam_intro)) {
        $exam_intro = '<p><b>考试满分为：120+分，获得投稿发帖权限需要总分达到90分以上。</b></p>
    <p><b>请您认真查看每一道的答案并牢记于心，若是后续发布违规，不标准投稿将会永久取消发布投稿权限。</b></p>
    <p><b>您通过考试后发布投稿，帖子将不在需要审核。</b></p>';
    }

    // 输出设置页面表单
    echo '
    <div class="wrap">
        <h1>考试设置</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="enable_vip_upgrade">考试通过后自动升级为会员</label></th>
                    <td>
                        <input type="checkbox" name="enable_vip_upgrade" id="enable_vip_upgrade" value="1" ' . checked($enable_vip_upgrade, '1', false) . '>
                        <label for="enable_vip_upgrade">启用</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="vip_level">会员级别</label></th>
                    <td>
                        <input type="number" name="vip_level" id="vip_level" value="' . esc_attr($vip_level) . '" min="1" max="2">
                        <p class="description">会员级别，1为普通会员，2为高级会员。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="vip_exp_date">会员有效期</label></th>
                    <td>
                        <input type="text" name="vip_exp_date" id="vip_exp_date" value="' . esc_attr($vip_exp_date) . '">
                        <p class="description">设置会员的有效期，比如 "Permanent" 表示永久，或者可以设置为具体的时间，如 "2025-12-31"。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="require_points">答题是否需要积分</label></th>
                    <td>
                        <input type="checkbox" name="require_points" id="require_points" value="1" ' . checked($require_points, '1', false) . '>
                        <label for="require_points">启用</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="require_balance">答题是否需要余额</label></th>
                    <td>
                        <input type="checkbox" name="require_balance" id="require_balance" value="1" ' . checked($require_balance, '1', false) . '>
                        <label for="require_balance">启用</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="points_balance_cost">答题扣除积分</label></th>
                    <td>
                        <input type="number" name="points_balance_cost" id="points_balance_cost" value="' . esc_attr($points_balance_cost) . '" min="0">
                        <p class="description">设置每次答题扣除的积分数量。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="balance_cost">答题扣除余额</label></th>
                    <td>
                        <input type="number" name="balance_cost" id="balance_cost" value="' . esc_attr($balance_cost) . '" min="0">
                        <p class="description">设置每次答题扣除的余额数量。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="exam_intro">考试介绍内容</label></th>
                    <td>
                        <textarea name="exam_intro" id="exam_intro" class="regular-text" rows="5">' . esc_textarea($exam_intro) . '</textarea>
                        <p class="description">设置考试页面的介绍内容。</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="save_settings" id="save_settings" class="button button-primary" value="保存设置">
            </p>
        </form>
    </div>';
}

//题目批量上传
function import_questions_page() {
    // 检查权限
    if (!is_super_admin()) {
        wp_die('您不能访问此页面', '权限不足');
        exit;
    }

    // 处理文件上传和解析
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['questions_file'])) {
        $file = $_FILES['questions_file'];

        // 检查文件是否上传成功
        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $file['tmp_name'];
            $file_content = file_get_contents($file_tmp_path);

            // 调用解析函数来处理文件内容
            $import_result = import_questions_from_txt($file_content);

            if ($import_result) {
                echo '<div class="updated"><p>题目导入成功！</p></div>';
            } else {
                echo '<div class="error"><p>题目导入失败，请检查文件格式。</p></div>';
            }
        } else {
            echo '<div class="error"><p>文件上传失败，请重试。</p></div>';
        }
    }

    // 输出文件上传表单和格式说明
    echo '
    <div class="wrap">
        <h1>导入题库</h1>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="questions_file">选择题库文件 (.txt)</label></th>
                    <td>
                        <input type="file" name="questions_file" id="questions_file" accept=".txt" required>
                        <p class="description">请上传 .txt 文件，题目格式需符合以下要求。</p>
                    </td>
                </tr>
            </table>

            <h3>题目格式说明：</h3>
            <p>每行表示一个题目，字段之间使用 <strong>|</strong> 分隔，字段顺序如下：</p>
            <pre>
题目类型 | 题目内容 | 选项1 | 选项2 | 选项3 | 选项4 | 正确答案 | 提示 | 分数
            </pre>
            <p>示例：</p>
            <pre>
标题规范题 | 以下图包投稿类标题哪个是正确的？ | 选项A | 选项B | 选项C | 选项D | A | 标题规范提示 | 5
内容规范题 | 解压密码是否违规？ | 选项A | 选项B | 选项C | 选项D | D | 解压提示 | 10
            </pre>

            <p class="submit">
                <input type="submit" name="import_questions" id="import_questions" class="button button-primary" value="导入题目">
            </p>
        </form>
    </div>';
}

// 解析上传的 .txt 文件并导入题目到数据库
function import_questions_from_txt($file_content) {
    global $wpdb;

    // 分割文件内容为行，每行表示一个题目
    $lines = explode(PHP_EOL, $file_content);

    foreach ($lines as $line) {
        // 分割每一行的题目数据
        $fields = explode('|', $line);

        // 确保每行有足够的字段
        if (count($fields) < 8) {
            continue; // 跳过格式不正确的行
        }

        // 解析题目信息
        $type = sanitize_text_field($fields[0]);
        $name = sanitize_text_field($fields[1]);
        $all_answer = sanitize_text_field(implode('|', array_slice($fields, 2, 4))); // 选项
        $answer = sanitize_text_field($fields[6]);
        $tips = sanitize_textarea_field($fields[7]);
        $score = intval($fields[8]);

        // 将题目插入数据库
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
    }

    return true; // 导入成功
}