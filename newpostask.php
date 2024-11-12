<?php
/**
 * Template name: 投稿考试
 * Description:   newask page
 */
global $wpdb;
get_header();
if(!is_user_logged_in()){
    //未登录
    $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>未经授权的访问（未登录）！</b></div>';
    $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
    echo $html;
    get_footer();
    exit;
}

// 开始输出缓冲
ob_start();

//考试答题
$uid = get_current_user_id();
$sql_ck = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask'";
$row_ck = $wpdb->get_row($sql_ck, ARRAY_A);
if ($row_ck && isset($row_ck['meta_var'])) {
    if ($row_ck['meta_var'] != '1') {
        header('Location:/newask');
        exit;
    }
}

//管理员查询他人试卷
if(isset($_GET['action']) && $_GET['action'] == 'ck'){
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
if ($row_ck && isset($row_ck['meta_var'])) {
    if ($row_ck['meta_var'] == '1') {
        $sql_ht = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
        $row_ht = $wpdb->get_row($sql_ht, ARRAY_A);
        echo html_entity_decode($row_ht['meta_var']);
        get_footer();
        exit;
    } elseif ($row_ck['meta_var'] == '-2') {
        $sql_ht = "SELECT * FROM `wp_fl_meta` WHERE `meta_id` = '$uid' AND `meta_key` = 'newask_html'";
        $row_ht = $wpdb->get_row($sql_ht, ARRAY_A);
        $html .= '<main role="main" class="container"><div class="alert jb-red em12" style="margin: 2em 0;"><b>因未遵守发布规范，你的投稿发帖权限与考试权限已被永久取消！无法重考，若需申诉请联系i@acg.la</b></div>';
        $html .= '<a style="margin-bottom: 2em;" href="/" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回</span></a></main>';
        echo $html;
        get_footer();
        exit;
    }
}
//验证题目答案输出成绩
if(isset($_GET['action']) && $_GET['action'] == 'ans_check'){  
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
        $user_info = get_userdata($uid);
        $html .= '
    <div class="zib-widget hot-posts">
<div class="title-h-left"><b><h2>最终得分：' . $alls . '分【2023-02-09 第一代卷】</h2></b></div>
<b><p>考生：【' . $user_info->user_nicename . '】，恭喜合格，您已获得投稿免审权限，请您严格遵守投稿规范发布内容，否则若是后续发布违规，不标准投稿将会永久取消发布投稿权限。</p></b>
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
$temx = ''; // 初始化 $temx 变量
$tem = '
<main role="main" class="container">
<form action="/newask?action=ans_check" method="post">
<div class="zib-widget hot-posts">
<div class="title-h-left"><b><h2>投稿考试</h2></b></div>
<p>注意：在下面两个链接内找到所有题目正确答案。
</p>
<p><b>考试满分为：110+分，获得投稿发帖权限需要总分达到90分以上。</b></p>
<p><b>请您认真查看每一道的答案并牢记于心，若是后续发布违规，不标准投稿将会永久取消发布投稿权限。</b></p>
<p><b>您通过考试后发布投稿，帖子将不在需要审核。</b></p>
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

// 在输出之前结束缓冲并清除输出
ob_end_flush();
