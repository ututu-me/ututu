<?php
require "class.php";
require "authdb.php";
session_name('ututu_dream');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);
ini_set('session.gc_maxlifetime', 60*60*12);
session_save_path('/home/ututu/sessions');
session_start([
	'cookie_lifetime' => 60*60*24*365,
]);

session_update($pdo);

$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
unset($errorMessage);
$errorMessage=0;
forward_login();

//通知の取得
$sql = "select likes.id, likes.created_at, likes.like_user_id, likes.liked_post_id, likes.notif, account.name as liked_user_name, account.screen_name as liked_user_scname, dream.body, dream.user_id, 'like' as type from likes inner join account on likes.like_user_id = account.id inner join dream on likes.liked_post_id = dream.id where dream.user_id = ? union select follow.id, follow.created_at, follow.follow_user_id, follow. followed_user_id, followed_user_id as notif,  account.name, account.screen_name, NULL as body, NULL as user_id, 'follow' as type from follow inner join account on follow.follow_user_id = account.id where followed_user_id = ? order by created_at desc";
$stmt = $pdo->prepare($sql);
$stmt->execute( array($_SESSION['userdata']['id'], $_SESSION['userdata']['id']) );
$notificatons = $stmt->fetchAll();
?>
<html>
<head>
    <?php include("include/analytics.php"); ?>
    <?php include("include/version.php") ?>
    <meta http-equiv="content-type" charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/viewport-extra@1.0.3/dist/viewport-extra.min.js"></script>
    <script>
        new ViewportExtra(375)
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="./include/css/unity.css" rel="stylesheet">
    <link href="./include/css/header.css" rel="stylesheet">
    <link href="./include/css/article.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="./include/js/jquery.inview.min.js"></script>
    <script src="./include/js/unity.js"></script>
    <script src="./include/js/header.js"></script>
    <script src="./include/js/article.js"></script>
    <title>通知 | UTUTU</title>
</head>
<body>
    <div class="wrap">
        <?php include('./include/header.php'); ?>
        <div class="container">
            <main class="columns">
                <div class="main-column column is-9">
                    <section class="section">
                        <div class="container">
                            <h3 class="subtitle">通知</h3>
                            <?php foreach ( $notificatons as $loop ) : ?>
                                <div class="media" data-date="<?php echo $loop['created_at'] ?>">
                                    <?php if ( $loop['type'] == 'like' ) : ?>
                                        <div class="media-left">
                                            <span class="icon is-size-4" style="color: lightcoral;"><i class="fas fa-heart"></i></span>
                                        </div>
                                        <div class="media-content">
                                            <div class="content">
                                                <span class="is-size-6"><a href="userpage.php?name=<?php echo showText($loop['liked_user_name']) ?>"><?php echo showText($loop['liked_user_scname']) ?></a>さんが<a href="dream.php?id=<?php echo $loop['liked_post_id'] ?>">あなたの投稿</a>にいいねしました｡</span>
                                                <p class="has-text-grey-light"><?php echo showText($loop['body']) ?></p>
                                            </div>
                                        </div>
                                    <?php elseif ( $loop['type'] == 'follow' ) : ?>
                                        <div class="media-left">
                                            <span class="icon"><i class="fas fa-user-plus"></i></span>
                                        </div>
                                        <div class="media-content">
                                            <div class="content">
                                                <span><a href="userpage.php?name=<?php echo showText($loop['liked_user_name']) ?>"><?php echo showText($loop['liked_user_scname']) ?></a>さんがあなたをフォローしました｡</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($notificatons)): ?>
                                <div class="message">
                                    <div class="message-header">
                                        <p>通知はありません</p>
                                    </div>
                                    <div class="message-body">
                                        <p>いいねやフォローされるとここに通知として表示されます｡</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
                <?php include("./include/popular_new.php"); ?>
            </main>
        </div>
        <?php include("./include/footer.php") ?>
    </div>
</body>
</html>