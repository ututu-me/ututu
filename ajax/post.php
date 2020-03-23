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
forward_login();

$flag=0;
$title = filter_input(INPUT_POST, 'title');
$body = filter_input(INPUT_POST, 'body');

if ( !preg_match("/^.{1,25}$/u", $title) ) {
    $errorMessage[] = 'タイトルは25文字以内で入力してください｡';
}
if ( !preg_match("/^[\s\S]{1,400}$/u", $body) ) {
    $errorMessage[] = '本文は400文字以内で入力してください｡';
}
if ( empty($errorMessage) ) {
    $created = date('Y-m-d H:i:s');
    try {
        $stmt = $pdo->prepare("select * from dream");
        $stmt->execute();
        $stmt = $pdo->prepare("insert into dream(created_at, title, body, user_id) values(?, ?, ?, ?)");
        $stmt->execute( array($created, $title, $body, $_SESSION['userdata']['id']) );
        $flag = 0;
    } catch (PDOException $e) {
        $flag = $errorMessage;
        exit('データベース接続失敗 '.$e->getMessage());
    }
} else {
    $flag = $errorMessage;
}

$list = array("flag" => $flag );
header("Content-type: application/json; charset=UTF-8");
echo json_encode($list);
?>