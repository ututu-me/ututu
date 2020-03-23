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

if ( $bottom = filter_input(INPUT_POST, 'postnum') ) {
    $page = filter_input(INPUT_POST, 'page');
    $tab = filter_input(INPUT_POST, 'tab');
    $name = filter_input(INPUT_POST, 'user');
    if ( /* ユーザページからのアクセス時 */$page == '/ututu/userpage.php' ) {
        if ( $tab == "likes" ) {
            $sql = "select account.id as user_id, NULL as likecount, NULL as islike, NULL as isme, dream.user_id, dream.id as dreamid, account.name as name, account.screen_name as screen_name, dream.title as title, dream.body as body, dream.created_at as created_at, likes.id as likeid
             from likes inner join dream on dream.id = liked_post_id inner join account on dream.user_id = account.id
              where likes.id < ? and like_user_id = ? and delete_flag is null order by likeid desc limit 50";
            $stmt = $pdo->prepare($sql);
            $stmt->execute( array( $bottom, get_name_to_id($pdo, $name) ) );
            $notif = $stmt->fetchAll();
            
            $notif = array_reverse($notif); 

            $i=0;
            foreach ( $notif as $loop ) {
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
                
                $i=$i+1;
            }
        } else {
            $sql = "select account.id as user_id, NULL as likecount, NULL as islike, NULL as isme, dream.user_id, dream.id as dreamid, account.name as name, account.screen_name as screen_name, dream.title as title, dream.body as body, dream.created_at as created_at from dream inner join account on dream.user_id = account.id where dream.id < ? and name = ? and delete_flag is null order by dream.id desc limit 50";
            $stmt = $pdo->prepare($sql);
            $stmt->execute( array( $bottom, $name ) );
            $notif = $stmt->fetchAll();
            
            $notif = array_reverse($notif); 

            $i=0;
            foreach ( $notif as $loop ) {
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
                
                $i=$i+1;
            }
        }
    } else if ( /* ホームTLからのアクセス時 */$page == '/ututu/index.php' ) {
        
        $sql = "select account.id as user_id, NULL as likecount, NULL as islike, NULL as isme, dream.id as dreamid, account.name as name, account.screen_name as screen_name, dream.title as title, dream.body as body, dream.created_at as created_at from dream inner join account on dream.user_id = account.id where dream.id < ? and delete_flag is null order by dream.id desc limit 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute( array( $bottom ) );
        $notif = $stmt->fetchAll();
        
        $notif = array_reverse($notif); 

        $i=0;
        foreach ( $notif as $loop ) {
            if ( $_SESSION['userdata']['id'] == $loop['user_id'] || get_isfollow($pdo, $_SESSION['userdata']['id'], $loop['user_id']) == 1 ) {
                $loop = array_map('showText', $loop);

                $loop['isme'] = 0;
                $loop['likecount'] = (int)get_like_count_of_the_post($pdo, $loop['dreamid']);
                $loop['islike'] = (int)get_isliked_thepost($pdo, $_SESSION['userdata']['id'], $loop['dreamid']); 
                if ( $loop['user_id'] == $_SESSION['userdata']['id'] ) {
                    $loop['isme'] = 1;
                }
    
                unset($loop['user_id']);
                unset($loop[0]);
            } else {
                unset($notif[$i]);
            }
            $notif[$i] = $loop;
            
            $i=$i+1;
        }
        
    }

    $list = array("flag" => $flag, "new" => count($notif), "newpost" => $notif );
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($list);
}
?>