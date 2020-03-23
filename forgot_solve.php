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
$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
unset($errorMessage);
$errorMessage=0;
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <title>メールをご確認ください｡ | UTUTU</title>
</head>
<body>
<div class="wrap">
    <header class="navbar" role="navigation" aria-label="main navigation" style="box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1), 0 0 0 1px rgba(10, 10, 10, 0.1); z-index: 100;">
        <div class="navbar-brand">
            
            <div class="navbar-item"><a href="index.php"><img src="image/ututu_logo.svg" alt=""></a></div>
            
        </div>
        <div class="navbar-start">
            
        </div>
        <div class="navbar-end">
            
        </div>
	</header>
	<section class="hero is-primary is-bold is-fullheight">
		<div class="hero-body">
			<section class="container">
				<p class="content is-size-3">メールをご確認ください｡</p>
				<p class="content is-size-5">入力されたメールアドレスにパスワード再発行URLを送信しました｡</p>
				<a href="login.php" class="button is-link">ログイン画面へ</a>
			</section>
	
		</div>
    </section>
    <?php include("./include/footer.php") ?>
</div>
</body>
</html>