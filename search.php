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

if ( isset($_GET['from']) ) {
    if ( $_GET['from'] == 'header' ) {
        $search_word = filter_input(INPUT_GET, 'search');
    }
    if ( $_GET['from'] == 'box' ) {
        $search_word = filter_input(INPUT_GET, 'search_main');
    }
}
//フォロー済みユーザによる夢を取得
$stmt = $pdo->prepare("select *, dream.id as dreamid from dream inner join account on dream.user_id = account.id where dream.id <= ? and body like ? and delete_flag is null order by dream.id desc limit 500");
$stmt->execute( array(300, '%'.$search_word.'%') );
$data = $stmt->fetchAll();
$stmt = $pdo->prepare("select count(*) from dream inner join account on dream.user_id = account.id where dream.id <= ? and body like ? and delete_flag is null order by dream.id desc limit 500");
$stmt->execute( array(300, '%'.$search_word.'%') );
$count = $stmt->fetchColumn();
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
    <title><?php echo showText( $search_word ) ?> の検索結果 | UTUTU</title>
</head>
<script>
    //search_mainにフォーカスしているときにEnterする際､searchb_mainにsubmitする
    $(document).ready(function(){
        $(".search_main").keypress(function(){
            if (event.keyCode == 13) {
                $('#searchb_main').trigger("click");
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
                        <div class="content">
                            <!-- <form class="box" action="search.php" method="get"> -->
                                <div class="content">
                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input class="input search_main" type="text" placeholder="検索" name="search_main" value="<?php echo showText( $search_word ) ?>" form="searchbox">
                                        </div>
                                        <div class="control">
                                            <input type="submit" id="searchb_main" name="from" value="box" style="display: none;" form="searchbox">
                                            <label class="button is-info" for="searchb_main"><span class="icon"><i class="fas fa-search"></i></span></label>
                                        </div>
                                    </div>
                                </div>
                            <!-- </form> -->
                        </div>
                        <?php if ( !empty($search_word) ) : ?>
                            <p class="subtitle">
                                <span class="content"><span class="has-text-weight-bold has-text-info"><?php echo showText( $search_word ) ?></span>の検索結果</span>
                                <span class="content is-size-6"><span class="has-text-weight-bold has-text-info"><?php echo $count ?></span> 件</span>
                            </p>
                            <?php foreach ( $data as $loop ) : ?>
                                <?php include('./include/article.php'); ?>
                            <?php endforeach; ?>
                            <?php if ($count == 0): ?>
                                <div class="message">
                                    <div class="message-header">
                                        <p><strong><?php echo showText( $search_word ) ?></strong>の検索結果は見つかりませんでした</p>
                                    </div>
                                    <div class="message-body">
                                        <p>
                                            他の検索ワードを試してみてください｡
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
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