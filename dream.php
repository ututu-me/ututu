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
//forward_login();

$stmt = $pdo->prepare("select *, dream.id as dreamid from dream inner join account on dream.user_id = account.id where dream.id = ? and dream.delete_flag is null");
$stmt->execute( array($_GET['id']) );
$data = $stmt->fetchAll();
$showarticlecount = 0;
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
    <?php if ( isset($_SESSION['userdata']) ) : ?>
    <script src="./include/js/article.js"></script>
    <?php endif; ?>
    <script src="./include/js/follow.js"></script>
    <?php if ( !empty($errorMessage) ) : ?>
        <title><?php echo $errorMessage ?> | UTUTU</title>
    <?php else : ?>
        <?php if ( empty($data[0]['title']) ) : ?>
        <title>この投稿は表示できません | UTUTU</title>
        <?php else : ?>
        <title><?php echo showText($data[0]['title']) ?> | UTUTU</title>
        <?php endif; ?>
    <?php endif; ?>
</head>
<?php if ( !isset($_SESSION['userdata']) ) : ?>
    <style>
    /* いいねパネル */
    .like-box {
        display: none;
        position: fixed;
        z-index: 100;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .like-box + .overlay {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,0.3);
        z-index: 99;
        display: none;
    }
    </style>
    <script>
        $(document).on('click', '.like-button', function(){
            var button = $('.like-box');
            var overlay = $('.like-box+.overlay');
            button.show();
            overlay.show();
        });
        $(document).on('click', '.like-box+.overlay', function(){
            var overlay;
            var likebox;
            overlay = $(this);
            menu = overlay.prev();

            overlay.hide();
            likebox.hide();
        });
    </script>
<?php endif; ?>
<body>
    <div class="wrap">
        <?php if ( !isset($_SESSION['userdata']) ) : ?>
        <div class="like-box box">
            <div class="content">
                <p class="subtitle is-size-4">ログインしてください｡</p>
                <a href="login.php" class="button">ログイン</a>
                <p class="subtitle is-size-4">まだの方は登録するといいねなどの機能が利用できます｡</p>
                <a href="signon.php" class="button is-link">新規登録する</a>
            </div>
        </div>
        <div class="overlay"></div>
        <?php endif; ?>
        <?php include('./include/header.php'); ?>
        <div class="container">
            <main class="columns">
                <div class="main-column column is-9">
                    <?php if ( !isset($_SESSION['userdata']) ) : ?>
                    <div class="navbar is-hidden-desktop">
                        <div class="navbar-brand">
                            <div class="navbar-item">
                                <div class="buttons">
                                    <a href="login.php" class="button">ログイン</a>
                                    <a href="signon.php" class="button is-link">新規登録する</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ( !empty($errorMessage) ) : ?>
                    <?php echo $errorMessage ?>
                    <?php else : ?>
                    <div class="section">
                        <div class="box">
                            <?php foreach ( $data as $loop ) : ?>
                                <?php include('./include/article.php'); ?>
                                <?php $showarticlecount = $showarticlecount+1; ?>
                            <?php endforeach; ?>
                            <?php if ( $showarticlecount == 0 ) : ?>
                                <div class="message is-danger">
                                    <div class="message-header">
                                        <p>エラー</p>
                                    </div>
                                    <div class="message-body">
                                        <div class="content">
                                            <p>この投稿は表示できません｡</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php include("./include/popular_new.php"); ?>
            </main>
        </div>
        <?php include("./include/footer.php") ?>
    </div>
</body>
</html>