<?php
session_name('ututu_dream');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);
ini_set('session.gc_maxlifetime', 60*60*12);
session_save_path('/home/ututu/sessions');
session_start([
	'cookie_lifetime' => 60*60*24*365,
]);
require "class.php";
require "authdb.php";

//session_update($pdo);

$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
unset($errorMessage);
$errorMessage=0;

$userdata = new UserData();

//フォロー済みユーザによる夢を取得
$stmt = $pdo->prepare("select *, dream.id as dreamid from dream inner join account on dream.user_id = account.id where delete_flag is null order by dream.id desc");
$stmt->execute();
$data = $stmt->fetchAll();
$showarticlecount = 0;
$userdata = new UserStatus($pdo, $_SESSION['userdata']['name']);
?>
<?php if ( empty($_SESSION['userdata']) ) : ?>
<html>
    <head>
        <?php include("include/analytics.php"); ?>
        <?php include("include/version.php") ?>
        <meta http-equiv="content-type" charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="./include/css/unity.css" rel="stylesheet">
        <title>夢日記SNS UTUTU</title>
    </head>
    <body>
        <header class="hero is-primary is-bold is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title is-size-1" style="max-width: 48rem"><img src="image/ututu_logo.svg" alt="ututu_logo"></h1>
                    <p class="subtitle">UTUTUは夢日記を書いて､公開しあえるサービスです｡</p>
                    <a href="login.php" class="button is-large">ログイン</a>
                    <a href="signon.php" class="button is-link is-large">新規登録する</a>
                </div>
            </div>
        </header>
        <?php include("./include/footer.php") ?>
    </body>
</html>
<?php else : ?>
<html>
<head>
    <?php include("include/analytics.php"); ?>
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
    <link rel="icon" href="image/ututu_icon.svg" type="image/svg+xml" sizes="any">
    <link rel="icon" href="image/ututu_icon_180.png" type="image/x-png">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="./include/js/jquery.inview.min.js"></script>
    <script src="./include/js/unity.js"></script>
    <script src="./include/js/header.js"></script>
    <script src="./include/js/article.js"></script>
    <title>ホーム | UTUTU</title>
</head>
<script>
    //PC表示時の投稿処理
    $(document).on('click', '.posting-button', function(){
        var title;
        var body;
        var sendData;
        var html;
        var mes;
        mes='';
        title = $('.post-title').val();
        body = $('.post-body').val();
 
        $.ajax({
            url:'https://ututu.me/ajax/post.php',
            type: 'post',
            dataType: 'json',
            data: {
                title: title,
                body: body
            }
        }).done( function(data){
            if ( data.flag != 0 ) {
                for ( let i in data.flag ) {
                    mes = mes + `<p><span><i class="fas fa-exclamation-circle"></i></span><span>${data.flag[i]}</span></p>`;
                }
                html = `
                    <div class="posting-mesaage message is-danger">
                        <div class="message-body">   
                        ${mes}
                        </div>
                    </div>
                    `;
                if ( $('.posting-mesaage').length == 0 ) {
                    $('.posting-box').prepend(html);
                }
            } else {
                location.reload();
            }
        }).fail(function(data){
    
        });
    });
    //投稿ボタンにフォーカス時Enterで送信
    $(document).ready(function(){
        $(".posting-button").keypress(function(){
            if (event.keyCode == 13) {
                $('.posting-button').trigger("click");
                return false;
            }
        });
    });
</script>
<body>
    <div class="wrap">
        <?php include('./include/header.php'); ?>
        <div class="container">
            <main class="columns">
                <div class="main-column column is-9">
                    <section class="section is-hidden-touch">
                        <div class="container">
                            <div class="posting-box box">
                                <h3 class="subtitle">夢を残す</h3>
                                <div class="field">
                                    <div class="content">
                                        <div class="control">
                                            <span class="label">タイトル</span>
                                            <input type="text" class="input post-title" name="title" tabindex="1">
                                            <span class='title-c has-text-black-ter'>0</span>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="control">
                                            <span class="label">本文</span>
                                            <textarea class="post-body textarea" style="min-height: 8rem;" tabindex="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="level">
                                        <div class="level-left body-c has-text-black-ter">0</div>
                                        <div class=" level-right is-hidden-touch">
                                            <span class="button posting-button" tabindex="3">
                                                <span style="padding-right: 0.75rem;"><i class="fas fa-pen-nib"></i></span>
                                                <p>投稿する</p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="section">
                        <div class="container">
                            <section class="tabs is-full-width is-hidden-desktop">
                                <ul>
                                    <li class="is-active"><a class="subtitle"><i class="fas fa-home" style="padding-right: 0.75rem"></i>ホーム</a></li>
                                    <li><a href="search.php" class="subtitle"><i class="fas fa-search" style="padding-right: 0.75rem"></i>検索</a></li>
                                </ul>
                            </section>
                            <p class="subtitle is-hidden-touch">ホーム</p>
                            <div class="head"></div>
                            <?php foreach ( $data as $loop ) : ?>
                                <?php if ( $showarticlecount <= 50 ) : ?>
                                    <?php if ( $_SESSION['userdata']['id'] == $loop['user_id'] || get_isfollow($pdo, $_SESSION['userdata']['id'], $loop['user_id']) == 1 ) : ?>
                                        <?php include('./include/article.php'); ?>
                                        <?php $showarticlecount = $showarticlecount+1; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ( $showarticlecount == 0 ) : ?>
                                <?php //表示するものがないとき ?>
                                <?php if ( /* フォロー数が0なら */$userdata->getFollowNum($pdo, $name) == 0 ) : ?>
                                    <div class="message notif-noarticle">
                                        <div class="message-header">
                                            <p>使い方について</p>
                                        </div>
                                        <div class="message-body">
                                            <p>あなたの夢日記を投稿するか検索機能や新着投稿から夢日記を探して呼んでみましょう｡</p>
                                            <div class="content">
                                                <a href="dreaming.php" class="button">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-pen-nib"></i></span>
                                                    <p>夢を残す</p>
                                                </a>
                                                <a href="search.php" class="button is-link">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-search"></i></span>
                                                    <p>検索</p>
                                                </a>
                                                <a href="latest.php" class="button is-link">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-newspaper"></i></span>
                                                    <p>新着投稿</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="message notif-noarticle">
                                        <div class="message-header">
                                            <p>いいね機能について</p>
                                        </div>
                                        <div class="message-body">
                                            <p>
                                                <span class="icon" style="color: lightcoral;"><i class='fas fa-heart'></i></span>を押すと投稿したユーザにいいねを送ることができます｡
                                            </p>
                                        </div>
                                    </div>
                                <?php /* フォローしている人がいるにも関わらず表示するものがないとき */else : ?>
                                    <div class="message notif-noarticle">
                                        <div class="message-header">
                                            <p>表示する夢日記がありません｡</p>
                                        </div>
                                        <div class="message-body">
                                            <p>あなたの夢日記を投稿するか検索機能や新着投稿から夢日記を探して読んでみましょう｡</p>
                                            <div class="content">
                                                <a href="dreaming.php" class="button">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-pen-nib"></i></span>
                                                    <p>夢を残す</p>
                                                </a>
                                                <a href="search.php" class="button is-link">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-search"></i></span>
                                                    <p>検索</p>
                                                </a>
                                                <a href="latest.php" class="button is-link">
                                                    <span style="padding-right: 0.75rem;"><i class="fas fa-newspaper"></i></span>
                                                    <p>新着投稿</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            <div class="tail"></div>
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
<?php endif; ?>
