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

if ( $top = filter_input(INPUT_POST, 'top') ) {
    $sql = "select count(*) from likes inner join account on likes.like_user_id = account.id inner join dream on likes.liked_post_id = dream.id where likes.created_at > ? and dream.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute( array($top, $_SESSION['userdata']['id']) );
    $newcount = $stmt->fetchColumn();

    $sql = "select count(*) from follow inner join account on follow.follow_user_id = account.id where follow.created_at > ? and followed_user_id = ? order by created_at desc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute( array($top, $_SESSION['userdata']['id']) );
    $newcount = $newcount + $stmt->fetchColumn();

    if ( $newcount != 0 ) {
        $sql = "select likes.created_at, likes.liked_post_id, account.name as liked_user_name, account.screen_name as liked_user_scname, dream.body, 'like' as type from likes inner join account on likes.like_user_id = account.id inner join dream on likes.liked_post_id = dream.id where likes.created_at > ? and dream.user_id = ? union select follow.created_at, NULL as liked_post_id,  account.name, account.screen_name, NULL as body, 'follow' as type from follow inner join account on follow.follow_user_id = account.id where follow.created_at > ? and followed_user_id = ? limit 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute( array($top, $_SESSION['userdata']['id'], $top, $_SESSION['userdata']['id']) );
        $notif = $stmt->fetchAll();

        $i=0;
        foreach ( $notif as $loop ) {
            $loop = array_map('showText', $loop);
            $notif[$i] = $loop;
            $i=$i+1;
        }
    }

    $list = array("flag" => $flag, "new" => $newcount, "notif" => $notif );
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>