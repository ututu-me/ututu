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

$userdata = new UserData();

//フォロー済みユーザによる夢を取得
$stmt = $pdo->prepare("select *, dream.id as dreamid from dream inner join account on dream.user_id = account.id where delete_flag is null order by dream.id desc limit 50");
$stmt->execute();
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
    <title>投稿する | UTUTU</title>
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
                location.href = 'index.php';
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
                    <section class="section">
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
                                        <div class=" level-right">
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
                </div>
                <?php include("./include/popular_new.php"); ?>
            </main>
        </div>
        <?php include("./include/footer.php") ?>
    </div>
</body>
</html>