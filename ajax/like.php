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
forward_login();

$flag=0;
if ( $postnum = filter_input(INPUT_POST, 'postnum') ) {
    if ( /*既にいいね済みなら*/get_isliked_thepost($pdo, $_SESSION['userdata']['id'], $postnum) == 1 ) {
        try {
            $stmt = $pdo->prepare("delete from likes WHERE like_user_id = ? and liked_post_id = ?");
            $stmt->execute( array($_SESSION['userdata']['id'], $postnum) );
            $flag = 1;
        } catch (PDOException $e) {
            $flag = 0;
            exit('データベース接続失敗 '.$e->getMessage());
        }
    } /*まだいいねしていないなら*/else {
        $created = date('Y-m-d H:i:s');
        try {
            $stmt = $pdo->prepare("insert into likes(created_at, like_user_id, liked_post_id, notif) values(?, ?, ?, ?)");
            $stmt->execute( array($created, $_SESSION['userdata']['id'], $postnum, 0) );
            $flag = 1;
        } catch (PDOException $e) {
            $flag = 0;
            exit('データベース接続失敗 '.$e->getMessage());
        }
    }
    $likecount = get_like_count_of_the_post($pdo, $postnum);
    $list = array("flag" => $flag, "likecount" => $likecount );
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>