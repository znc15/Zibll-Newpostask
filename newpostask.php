<?php
/**
 * Template name: 投稿考试
 * Description:   newask page
 */
error_reporting(E_ALL & ~E_NOTICE);
global $wpdb;
get_header();
if(!is_user_logged_in()){
    //未登录
    $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>未经授权的访问（未登录）！</b></div>';
    $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
    echo $html;
    //考试答题
$uid = get_current_user_id();
$sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask'";
$row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
if($row_ck['meta_var'] != '1'){
    header('Location:/newask');
    exit;
}
    get_footer();
    exit;
}
//管理员查询他人试卷
if($_GET['action'] == 'ck'){
    if (!is_super_admin()) {
        $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>未经授权的访问（权限不足）！</b></div>';
        $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
        echo $html;
        get_footer();
		exit;
	}
    if($_GET['uid'] != ''){
        $uid = $_GET['uid'];
        $sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask'";
        $row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
        if($row_ck['meta_var'] != ''){
            $sql_ht = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
            $row_ht = $wpdb->get_row($sql_ht, ARRAY_A);
            echo html_entity_decode($row_ht['meta_var']);
            get_footer();
            exit;
        }else{
            $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>未找到此用户对应试卷！</b></div>';
            $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
            echo $html;
            get_footer();
            exit;
        }   
    }else{
        $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>UID为空！</b></div>';
        $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
        echo $html;
        get_footer();
		exit;
    }
}
//查询是否已通过考试并输出成绩
$uid = get_current_user_id();
$sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask'";
$row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
if($row_ck['meta_var'] == '1'){
    $sql_ht = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
    $row_ht = $wpdb->get_row($sql_ht, ARRAY_A);
    echo html_entity_decode($row_ht['meta_var']);
    get_footer();
    exit;
}elseif($row_ck['meta_var'] == '-2'){
    $sql_ht = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
    $row_ht = $wpdb->get_row($sql_ht, ARRAY_A);
    $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>因未遵守发布规范，你的投稿发帖权限与考试权限已被永久取消！无法重考，若需申诉请联系i@acg.la</b></div>';
    $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
    echo $html;
    get_footer();
    exit;
}
//验证题目答案输出成绩
if($_GET['action'] == 'ans_check'){  
    // 扣除积分和余额
    $uid = get_current_user_id();
    $points_cost = get_option('points_balance_cost', 0); // 获取每次答题扣除的积分
    $balance_cost = get_option('balance_cost', 0); // 获取每次答题扣除的余额

    // 检查积分设置是否启用
    $is_points_enabled = get_option('require_points', '0'); // 获取积分设置是否启用

    // 检查余额设置是否启用
    $is_balance_enabled = get_option('require_balance', '0'); // 获取余额设置是否启用

    // 扣除积分
    $current_points = intval(get_user_meta($uid, 'points', true));
    if ($is_points_enabled && $current_points >= $points_cost && $points_cost > 0) { // 确保积分设置启用且积分足够
        update_user_meta($uid, 'points', $current_points - $points_cost);

        // 插入数据到 wp_zibpay_order
        global $wpdb;
        $order_data = array(
            'user_id' => $uid,
            'order_num' => '考试扣除',
            'order_type' => 1,
            'create_time' => current_time('mysql'),
            'pay_type' => 'points',
            'pay_detail' => serialize(array('points' => $points_cost)),
            'pay_time' => current_time('mysql'),
            'status' => 1
        );

        $wpdb->insert('wp_zibpay_order', $order_data);
    }

    // 扣除余额
    $current_balance = floatval(get_user_meta($uid, 'balance', true));
    if ($is_balance_enabled && $current_balance >= $balance_cost && $balance_cost > 0) { // 确保余额设置启用且余额足够
        update_user_meta($uid, 'balance', $current_balance - $balance_cost);

        // 插入数据到 wp_zibpay_order
        $order_data_balance = array(
            'user_id' => $uid,
            'order_num' => '考试扣除',
            'order_type' => 1,
            'create_time' => current_time('mysql'),
            'pay_type' => 'balance',
            'pay_detail' => serialize(array('balance' => $balance_cost)),
            'pay_time' => current_time('mysql'),
            'status' => 1
        );

        $wpdb->insert('wp_zibpay_order', $order_data_balance);
    }

    $html = '<main role="main" class="container">';
    $arr = $_POST;
    $keys = array_keys($arr);
    $values = array_values($arr);
    for($i=0;$i<count($arr);$i++){
        if($keys[$i] != ''){
            if(strlen($values[$i]) == 1){
                $sql = "SELECT * FROM `fl_ask_tm` WHERE `ID` = " . $keys[$i];
                $row = $wpdb->get_row($sql, ARRAY_A);
                if($row['ID'] != ''){
                    if($values[$i] == $row['answer']){
                        $alls = $alls + intval($row['score']);
                        $html .= '
                    <div class="zib-widget ajax-item mb10 order-type-1">
<div class="pay-tag badg badg-sm mr6">' . $row['type'] . '</div>'. $row['name'] .'
<div class="pull-right">
<span class="focus-color em14 shrink0"> + '. $row['score'] .'</span>
</div></div></div>';
                    }else{
                        $alls = $alls - intval($row['score']);
                        $html .= '
                        <div class="zib-widget ajax-item mb10 order-type-1">
<div class="pay-tag badg badg-sm mr6">' . $row['type'] . '</div>'. $row['name'] .'
<div class="pull-right">
<span class="focus-color em14 shrink0"> - '. $row['score'] .'</span>
</div></div></div>';
                    }
                }
            }else{
                $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>数据不合规，已记录进系统，再次触发将会永久封号。</b></div>';
                $html .= '<a style="margin-bottom: 2em;" href="/newask" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
                echo $html;
                exit;
            }
        }else{
            $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>请您填写每一道题目后在提交！</b></div>';
            $html .= '<a style="margin-bottom: 2em;" href="/newask" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
            echo $html;
            exit;
        }
    }
    //判断分数是否合格
    if($alls >= 90){
        if($row_ck['meta_var'] != ''){
            $up_sql = "UPDATE `wp_fl_meta` SET `meta_var` = '1' WHERE `meta_id` = '$uid' AND `meta_key` = 'newask';";
            $wpdb->query($up_sql);
        }else{
            $in_sql = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, $uid, 'newask', '1');";
            $wpdb->query($in_sql);
        }  

        // 添加会员升级逻辑
        if(get_option('enable_vip_upgrade', '0') == '1') {
            // 获取设置的会员级别和有效期
            $vip_level = get_option('vip_level', 1); 
            $vip_exp_date = get_option('vip_exp_date', 'Permanent');
            
            // 更新用户的会员信息
            update_user_meta($uid, 'vip_level', $vip_level);
            update_user_meta($uid, 'vip_exp_date', $vip_exp_date);
            
            // 记录会员升级日志
            $log_data = array(
                'user_id' => $uid,
                'action' => 'exam_vip_upgrade',
                'time' => current_time('mysql'),
                'value' => array(
                    'level' => $vip_level,
                    'exp_date' => $vip_exp_date,
                    'exam_score' => $alls
                )
            );
            
            // 可以添加日志记录到数据库
            do_action('zib_user_upgrade_log', $log_data);
        }

        $user_info = get_userdata($uid);
        $html .= '
    <div class="zib-widget hot-posts">
    <div class="title-h-left"><b><h2>最终得分：' . $alls . '分【2023-02-09 第一代卷】</h2></b></div>
    <b><p>考生：【' . $user_info->user_nicename . '】，恭喜合格，您已获得投稿免审权限';
    
    // 如果启用了会员升级，显示会员升级信息
    if(get_option('enable_vip_upgrade', '0') == '1') {
        $html .= '并已自动升级为' . ($vip_level == 1 ? '普通会员' : '高级会员');
        if($vip_exp_date != 'Permanent') {
            $html .= '(有效期至' . $vip_exp_date . ')';
        } else {
            $html .= '(永久有效)';
        }
    }
    
    $html .= '，请您严格遵守投稿规范发布内容，否则若是后续发布违规，不标准投稿将会永久取消发布投稿权限。</p></b>
    <a style="margin-bottom: 2em;" href="/newposts" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">发布投稿</span></a>
    </div>';
    }else{
        if($row_ck['meta_var'] != ''){
            $up_sql = "UPDATE `wp_fl_meta` SET `meta_var` = '-1' WHERE `meta_id` = '$uid' AND `meta_key` = 'newask';";
            $wpdb->query($up_sql);
        }else{
            $in_sql = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, $uid, 'newask', '-1');";
            $wpdb->query($in_sql);
        } 
        $user_info = get_userdata($uid);
        $html .= '
    <div class="zib-widget hot-posts">
<div class="title-h-left"><b><h2>最终得分：' . $alls . '分</h2></b></div>
<b><p>考生：【' . $user_info->user_nicename . '】，不合格，请您认真查看每一道的答案并牢记于心！</p></b>
<a style="margin-bottom: 2em;" href="/newask" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回重考</span></a>
</div>';
    }
    $html .= '</main>';
    //试卷html更新
    $sql_q_h = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
        $row_q_h = $wpdb->get_row($sql_q_h, ARRAY_A);
        if($row_q_h['meta_var'] == ''){
            $ht_data = htmlentities($html);
            $html_sql = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, '$uid', 'newask_html', '$ht_data');";         
            $wpdb->query($html_sql);    
        }else{
            $ht_data = htmlentities($html);
            $html_sql = "UPDATE `wp_fl_meta` SET `meta_var` = '$ht_data' WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
            $wpdb->query($html_sql);        
        }
        //试卷得分更新
        $newask_score_h = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_score'";
        $newask_score_row = $wpdb->get_row($newask_score_h, ARRAY_A);
        if($newask_score_row['meta_var'] == ''){
            $newask_score_sql = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, '$uid', 'newask_score', '$alls');";
            $wpdb->query($newask_score_sql);
        }else{
            $newask_score_sql = "UPDATE `wp_fl_meta` SET `meta_var` = '$alls' WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_score'";
            $wpdb->query($newask_score_sql);
        }
        //初考时间更新
        $newask_in_h = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_in_time'";
        $newask_in_row = $wpdb->get_row($newask_in_h, ARRAY_A);
        if($newask_in_row['meta_var'] == ''){
            $time = date("Y-m-d H:i:s", time()+8*60*60);
            $newask_in_time = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, '$uid', 'newask_in_time', '$time');";
            $wpdb->query($newask_in_time);
        }
        //初考更新时间
        $time_up_h = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_up_time'";
        $time_up_row = $wpdb->get_row($time_up_h, ARRAY_A);
        if($time_up_row['meta_var'] == ''){
            $time = date("Y-m-d H:i:s", time()+8*60*60);
            $time_up_sql = "INSERT INTO `wp_fl_meta` (`ID`, `meta_id`, `meta_key`, `meta_var`) VALUES (NULL, '$uid', 'newask_up_time', '$time');";
            $wpdb->query($time_up_sql);
        }else{
            $time = date("Y-m-d H:i:s", time()+8*60*60);
            $time_up_sql = "UPDATE `wp_fl_meta` SET `meta_var` = '$time' WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_up_time'";
            $wpdb->query($time_up_sql);
        }
        echo $html;
    get_footer();
    exit;
}
?>
<!-- 处理按钮更新答案框 -->
<script>
    function set_in_var(id,ans){
        document.getElementById(id).focus();
        document.getElementById(id).value = ans; 
        document.getElementById(id).blur();
    }
</script>

<?php
//输出试卷
$ask_q = "SELECT * FROM fl_ask_tm ORDER BY RAND() LIMIT 20;";
$ask_res = $wpdb->get_results($ask_q, ARRAY_A);
$x=0;  
//var_dump($ask_res);
//style="width: 95%;margin: auto;"
$tem = '
<main role="main" class="container">
<form action="/newask?action=ans_check" method="post">
<div class="zib-widget hot-posts">
<div class="title-h-left"><b><h2>投稿考试</h2></b></div>
<p>' . wp_kses_post(get_option('exam_intro', '')) . '</p>
</div>
';
//循环取出所有题目数据
foreach ($ask_res as $ask_row){  
    $all_answer_arr = explode('|', $ask_row['all_answer']);
    $ii = count($all_answer_arr);
    //分割答案并输出题目选项
    if ($ii <= 4) {
        $askid = $ask_row['ID'];
        $asktype = $ask_row['type'];
        $asktitle = $ask_row['name'];
        $score = $ask_row['score'];
        $tips = $ask_row['tips'];
        $all_answer_html = '';
        for ($i = 0;$i <= $ii;$i++) {
            if($i == 0){
                $ajax_j = "set_in_var('".$askid."','A');";
                $answer_h = 'A.' . $all_answer_arr[$i];
                $all_answer_html .= '<p><a class="but mr6" onclick="'.$ajax_j.'" data-toggle="tab">' . $answer_h . '</a>';
            }elseif($i == 1){
                $ajax_j = "set_in_var('".$askid."','B');";
                $answer_h = 'B.' . $all_answer_arr[$i];
                $all_answer_html .= '<p><a class="but mr6" onclick="'.$ajax_j.'" data-toggle="tab">' . $answer_h . '</a>';
            }elseif($i == 2){
                $ajax_j = "set_in_var('".$askid."','C');";
                $answer_h = 'C.' . $all_answer_arr[$i];
                $all_answer_html .= '<p><a class="but mr6" onclick="'.$ajax_j.'" data-toggle="tab">' . $answer_h . '</a>';
            }elseif($i == 3){
                $ajax_j = "set_in_var('".$askid."','D');";
                $answer_h = 'D.' . $all_answer_arr[$i];
                $all_answer_html .= '<p><a class="but mr6" onclick="'.$ajax_j.'" data-toggle="tab">' . $answer_h . '</a>';
            }
        }
        $tem2 = '   
<div class="zib-widget ajax-item mb10 order-type-1">
<div class="pay-tag badg badg-sm mr6">' . $asktype . '</div>' . $asktitle . '</a>
<div class="pull-right">
<span class="focus-color em14 shrink0"> <svg class="icon mr6 em09" aria-hidden="true"><use xlink:href="#icon-trend-color"></use></svg> + ' . $score . '</span>
</div>
<br><br>' . $all_answer_html . '
<p><div class="meta-time em09 muted-2-color flex ac jsb hh">Tips：' . $tips . '
</div>
<p><div class="relative line-form mb10">
<input type="text" name="' . $askid . '" class="line-form-input" id="' . $askid . '" placeholder="" maxlength="1" required="required">
<div class="scale-placeholder">请输入你的答案</div>
<div class="abs-right muted-2-color"></div>
<i class="line-form-line"></i></div>
</div></div>
';
    }
    //二次打乱题目
    $rand = rand();
    $rand2 = rand();
    if($rand >= $rand2){
        $temx = $tem2 . $temx;
    }else{
        $temx = $temx . $tem2;
    }
    $tem2 = '';
}
echo $tem . $temx .'
<button type="submit" class="but jb-blue padding-lg btn-block"><i class="fa fa-check"></i> 确认提交</button><br><br>
<div style="text-align:center;">
<div><b>Design By <a href="https://acg.la">ACG.LA</a> | Power By <a href="https://www.littlesheep.cc">LittleSheep</a></b></div>
</div>
</form>
</main>';
?>
			<?php comments_template('/template/comments.php', true); ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
