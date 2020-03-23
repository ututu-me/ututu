<?php 
require "class.php";
require "sendmail.php";
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
forward_main();

if ( !empty($_POST['newpass']) ) {
    $password = $_POST['password'];
    $password_re = $_POST['password_re'];
    $check = array(4);
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
    if ( !isset($errorMessage) ) {
        $stmt = $pdo->prepare("select *, account.name as name from forgot inner join account on forgot.user_id = account.id where forgot.token = ?"); 
        $stmt->execute( array($_GET['token']) );
        $data = $stmt->fetch();
        $name = $data['name'];

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'update account set password = ? where name = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute( array( $hashed, $name ) );
        header("Location: index.php");
    }
}
//ログインボタンが押された
if ( !empty($_POST['send']) ) {
    if ( empty($_POST['mailadd']) ) {
        $errorMessage = "メールアドレスを入力してください｡";
    } else {
        $mailadd = $_POST['mailadd'];
        try {
            $stmt = $pdo->prepare("select count(*) from account where mailadd = ?"); 
            $stmt->execute( array($mailadd) );
            $count = $stmt->fetchColumn();
            if ( $count == 0 ) {
                $errorMessage = "入力されたメールアドレスは登録されていません｡";
            } else {
                $stmt = $pdo->prepare("select * from account where mailadd = ?"); 
                $stmt->execute( array($mailadd) );
                $data = $stmt->fetch();
                $scname = $data['screen_name'];
                $token = hash('sha256', uniqid(rand(), 1));
                    $body = "
					―――――――――――――――――――――――――――――――――――
					このメッセージはUTUTU(ututu.me)より自動送信されています｡
					お心当たりのない方はお手数ですが本メッセージの削除をお願いします｡
					―――――――――――――――――――――――――――――――――――
					*
					*
					*
					====================================================

					UTUTUのパスワード再設定リンクをお送りします

					====================================================

					$scname さん


                    UTUTUのパスワード再設定リンクをお送りします｡
                    
                    以下のリンクから24時間以内に新しいパスワードを設定してください｡


					★パスワードの再設定はこちらから
					https://ututu.me/forgot.php?token=$token

					*
					*
					*
					―――――――――――――――――――――――――――――――――――
					UTUTU
					ututu@ututu.me
					―――――――――――――――――――――――――――――――――――
                    ";
                    
                $stmt = $pdo->prepare("insert into forgot(user_id, token, resetdate) values(?, ?, ?)"); 
                $stmt->execute( array($data['id'], $token, date('Y-m-d H:i:s')) );
                if ( mail($mailadd, "パスワード再設定リンクをお送りしました | UTUTU", $body, "From: info@ututu.me\r\nContent-type: text/plain; charset=UTF-8") ) {
                    send_to_slack($body);
                } else {
                    send_to_slack("メール送信エラー");
                }
                header("Location:forgot_solve.php");
            }
        } catch (PDOException $e) {
        $errorMessage = "database error";
        }
    }
}
?>
<?php if ( empty($_GET['token']) ) : ?>
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
    <title>パスワード復旧 | UTUTU</title>
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
                                                    <span><i class="fas fa-exclamation-circle"></i></span>
                                                    <span><?php echo $errorMessage ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="subtitle has-text-black-ter">パスワード復旧</h3>
                                        <p class="content is-small">登録したメールアドレスを入力してください｡<br>パスワード再設定用URLを送信します｡</p>
                                        <form action="" method="post">
                                            <div class="field">
                                                <label class="label">ID</label>
                                                <div class="control">
                                                <input class="input" type="text" placeholder="name@ututu.me" name="mailadd">
                                                </div>
                                            </div>
                                            <input class="button is-link" type="submit" name="send">
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
<?php elseif ( !empty($_GET['token']) ) : ?>
<?php 
    $stmt = $pdo->prepare("select *, account.name as name from forgot inner join account on forgot.user_id = account.id where forgot.token = ?"); // 入力されたIDがDB上に登録されているか?
    $stmt->execute( array($_GET['token']) );
    $data = $stmt->fetch();

    $origin = strtotime($data['resetdate']);
    $diff = strtotime(date('Y-m-d H:i:s')) - $origin;
    if ( $diff > 86400 ) {
        header("Location: index.php");
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
    <title>パスワード復旧 | UTUTU</title>
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
							<div class="box">
								<div class="box-content">
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
										<h3 class="subtitle has-text-black-ter">パスワード再設定</h3>
										<form action="" method="post">
											<div class="field">
												<label class="label">ID</label>
												<div class="control">
												<input class="input" type="text" placeholder="" name="" value="<?php echo showText($data['name']) ?>" disabled>
												</div>
											</div>
											<div class="field">
												<label class="label">新しいパスワード</label>
												<div class="control">
													<input class="input <?php if ( $check[2] == 1 ) echo "is-danger" ?>" type="password" placeholder="" name="password">
													<input class="input" type="password" placeholder="確認" name="password_re">
													<p class="has-text-grey is-size-7">大文字小文字英数記号が各1文字以上入る8-128文字</p>
												</div>
											</div>
											<input class="button is-link" type="submit" name="newpass">
										</form>
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
<?php endif; ?>