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
forward_login();

$name = $_GET['name'];
$name_to_id = get_name_to_id($pdo, $name);
$userdata = new UserStatus($pdo, $name);

//フォローチェック
if ( get_isfollow($pdo, $_SESSION['userdata']['id'], $name_to_id) == 1 ) {
    $button_text = 'フォロー中';
    $isfollow = 1;
} else {
    $button_text = 'フォロー';
    $isfollow = 0;
}

//IDから名前とbioを抽出
$sql = 'select screen_name, bio from account where name = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute( array( $name ) );
$paging_user = $stmt->fetch();

//いいねの総数を取得
$sql = 'select count(*) from likes inner join dream on likes.liked_post_id = dream.id where like_user_id = ? and delete_flag is null';
$stmt = $pdo->prepare($sql);
$stmt->execute( array( $name_to_id ) );
$likecount = $stmt->fetchColumn();

//ユーザによる夢を取得
if ( !empty($name) ) {
    if ( empty($paging_user) ) {
        $errorMessage = "お探しのユーザは見つかりませんでした｡";
    } else {
        if ( /* ユーザトップページ */ empty($_GET['page']) ) {
            $stmt = $pdo->prepare("select *, dream.id as dreamid from dream inner join account on dream.user_id = account.id where name = ? and delete_flag is null order by dream.id desc limit 50");
            $stmt->execute( array($name) );
            $title = $paging_user['screen_name']."さんの夢日記 | UTUTU";
        }
        if ( /* いいね */ $_GET['page'] == 'likes' ) {
            $stmt = $pdo->prepare("select dream.id, dream.id as dreamid, name, screen_name, body, dream.created_at, likes.id as likeid from likes inner join dream on dream.id = liked_post_id inner join account on dream.user_id = account.id where like_user_id = ? and delete_flag is null order by likeid desc limit 50");
            $stmt->execute( array( get_name_to_id($pdo, $name) ) );
            $title = $paging_user['screen_name']."さんのいいね | UTUTU";
        }
        if ( /* フォロー一覧 */ $_GET['page'] == 'following' ) {
            $stmt = $pdo->prepare("select * from follow inner join account on follow.followed_user_id = account.id where follow_user_id = ? order by follow.id desc");
            $stmt->execute( array(get_name_to_id($pdo, $name)) );
            $title = $paging_user['screen_name']."さんのフォロー | UTUTU";
        }
        if ( /* フォロワー一覧 */ $_GET['page'] == 'followers' ) {
            $stmt = $pdo->prepare("select * from follow inner join account on follow.follow_user_id = account.id where followed_user_id = ? order by follow.id desc");
            $stmt->execute( array(get_name_to_id($pdo, $name)) );
            $title = $paging_user['screen_name']."さんのフォロワー | UTUTU";
        }
    }
} else {
    $errorMessage = "お探しのページは見つかりませんでした｡";
}

$data = $stmt->fetchAll();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="./include/css/unity.css" rel="stylesheet">
    <link href="./include/css/header.css" rel="stylesheet">
    <link href="./include/css/article.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="./include/js/unity.js"></script>
    <script src="./include/js/header.js"></script>
    <script src="./include/js/article.js"></script>
    <script src="./include/js/follow.js"></script>
    <?php if ( !empty($errorMessage) ) : ?>
        <title><?php echo $errorMessage ?> | UTUTU</title>
    <?php else : ?>
        <title><?php echo $title ?></title>
    <?php endif; ?>
</head>
<body>
    <div class="wrap">
        <?php include('./include/header.php'); ?>
        <div class="container">
            <main class="columns">
                <div class="main-column column is-9">
                    <section class="section">
                        <div class="container">
                            <?php if ( !empty($errorMessage) ) : ?>
                            <?php echo $errorMessage ?>
                            <?php else : ?>
                            <div class="hero is-medium is-bold is-primary" style="background-image: url('https://ututu.me/image/header/<?php echo $_GET['name'] ?>.png?<?php echo date('dHis') ?>'); background-size: cover;">
                                <div class="hero-body">
                                    <div class="container">
                                        <div class="media article">
                                            <div class="media-left">
                                                <div class="image is-128x128 is-hidden-touch">
                                                    <img src="https://ututu.me/image/icon/<?php echo $_GET['name'] ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%">
                                                </div>
                                                <div class="image is-64x64 is-hidden-desktop">
                                                    <img src="https://ututu.me/image/icon/<?php echo $_GET['name'] ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%">
                                                </div>
                                            </div>
                                            <div class="media-content">
                                                    <ul>
                                                        <a href="userpage.php?name=<?php echo $_GET['name'] ?>"><li style="margin: 3px 0;"><h2 class="title" style="background-color: black; display: inline-block;"><?php echo showText( $userdata->getSCName() ) ?></h2></li></a>
                                                        <a href="userpage.php?name=<?php echo $_GET['name'] ?>"><li><h2 class="subtitle" style="background-color: black; display: inline-block;" >@<?php echo $userdata->getName() ?></h2></li></a>
                                                        <li><span><?php echo showText($paging_user['bio']) ?></span></li>
                                                    </ul>
                                                <?php if ( $_SESSION['userdata']['name'] != $name ) : ?>
                                                    <div class="follow-button button <?php if ( $isfollow == 1 ) echo 'is-primary' ?>" data-name="<?php echo $name ?>">
                                                        <span class="icon" style="margin-right: 0.75rem;"><i class="fas fa-user-plus"></i></span>
                                                        <span class="is-text-4"><?php echo $button_text ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ( $_SESSION['userdata']['name'] == $name ) : ?>
                                                    <div class="follow-button" data-name="<?php echo $name ?>">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-bottom">
                                    <nav class="level is-mobile" style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.39));">
                                        <div class="level-left"></div>
                                        <div class="level-right">
                                            <span class="level-item has-text-centered">
                                                <a href="userpage.php?name=<?php echo $_GET['name'] ?>&page=following">
                                                    <p class="heading has-text-white">Following</p>
                                                    <p class="title is-size-5"><?php echo $userdata->getFollowNum($pdo, $name) ?></p>
                                                </a>
                                            </span>
                                            <span class="level-item has-text-centered">
                                                <a href="userpage.php?name=<?php echo $_GET['name'] ?>&page=followers">
                                                    <p class="heading has-text-white">Followers</p>
                                                    <p class="title is-size-5"><?php echo $userdata->getFollowerNum($pdo, $name) ?></p>
                                                </a>
                                            </span>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                            <div class="tabs">
                                <ul>
                                    <?php if ( empty($_GET['page']) ) : ?>
                                    <li class="is-active">
                                    <?php else : ?>
                                    <li>
                                    <?php endif; ?>
                                        <span class="has-text-centered">
                                            <a href="userpage.php?name=<?php echo $_GET['name'] ?>">
                                                <p class="heading">Dreams</p>
                                                <p class="title is-size-5"><?php echo $userdata->getPostNum($pdo, $name) ?></p>
                                            </a>
                                        </span>
                                    </li>
                                    <?php if ( $_GET['page'] == 'likes' ) : ?>
                                    <li class="is-active" data-page="likes">
                                    <?php else : ?>
                                    <li>
                                    <?php endif; ?>
                                        <span class="level-item has-text-centered">
                                            <a href="userpage.php?name=<?php echo $_GET['name'] ?>&page=likes">
                                                <p class="heading">Likes</p>
                                                <p class="title is-size-5"><?php echo $likecount ?></p>
                                            </a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="section">
                                <?php if ( empty($_GET['page']) || $_GET['page'] == 'likes' ) : ?>
                                    <div class="head"></div>
                                    <?php if (empty($_GET['page']) && $userdata->getPostNum($pdo, $name) == 0) : ?>
                                        <div class="message">
                                            <div class="message-header">
                                                <p>表示する投稿がありません</p>
                                            </div>
                                            <div class="message-body">
                                                <p>このユーザはまだ投稿していません｡</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($_GET['page'] == 'likes' && $likecount == 0) : ?>
                                        <div class="message">
                                            <div class="message-header">
                                                <p>表示する投稿がありません</p>
                                            </div>
                                            <div class="message-body">
                                                <p>このユーザはまだいいねしていません｡</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php foreach ( $data as $loop ) : ?>
                                        <?php include('./include/article.php'); ?>
                                    <?php endforeach; ?>
                                    <div class="tail"></div>
                                <?php endif; ?>
                                <?php if ( $_GET['page'] == 'following' || $_GET['page'] == 'followers' ) : ?>
                                    <?php if ( $_GET['page'] == 'following' ) : ?>
                                        <h3 class="subtitle">フォロー</h3>
                                    <?php endif; ?>
                                    <?php if ( $_GET['page'] == 'followers' ) : ?>
                                        <h3 class="subtitle">フォロワー</h3>
                                    <?php endif; ?>
                                    <?php foreach ( $data as $loop ) : ?>
                                        <?php include('./include/follow.php'); ?>
                                    <?php endforeach; ?>
                                    <?php if (empty($data)) : ?>
                                        <?php if ($_GET['page'] == 'following') : ?>
                                            <div class="message">
                                                <div class="message-header">
                                                    <p>表示するユーザがありません｡</p>
                                                </div>
                                                <div class="message-body">
                                                    <p>このユーザはまだフォローしているユーザがいません｡</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($_GET['page'] == 'followers') : ?>
                                            <div class="message">
                                                <div class="message-header">
                                                    <p>表示するユーザがありません｡</p>
                                                </div>
                                                <div class="message-body">
                                                    <p>このユーザはまだフォロワーがいません｡</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
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