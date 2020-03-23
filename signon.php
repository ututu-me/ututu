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
require "sendmail.php";
require "authdb.php";
$_SESSION['latest_activity'] = date('Y-m-d H:i:s'); 
$errorMessage = array();
unset($errorMessage);
forward_main();

//登録ボタンが押された
if ( !empty($_POST['signon']) ) {
	$name = $_POST['name'];
	$id = $_POST['id'];
	$mailadd = $_POST['mailadd'];
	$password = $_POST['password'];
  	$password_re = $_POST['password_re'];
  	$check = array(4);

	//名前のシンタックス
	if ( !preg_match("/^.{1,30}$/", $id) ) {
		$errorMessage[] = '名前は30文字以内で入力してください｡';
		$check[3] = 1;
	}
  //IDのシンタックス
  if ( !preg_match("/^[a-zA-Z0-9_]{1,15}$/", $id) ) {
    $errorMessage[] = 'IDに使用できない文字が含まれています｡';
    $check[0] = 1;
  } else {
    // IDのユニークチェック
    $stmt = $pdo->prepare("select count(*) from account where name = ?");
    $stmt->execute( array($id) );
    $data = $stmt->fetchColumn();
    if ( $data != 0 ) {
		$errorMessage[] = 'そのIDは既に使用されています｡';
      	$check[0] = 1;
    }
  }
  //メールアドレスのシンタックス
  if ( !filter_var( $mailadd, FILTER_VALIDATE_EMAIL ) ) {
	$errorMessage[] = 'メールアドレスの形式が違います｡';
    $check[1] = 1;
  } else {
	// メールアドレスのユニークチェック
	$stmt = $pdo->prepare("select count(*) from account where mailadd = ?");
	$stmt->execute( array($mailadd) );
	$data = $stmt->fetchColumn();
	if ( $data != 0 ) {
		$errorMessage[] = 'そのメールアドレスは既に登録されています｡';
		$check[1] = 1;
	}
  }
  	//パスワードのシンタックス
	if ( !preg_match("/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!-\/:-@[-`{-~])[a-zA-Z0-9!-~]{8,128}$/", $password) ) {
		$errorMessage[] = 'パスワードは半角大文字小文字英数記号を各1文字ずつ使用してください｡';
		$check[2] = 1;
	}
	if ( !preg_match("/^.{8,128}$/", $password) ) {
		$errorMessage[] = 'パスワードは8文字以上128文字以下で入力してください｡';
		$check[2] = 1;
	}
	if ( $password != $password_re ) {
		$errorMessage[] = '確認用パスワードが一致しませんでした｡';
		$check[2] = 1;
	}
	//利用規約に同意しているか確認
	if( !isset($_POST['rule']) ) {
		$errorMessage[] = '利用規約に同意してください｡';
	}

	//アカウント情報をDBへ登録する
	if ( !isset($errorMessage) ) {
		$hashed = password_hash($password, PASSWORD_DEFAULT);
		try {
			$date = date('Y-m-d H:i:s');
			$urltoken = hash('sha256', uniqid(rand(), 1));
			$uniqid = uniqid(rand());
			$sql = 'insert into account(name, mailadd, screen_name, password, token, active_status, join_date) values(?, ?, ?, ?, ?, ?, ?)';
			$stmt = $pdo->prepare($sql);
			$stmt->execute( array( $id, $mailadd, $name, $hashed, $urltoken, 0, $date ) );

			if ( $stmt->rowCount() == 1 ) {
				$body = "
					―――――――――――――――――――――――――――――――――――
					このメッセージはUTUTU(ututu.me)より自動送信されています｡
					お心当たりのない方はお手数ですが本メッセージの削除をお願いします｡
					―――――――――――――――――――――――――――――――――――
					*
					*
					*
					====================================================

					UTUTUへようこそ

					====================================================

					$name さん


					この度はUTUTUへのご登録誠にありがとうございます｡

					UTUTUは夢日記を綴って､残すことができるサービスです｡

					他のユーザの夢日記も閲覧することができます｡

					素敵な夢にはいいねを押してあとで読むこともできます｡


					★ログインはこちらから
					https://ututu.me/index.php

					*
					*
					*
					―――――――――――――――――――――――――――――――――――
					UTUTU
					ututu@ututu.me
					―――――――――――――――――――――――――――――――――――
					";
				mail($mailadd, "登録ありがとうございます | UTUTU", $body, "From: info@ututu.me\r\nContent-type: text; charset=UTF-8");
				send_to_slack($body);
			}
			
			$stmt = $pdo->prepare("select * from account where name = ?");
			$stmt->execute( array($id) );
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$_SESSION['userdata']= array(
				'id' => $row['id'],
				'name' => $row['name'],
				'mailadd' => $row['mailadd'],
				'sc_name' => $row['screen_name']
			);
			
			$_SESSION['signon'] = 'done';

			//アイコンとヘッダをデフォルトのものをコピーする
			//copy("./image/defaulticon.png", "./image/icon/".$_SESSION['userdata']['name'].".png");
			//copy("./image/defaultheader.png", "./image/header/".$_SESSION['userdata']['name'].".png");
			symlink ("/var/www/ututu/image/default_icon.png", "./image/icon/".$_SESSION['userdata']['name'].".png");
			symlink ("/var/www/ututu/image/default_header.png", "./image/header/".$_SESSION['userdata']['name'].".png");
			header("Location:welcome.php");
			exit();
		} catch (PDOException $e) {
			$errorMessage = "database error";
		}
	}
} else {
	$mailadd = '';
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
     
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <title>新規登録 | UTUTU</title>
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
				<div class="columns">
					<div class="column is-half is-offset-one-quarter">
						<section class="section">
							<div class="card">
								<div class="card-content">
									<div class="content">
										<?php if ( isset($errorMessage) ) : ?>
											<div class="message is-danger">
												<div class="message-body">
													<?php foreach ( $errorMessage as $loop ) : ?>
														<p>
															<span><i class="fas fa-exclamation-circle"></i></span>
															<span><?php echo $loop ?></span>
														</p>
													<?php endforeach; ?>
												</div>
											</div>
										<?php endif; ?>
										<h3 class="subtitle has-text-black-ter">新規登録</h3>
										<form action="" method="post">
											<div class="field">
												<label class="label">名前</label>
												<div class="control">
												<input class="input <?php if ( $check[3] == 1 ) echo "is-danger" ?>" type="text" name="name" value="<?php echo $name ?>">
												<p class="has-text-grey is-size-7">30文字まで</p>
												</div>
											</div>
											<div class="field">
												<label class="label">ID</label>
												<div class="control">
												<input class="input <?php if ( $check[0] == 1 ) echo "is-danger" ?>" type="text" placeholder="" name="id" value="<?php echo $id ?>">
												<p class="has-text-grey is-size-7">30文字までの半角英数字及び_(アンダースコア)</p>
												<p class="has-text-grey is-size-7"></p>
												</div>
											</div>
											<div class="field">
												<label class="label">メールアドレス</label>
												<div class="control">
												<input class="input <?php if ( $check[1] == 1 ) echo "is-danger" ?>" type="email" placeholder="name@ututu.me" name="mailadd" value="<?php echo $mailadd ?>">
												</div>
											</div>
											<div class="field">
												<label class="label">パスワード</label>
												<div class="control">
													<input class="input <?php if ( $check[2] == 1 ) echo "is-danger" ?>" type="password" placeholder="" name="password">
													<input class="input" type="password" placeholder="確認" name="password_re">
													<p class="has-text-grey is-size-7">大文字小文字英数記号が各1文字以上入る8-128文字</p>
												</div>
											</div>
											<div class="field">
												<label class="checkbox">
													<input type="checkbox" name="rule">
													<span><a class="has-text-info" href="rule.php" target="_blank">利用規約とプライバシーポリシー</a>に同意する</span>
												</label>
											</div>
											<input class="button is-link" type="submit" name="signon" value="登録">
										</form>
										<div class="content">
										<p><a href="login.php">ログインはこちら</a></p>
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