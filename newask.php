<?php
/*
Plugin Name: 子比考试系统 (Zibll Theme NewAsk Admin)
Plugin URI: https://github.com/levent233/zibll-newpostask
Description: 为子比主题添加考试功能(Add exam function to Zibll Theme)
Version: 1.0.1
Author: Le Vent
Author URI: https://acg.la
*/
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}
register_activation_hook(__FILE__, 'plugin_activate');
function plugin_activate() { 
	$sql1 = "
CREATE TABLE `fl_ask_tm` (
  `ID` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` longtext NOT NULL,
  `all_answer` longtext DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `tips` longtext DEFAULT NULL,
  `score` int(255) NOT NULL
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
		`ID` int(11) NOT NULL,
		`meta_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`meta_var` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
	";
	$sql3 = "ALTER TABLE `wp_fl_meta` CHANGE `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`ID`);";
	$sql4 = "ALTER TABLE `fl_ask_tm` CHANGE `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`ID`);";
	$sql5 = "ALTER TABLE `fl_ask_tm` auto_increment = 18;";
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	if($wpdb->get_var("SHOW TABLES LIKE 'wp_fl_meta'") != 'wp_fl_meta'){
		dbDelta($sql2);
		$wpdb->query($sql3);
	}
	if($wpdb->get_var("SHOW TABLES LIKE 'fl_ask_tm'") != 'fl_ask_tm'){
		dbDelta($sql1);
		$wpdb->query($sql4);
		$wpdb->query($sql5);

	}
}
//添加菜单挂钩
add_action('admin_menu', 'newask_menu');
function newask_page(){
	if (!is_super_admin()) {
		wp_die('您不能访问此页面', '权限不足');
		exit;
	}
	if($_GET['set'] == ''){
		$newask = get_fl_meta_key_all('newask');
		if($newask != ''){
			if ($_GET['pageed'] != '' && $_GET['pageed'] != '0') { //获取当前页码
				$page = $_GET['pageed'];
			   }elseif($_GET['pageed'] == '0'){
				$html = '
				<main role="main" class="container">
				<div class="zib-widget hot-posts">
				<p>暂无数据。
				';
				$html .= '</div></main>';
				echo $html;
				exit;
			   }elseif($_GET['pageed'] == ''){
				$page = '1';
			   }
				$syy = intval($page) - 1;
				$xyy = intval($page) + 1;
				$z = ceil(count($newask) / 20);
				if($page > $z){
					$html = '
					<main role="main" class="container">
					<div class="zib-widget">
					<p>暂无数据。
					';
					$html .= '</div></main>';
					echo $html;
					exit;
				}
				$html = '
			<link rel="stylesheet" id="_bootstrap-css" href="/wp-content/themes/zibll/css/bootstrap.min.css?ver=6.9.1" type="text/css" media="all">
			<link rel="stylesheet" id="_fontawesome-css" href="/wp-content/themes/zibll/css/font-awesome.min.css?ver=6.9.1" type="text/css" media="all">
			<link rel="stylesheet" id="_main-css" href="/wp-content/themes/zibll/css/main.min.css?ver=6.9.1" type="text/css" media="all">
			<link rel="stylesheet" id="_forums-css" href="/wp-content/themes/zibll/inc/functions/bbs/assets/css/main.min.css?ver=6.9.1" type="text/css" media="all">
			<h2>NEWASK ADMIN</h2>
			<main role="main" class="container">
			<div class="zib-widget hot-posts">
			<style>
			table tbody td {
				text-overflow: ellipsis;
				white-space: nowrap;
				overflow: hidden;		
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
			</tr>
			';
				$html .= showpage($page);
				$html .= '</table>
				</div>
				<div style="text-align:center;">
<div><b>功能由 © <a href="https://acg.la">ACG.LA</a> · <a href="https://github.com/levent233/zibll-newpostask">Github</a> 提供支持</b></div>
</div>
				</main>';
			if($page != '1' && $page != $z){
				$html .='
				<div class="tablenav-pages"><span class="displaying-num">' . count($newask) . '个项目</span>
				<span class="pagination-links"><a class="first-page button" href="/wp-admin/admin.php?page=newask"><span class="screen-reader-text">首页</span><span aria-hidden="true">«</span></a>
				<a class="prev-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $syy . '"><span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span></a>
				<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第' . $page . '页，共<span class="total-pages">' . $z . '</span>页</span></span>
				<a class="next-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $xyy . '"><span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span></a>
				<a class="last-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $z . '"><span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span></a></span></div>
				';
			   }elseif($page == $z && $page == '1'){
				$html .='
				<div class="tablenav-pages"><span class="displaying-num">' . count($newask) . '个项目</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
				<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第' . $page . '页，共<span class="total-pages">' . $z . '</span>页</span></span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
				';
			   }elseif($page == '1'){
				$html .='
				<div class="tablenav-pages"><span class="displaying-num">' . count($newask) . '个项目</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
				<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第' . $page . '页，共<span class="total-pages">' . $z . '</span>页</span></span>
				<a class="next-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $xyy . '"><span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span></a>
				<a class="last-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $z . '"><span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span></a></span></div>
				';
			   }elseif($page == $z){
				$html .='
				<div class="tablenav-pages"><span class="displaying-num">' . count($newask) . '个项目</span>
				<span class="pagination-links"><a class="first-page button" href="/wp-admin/admin.php?page=newask"><span class="screen-reader-text">首页</span><span aria-hidden="true">«</span></a>
				<a class="prev-page button" href="/wp-admin/admin.php?page=newask&pageed=' . $syy . '"><span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span></a>
				<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第' . $page . '页，共<span class="total-pages">' . $z . '</span>页</span></span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
				<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
				';
			   }
			echo $html;
		}else{
			$html = '
			<main role="main" class="container">
			<div class="zib-widget hot-posts">
			<p>暂无数据。
			';
			$html .= '</div></main>';
			echo $html;
		}
	}else{
		$uid = $_GET['set'];
		$html = '
		<main role="main" class="container">
<form action="/newask?action=ans_check" method="post">

</div>';
	}
}
// Add the page to the admin menu
function newask_menu(){
	add_menu_page('考试管理', '考试管理', 'administrator','newask', 'newask_page', 'dashicons-admin-tools', 90);
}

function get_fl_user_meta($meta_id, $meta_key){
	global $wpdb;
	$sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$meta_id' AND `meta_key` = '$meta_key'";
	$row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
	if($row_ck['meta_var'] != ''){
		return $row_ck['meta_var'];
	}else{
		return '无数据';
	}
}
function get_fl_meta_key_all($meta_key){
	global $wpdb;
	$sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_key` = '$meta_key'";
	$row_ck = $wpdb->get_results($sql_ck, ARRAY_A);
	return $row_ck;
}
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
			//var_dump(count($newask));
			$arr = $newask[$i];
			if($arr != ''){
				$user_info = get_userdata($arr['meta_id']);
				if($arr['meta_var'] == '1'){
					$zt = '已通过';
				}elseif($arr['meta_var'] == '-1'){
					$zt = '分数不达标';
				}elseif($arr['meta_var'] == '-2'){
					$zt = '封禁权限';
				}
				$uid = $arr['meta_id'];
				$sql_soc = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_score'";
				$row_soc = $wpdb->get_row($sql_soc, ARRAY_A);
				if($row_soc['meta_var'] != ''){
				$soc = $row_soc['meta_var'];
				}else{
					$soc = '无数据';
				}
			$html .= '
			<tr>
			<td width="10%">' . $arr['meta_id'] . '</td>
			<td width="20%">' . $user_info->user_nicename . ' · ' . $user_info->user_email . '</td>
			<td width="20%">' . $zt . ' (' . $soc . '分)</td>
			<td width="20%">' . get_fl_user_meta($arr['meta_id'], 'newask_in_time') . '</td>
			<td width="20%">' . get_fl_user_meta($arr['meta_id'], 'newask_up_time') . '</td>
			<td width="20%"><span class="but" data-clipboard-text="编辑"><a href="/wp-admin/admin.php?page=newask&set=' . $arr['meta_id'] . '">编辑</a></span> <span class="but" data-clipboard-text="查看试卷"><a href="/newask?action=ck&uid=' . $arr['meta_id'] . '">查看试卷</a></span></td>
			</tr>';
			}		
		}
	}else{
		for($i = $d;count($newask) >= $i;$i++){
			if($i >= $s){
				break;
			}
			//var_dump(count($newask));
			$arr = $newask[$i];
			if($arr != ''){
				$user_info = get_userdata($arr['meta_id']);
			if($arr['meta_var'] == '1'){
			$zt = '已通过';
			}elseif($arr['meta_var'] == '-1'){
			$zt = '分数不达标';
			}elseif($arr['meta_var'] == '-2'){
			$zt = '封禁权限';
			}
			$uid = $arr['meta_id'];
				$sql_soc = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_score'";
				$row_soc = $wpdb->get_row($sql_soc, ARRAY_A);
				if($row_soc['meta_var'] != ''){
				$soc = $row_soc['meta_var'];
				}else{
					$soc = '无数据';
				}
			$html .= '
			<tr>
			<td width="10%">' . $arr['meta_id'] . '</td>
			<td width="20%">' . $user_info->user_nicename . ' · ' . $user_info->user_email . '</td>
			<td width="20%">' . $zt . ' (' . $soc . '分)</td>
			<td width="20%">' . get_fl_user_meta($arr['meta_id'], 'newask_in_time') . '</td>
			<td width="20%">' . get_fl_user_meta($arr['meta_id'], 'newask_up_time') . '</td>
			<td width="20%"><span class="but" data-clipboard-text="编辑"><a href="/wp-admin/admin.php?page=newask&set=' . $arr['meta_id'] . '">编辑</a></span> <span class="but" data-clipboard-text="查看试卷"><a href="/newask?action=ck&uid=' . $arr['meta_id'] . '">查看试卷</a></span></td>
			</tr>';
			}
		}
	}
	//var_dump($newask['1']);
	return $html;
}
