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
if ( $name = filter_input(INPUT_POST, 'name') ) {
    $id = get_name_to_id($pdo, $name);
    if ( /*既にフォロー済みなら*/get_isfollow($pdo, $_SESSION['userdata']['id'], $id) == 1 ) {
        try {
            $stmt = $pdo->prepare("DELETE FROM follow WHERE follow_user_id = ? and followed_user_id = ?");
            $stmt->execute( array($_SESSION['userdata']['id'], $id) );
            $flag = 'del';
        } catch (PDOException $e) {
            $flag = 0;
            exit('データベース接続失敗 '.$e->getMessage());
        }
    } /*まだフォローしていないなら*/else {
        $created = date('Y-m-d H:i:s');
        try {
            $stmt = $pdo->prepare("insert into follow(created_at, follow_user_id, followed_user_id) values(?, ?, ?)");
            $stmt->execute( array($created, $_SESSION['userdata']['id'], $id) );
            $flag = 'followed';
        } catch (PDOException $e) {
            $flag = 0;
            exit('データベース接続失敗 '.$e->getMessage());
        }
    }
    $list = array("flag" => $flag);
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>