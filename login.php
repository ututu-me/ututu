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
$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
unset($errorMessage);
$errorMessage=0;
forward_main();

//ログインボタンが押された
if ( !empty($_POST['login']) ) {
	$mailadd = $_POST['mailadd'];
	$password = $_POST['password'];
	try {
		$stmt = $pdo->prepare("select * from account where mailadd = ?");
		$stmt->execute( array($mailadd) );
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ( password_verify($password, $row['password']) ) {
			session_regenerate_id(true);
			$_SESSION['userdata']= array(
				'id' => $row['id'],
				'name' => $row['name'],
				'mailadd' => $row['mailadd'],
				'sc_name' => $row['screen_name']
			);
			//ログイン情報をDBに記録
			$date = date('Y-m-d H:i:s');
			$stmt = $pdo->prepare("insert into signin_log(user_id, created_at, IPaddr) values(?, ?, ?)");
			$stmt->execute( array( get_name_to_id($pdo,$_SESSION['userdata']['name']), $date, $_SERVER['REMOTE_ADDR']) );
			header("Location:index.php");
			exit();
		} else {
			$errorMessage = 1;
		}
	} catch (PDOException $e) {
    $errorMessage = "database error";
	}
}
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
    <title>ログイン | UTUTU</title>
</head>
<script>
window.onpageshow = function(event) {
	if (event.persisted) {
		window.location.reload()
	}
};
</script>
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
				<div class="columns">
					<div class="column is-half is-offset-one-quarter">
						<section class="section">
							<div class="box">
								<div class="box-content">
									<div class="content">
										<?php if ( $errorMessage == 1 ) : ?>
											<div class="message is-danger">
												<div class="message-body">
													<span><i class="fas fa-exclamation-circle"></i></span>
													<span>IDまたはパスワードが間違っています｡</span>
												</div>
											</div>
										<?php endif; ?>
										<h3 class="subtitle has-text-black-ter">ログイン</h3>
										<form action="" method="post">
											<div class="field">
												<label class="label">ID</label>
												<div class="control">
												<input class="input" type="email" placeholder="name@ututu.me" name="mailadd">
												</div>
											</div>
											<div class="field">
												<label class="label">パスワード</label>
												<div class="control">
													<input class="input" type="password" placeholder="" name="password">
												</div>
											</div>
											<input class="button is-link" type="submit" name="login">
										</form>
										<div class="content">
										<p><a href="forgot.php">パスワードを忘れた方はこちら</a></p>
										<p><a href="signon.php">新規登録はこちら</a></p>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>
			</section>
	
		</div>
	</section>
	<?php include("./include/footer.php") ?>
</div>
</body>
</html>