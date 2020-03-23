<?php
require "../class.php";
require "../authdb.php";
session_name('ututu_dream');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);
ini_set('session.gc_maxlifetime', 60*60*12);
session_save_path('/home/ututu/sessions');
session_start([
	'cookie_lifetime' => 60*60*24*365,
]);
$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
unset($errorMessage);
$errorMessage=0;
$newcount = 0;
forward_login();

if ( $top = filter_input(INPUT_POST, 'postnum') ) {
    $page = filter_input(INPUT_POST, 'page');
    if ( /* ホームTLからのアクセス時 */$page == '/ututu/index.php' ) {
        //新着投稿の件数を取得
        $sql = "select count(*) from dream inner join account on dream.user_id = account.id where dream.id > ? and delete_flag is null order by dream.id desc";
        $stmt = $pdo->prepare($sql);
        $stmt->execute( array($top) );
        $newcount = $stmt->fetchColumn();

        if ( $newcount != 0 ) {
            $sql = "select NULL as likecount, NULL as islike, NULL as isme, dream.user_id, dream.id as dreamid, account.name as name, account.screen_name as screen_name, dream.title as title, dream.body as body, dream.created_at as created_at from dream inner join account on dream.user_id = account.id where dream.id > ? and delete_flag is null order by dream.id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute( array($top) );
            $notif = $stmt->fetchAll();

            $i=0;
            foreach ( $notif as $loop ) {
                if ( /* 自分またはフォローしたユーザによる投稿ならば */$_SESSION['userdata']['id'] == $loop['user_id'] || get_isfollow($pdo, $_SESSION['userdata']['id'], $loop['user_id']) == 1 ) {
                    $loop = array_map('showText', $loop);

                    $loop['isme'] = 0;
                    $loop['likecount'] = (int)get_like_count_of_the_post($pdo, $loop['dreamid']);
                    $loop['islike'] = (int)get_isliked_thepost($pdo, $_SESSION['userdata']['id'], $loop['dreamid']); 
                    if ( $loop['user_id'] == $_SESSION['userdata']['id'] ) {
                        $loop['isme'] = 1;
                    }

                    unset($loop['user_id']);
                    unset($loop[0]);
                    $notif[$i] = $loop;
                } else {
                    unset($notif[$i]);
                }
                $i=$i+1;
            }
        }
    }

    $list = array("flag" => $flag, "new" => count($notif), "newpost" => $notif );
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>