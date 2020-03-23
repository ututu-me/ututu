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
if ( $_GET['page'] == 'del' ) {
    if ( !empty($_POST['delete']) ) {
        $password = $_POST['password'];
        if ( !empty($password) ) {
            $stmt = $pdo->prepare("select password from account where name = ?");
            $stmt->execute( array($_SESSION['userdata']['name']) );
            $passhash = $stmt->fetch();
            if ( !password_verify($password, $passhash['password']) ) {
                $errorMessage[] = 'パスワードが違います｡';
            } else {
                //アカウント削除処理
                unlink("./image/icon/".$_SESSION['userdata']['name'].".png");
                $sql = 'update dream set delete_flag = 1, delete_date = ? where user_id = ?';
                $stmt = $pdo->prepare($sql);
                $stmt->execute( array(date('Y-m-d H:i:s'), $_SESSION['userdata']['id']) );
                $sql = 'delete from account where id = ?';
                $stmt = $pdo->prepare($sql);
                $stmt->execute( array($_SESSION['userdata']['id']) );
                $_SESSION = array();
                session_destroy();
                $_SESSION['thanks'] = 'deleted';
                header("Location: settings.php");
            }
        } else {
            $errorMessage[] = 'パスワードを入力してください｡';
        }
    }
}
if ( !empty($_POST['update']) ) {
    $icon = $_FILES['icon'];
    $header = $_FILES['header'];
    $name = $_POST['name'];
    $id = $_POST['id'];
    $bio = $_POST['bio'];
    //$mailadd = $_POST['mailadd'];
    $mailadd = $_SESSION['userdata']['mailadd'];
    $new_password = $_POST['new_password'];
    $new_password_re = $_POST['new_password_re'];
    $current_password = $_POST['current_password'];

    if ( is_uploaded_file($icon['tmp_name']) ) {
        $mimetype = mime_content_type( $icon['tmp_name'] ); //mimetypeを取得
        if ( /* jpgeかpngのときのみ保存処理に入る */$mimetype == 'image/jpeg' || $mimetype == 'image/png' ) {
            if ( $icon['size'] <= 5242880 ) {
                $iconimage = $icon['tmp_name']; 
                $deg = getImageRotateDegFromExif($iconimage);
                if ( $mimetype == 'image/jpeg' ) {
                    imageconverter($iconimage, $iconimage, $mimetype);
                }

                rotateImage($iconimage, $iconimage, $deg);

                $iconimage = trimfillimage($iconimage, 256, 256); //256×256の画像に変換
                if ( is_link("./image/icon/".$_SESSION['userdata']['name'].".png") ) {
                    unlink("./image/icon/".$_SESSION['userdata']['name'].".png");
                }
                if ( move_uploaded_file($iconimage, "./image/icon/".$_SESSION['userdata']['name'].".png") ) {
                    chmod("./image/icon/".$_SESSION['userdata']['name'].".png", 0644);
                }
            } else {
                $errorMessage[] = '5MBまでの画像をアップロードしてください｡';
            }
        } else {
            $errorMessage[] = 'jpegまたはpng形式の画像を選択してください｡';
        }
    } else {
        if ( $icon['error'] == 1 ) {
            $errorMessage[] = '5MBまでの画像をアップロードしてください｡';
        }
        if ( $icon['error'] == 3 || $icon['error'] == 6 ) {
            $errorMessage[] = 'アップロードに失敗しました｡';
        }
    }
    if ( is_uploaded_file($header['tmp_name']) ) {
        $mimetype = mime_content_type( $header['tmp_name'] ); //mimetypeを取得
        if ( /* jpgeかpngのときのみ保存処理に入る */$mimetype == 'image/jpeg' || $mimetype == 'image/png' ) {
            if ( $header['size'] <= 5242880 ) {
                $headerimage = $header['tmp_name']; 
                $deg = getImageRotateDegFromExif($headerimage);
                if ( $mimetype == 'image/jpeg' ) {
                    imageconverter($headerimage, $headerimage, $mimetype);
                }

                rotateImage($headerimage, $headerimage, $deg);

                $headerimage = trimfillimage($headerimage, 1500, 500); //1500×500の画像に変換
                if ( is_link("./image/header/".$_SESSION['userdata']['name'].".png") ) {
                    unlink("./image/header/".$_SESSION['userdata']['name'].".png");
                }
                if ( move_uploaded_file($headerimage, "./image/header/".$_SESSION['userdata']['name'].".png") ) {
                    chmod("./image/header/".$_SESSION['userdata']['name'].".png", 0644);
                } else {
                    $errorMessage[] = '画像の保存に失敗しました｡';
                }
            } else {
                $errorMessage[] = '5MBまでの画像をアップロードしてください｡';
            }
        } else {
            $errorMessage[] = 'jpegまたはpng形式の画像を選択してください｡';
        }
    } else {
        if ( $icon['error'] == 1 ) {
            $errorMessage[] = '5MBまでの画像をアップロードしてください｡';
        }
        if ( $icon['error'] == 3 || $icon['error'] == 6 ) {
            $errorMessage[] = 'アップロードに失敗しました｡';
        }
    }

    //名前のシンタックス
	if ( !preg_match("/^.{1,15}$/u", $name) ) {
		$errorMessage[] = '名前は15文字以内で入力してください｡';
    }
    if ( $_SESSION['userdata']['name'] != $id ) {
        //IDのシンタックス
        if ( !preg_match("/^[a-zA-Z0-9_]{1,15}$/", $id) ) {
            $errorMessage[] = 'IDに使用できない文字が含まれています｡';
        } else {
            // IDのユニークチェック
            $stmt = $pdo->prepare("select count(*) from account where name = ?");
            $stmt->execute( array($id) );
            $data = $stmt->fetchColumn();
            if ( $data != 0 ) {
                $errorMessage[] = 'そのIDは既に使用されています｡';
            }
        }
    }
    if ( $_SESSION['userdata']['mailadd'] != $mailadd ) {
        //メールアドレスのシンタックス
        if ( !filter_var( $mailadd, FILTER_VALIDATE_EMAIL ) ) {
            $errorMessage[] = 'メールアドレスの形式が違います｡';
        } else {
            // メールアドレスのユニークチェック
            $stmt = $pdo->prepare("select count(*) from account where mailadd = ?");
            $stmt->execute( array($mailadd) );
            $data = $stmt->fetchColumn();
            if ( $data != 0 ) {
                $errorMessage[] = 'そのメールアドレスは既に登録されています｡';
            }
        }
    }
    if ( !empty($new_password) ) {
        //パスワードのシンタックス
        if ( !preg_match("/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!-\/:-@[-`{-~])[a-zA-Z0-9!-~]{8,128}$/", $new_password) ) {
            $errorMessage[] = 'パスワードは半角大文字小文字英数記号を各1文字ずつ使用してください｡';
        }
        if ( !preg_match("/^.{8,128}$/", $new_password) ) {
            $errorMessage[] = 'パスワードは8文字以上128文字以下で入力してください｡';
        }
        if ( $new_password != $new_password_re ) {
            $errorMessage[] = '確認用パスワードが一致しませんでした｡';
        }

        $stmt = $pdo->prepare("select password from account where name = ?");
        $stmt->execute( array($_SESSION['userdata']['name']) );
		$passhash = $stmt->fetch();
        if ( !password_verify($current_password, $passhash['password']) ) {
            $errorMessage[] = 'パスワードが違います｡';
        }
    }

    if ( empty($errorMessage) ) {
        if ( /* パスワード変更時の処理 */ !empty($new_password) ) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "update account set screen_name = ?, name = ?, bio = ?, mailadd = ?, password = ? where name = ?";
            $inputvars = array( $name, $id, $bio, $mailadd, $hashed, $_SESSION['userdata']['name'] );
        } /* パスワード変更なし時の処理 */ else {
            $sql = "update account set screen_name = ?, name = ?, bio = ?, mailadd = ? where name = ?";
            $inputvars = array( $name, $id, $bio, $mailadd, $_SESSION['userdata']['name'] );
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute( $inputvars );

        $stmt = $pdo->prepare("select * from account where name = ?");
		$stmt->execute( array($id) );
        $newdata = $stmt->fetch();
        if ( /* IDの変更があった場合､画像のファイル名も変える */$_SESSION['userdata']['name'] != $newdata['name'] ) {
            rename( "./image/header/".$_SESSION['userdata']['name'].".png" , "./image/header/".$newdata['name'].".png" );
            rename( "./image/icon/".$_SESSION['userdata']['name'].".png" , "./image/icon/".$newdata['name'].".png" );
        }
        $_SESSION['userdata']= array(
            'id' => $newdata['id'],
            'name' => $newdata['name'],
            'mailadd' => $newdata['mailadd'],
            'sc_name' => $newdata['screen_name']
        );
        $errorMessage[] = "更新成功";
        header("Location: settings.php", true, 301);
        exit;
    }
}
//フォロー済みユーザによる夢を取得
$sql = "select * from account where name = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute( array($_SESSION['userdata']['name']) );
$mydata = $stmt->fetch();
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="./include/js/unity.js"></script>
    <script src="./include/js/header.js"></script>
    <title>設定 | UTUTU</title>
</head>
<body>
    <div class="wrap">
        <?php include('./include/header.php'); ?>
        <main>
            <div class="hero is-bold is-primary" style="background-image: url('https://ututu.me/image/header/<?php echo $_SESSION['userdata']['name'] ?>.png?<?php echo date('dHis') ?>'); background-size: cover;">
                <div class="hero-body">
                    <div class="container">
                        <div class="media article">
                            <div class="media-left">
                                <div class="image is-64x64">
                                    <img src="https://ututu.me/image/icon/<?php echo $_SESSION['userdata']['name'] ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%">
                                </div>
                            </div>
                            <div class="media-content">
                                <div class="menu">
                                    <ul class="menu-list">
                                        <li><p class="title has-background-black has-text-white is-inline-block"><?php echo showText($_SESSION['userdata']['sc_name']) ?></p></li>
                                        <li><p class="subtitle has-background-black has-text-white is-inline-block">@<?php echo $_SESSION['userdata']['name'] ?></p></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="columns">
                    <div class="main-column column is-9">
                        <section class="container">
                            <?php if ( $_GET['page'] != 'del' ): ?>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="section">
                                    <h3 class="subtitle">設定</h3>
                                </div>
                                <div class="box">
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

                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-user"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">アイコン画像</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="file">
                                                        <label class="file-label">
                                                            <input class="file-input" type="file" name="icon" accept="image/*">
                                                            <span class="file-cta">
                                                                <span class="file-icon">
                                                                    <i class="fas fa-upload"></i>
                                                                </span>
                                                                <span class="file-label">
                                                                    ファイルの選択
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <p class="has-text-grey is-size-7">5MBまでのjpgまたはpng</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-image"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">ヘッダー画像</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="file">
                                                        <label class="file-label">
                                                            <input class="file-input" type="file" name="header" accept="image/*">
                                                            <span class="file-cta">
                                                                <span class="file-icon">
                                                                    <i class="fas fa-upload"></i>
                                                                </span>
                                                                <span class="file-label">
                                                                    ファイルの選択
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <p class="has-text-grey is-size-7">5MBまでのjpgまたはpng</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-signature"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">ユーザ名</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="input post-title" name="name" value="<?php echo showText($_SESSION['userdata']['sc_name']) ?>" style="max-width: 30rem;">
                                                    <p class="has-text-grey is-size-7">15文字まで</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-at"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">ID</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="input post-title" name="id" value="<?php echo $_SESSION['userdata']['name'] ?>" style="max-width: 30rem;">
                                                    <p class="has-text-grey is-size-7">15文字までの半角英数字及び_(アンダースコア)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-align-left"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">bio</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <textarea name="bio" id="" cols="30" rows="10" class="textarea"><?php echo showText($mydata['bio']) ?></textarea>
                                                    <p class="has-text-grey is-size-7">200文字まで</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-envelope"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">メールアドレス</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php echo showText($_SESSION['userdata']['mailadd']) ?>
                                                    <p></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-key"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">パスワードの変更</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label for="" class="label is-small has-text-weight-normal	">新しいパスワード</label>
                                                    <input type="password" class="input post-title" name="new_password">
                                                    <label for="" class="label is-small has-text-weight-normal	">再入力</label>
                                                    <input type="password" class="input post-title" name="new_password_re">
                                                    <label for="" class="label is-small has-text-weight-normal	">現在のパスワード</label>
                                                    <input type="password" class="input post-title" name="current_password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <input type="submit" value="変更の保存" class="button is-primary" name="update">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="content">
                                        <div class="field">
                                            <div class="content">
                                                <div class="control">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <span class="icon"><i class="fas fa-user-times"></i></span>
                                                        </div>
                                                        <div class="media-content">
                                                            <div class="content">
                                                                <span class="label">アカウントの削除</span>
                                                                <a href="settings.php?page=del" class="button is-danger">アカウントの削除</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php else : ?>
                                <div class="section">
                                    <h3 class="subtitle">アカウントの削除</h3>
                                </div>
                                <form action="" method="post">
                                    <div class="box">
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
                                        <div class="content">
                                            <div class="field">
                                                <div class="content">
                                                    <div class="control">
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <span class="icon"><i class="fas fa-key"></i></span>
                                                            </div>
                                                            <div class="media-content">
                                                                <div class="content">
                                                                    <span class="label">パスワード</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="password" class="input post-title" name="password">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="content">
                                            <div class="field">
                                                <div class="content">
                                                    <div class="control">
                                                        <input type="submit" value="アカウントの削除" class="button is-danger" name="delete">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </section>
                    </div>
                </div>
            </div>
        </main>
        <?php include("./include/footer.php") ?>
    </div>
</body>
</html>