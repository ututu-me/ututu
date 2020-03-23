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
    if ( get_postid_to_name($pdo, $postnum) == $_SESSION['userdata']['id'] ) {
        $date = date('Y-m-d H:i:s');
        try {
            $stmt = $pdo->prepare("update dream set delete_flag = 1, delete_date = ? where id = ?");
            $stmt->execute( array($date, $postnum) );
            $flag = 1;
            $delmes = "削除しました｡";
        } catch (PDOException $e) {
            $flag = 0;
            exit('データベース接続失敗 '.$e->getMessage());
        }
    } else {
        $flag=0;
    }
    $list = array("flag" => $flag, "delmes" => $delmes );
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>