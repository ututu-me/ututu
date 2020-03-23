<?php

class Dbdata {
	private $host;
	private $user;
	private $pass;
	private $dbname;
	public function __construct($host, $user, $pass, $dbname) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->dbname = $dbname;
	} 
	public function getHostname() { return $this->host; }
	public function getUsername() { return $this->user; }
	public function getPassword() { return $this->pass; }
	public function getDbname() { return $this->dbname; }
}

class UserData {
	private $name;
	private $screen_name;
	private $post_num;
	public function findIdToName($pdo, $id) {
		$stmt = $pdo->prepare("select * from account where id = ?");
		$stmt->execute( array($id) );
		$result = $stmt->fetch();
		$this->$name = $result['name'];
	}
	public function findIdToScreenName($pdo, $id) {
		$stmt = $pdo->prepare("select * from account where id = ?");
		$stmt->execute( array($id) );
		$result = $stmt->fetch();
		$this->$name = $result['screen_name'];
    }
    
    public function findNameToSCName() {
		findNameToPostNum($pdo, $name);
		return $this->$post_num;
    }
    
	public function getIdToName() {
		findIdToName($pdo, $id);
		return $this->$name; 
	}
	public function getIdToScreenName() {
		findIdToScreenName($pdo, $id);
		return $this->$screen_name;
	}
	public function getNameToPostNum() {
		findNameToPostNum($pdo, $name);
		return $this->$post_num;
    }
}

class UserStatus {
    private $id;
    private $name;
    private $mailadd;
    private $scname;

    //ユーザの投稿数の合計を返す
    public function getPostNum($pdo, $name) {
        $stmt = $pdo->prepare("select count(*) from dream inner join account on dream.user_id = account.id where name = ? and delete_flag is null order by dream.id desc");
        $stmt->execute( array($name) );
        $post_num = $stmt->fetch();
        return $post_num['count(*)'];
    }
    function getFollowNum($pdo, $name) {
        $stmt = $pdo->prepare("select count(follow_user_id=? or null) from follow");
        $stmt->execute( array( $this->id ) );
        $follownum = $stmt->fetchColumn();
        return $follownum;  
    }
    function getFollowerNum($pdo, $name) {
        $stmt = $pdo->prepare("select count(followed_user_id=? or null) from follow");
        $stmt->execute( array( $this->id ) );
        $follownum = $stmt->fetchColumn();
        return $follownum;  
    }

    public function __construct($pdo, $name) {
        $stmt = $pdo->prepare("select * from account where name = ?");
        $stmt->execute( array($name) );
        $result = $stmt->fetch();

        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->mailadd = $result['mailadd'];
        $this->scname = $result['screen_name'];
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getMail() { return $this->mailadd; }
    public function getSCName() { return $this->scname; }
}

//テキストの表示
function showText($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

//UserIDからスクリーンネームを取得する checked
function get_id_to_name($pdo, $id) {
	$stmt = $pdo->prepare("select * from account where id = ?");
	$stmt->execute( array($id) );
	$result = $stmt->fetch();
	return $result['name'];
}
//UserIDからスクリーンネームを取得する checked
function get_id_to_sname($pdo, $id) {
	$stmt = $pdo->prepare("select * from account where id = ?");
	$stmt->execute( array($id) );
	$result = $stmt->fetch();
	return $result['screen_name'];
}
function get_name_to_id($pdo, $name) {
	$stmt = $pdo->prepare("select * from account where name = ?");
	$stmt->execute( array($name) );
	$result = $stmt->fetch();
	return $result['id'];
}

function get_user_sname() {
	return $_SESSION['userdata']['name'];
}
function get_user_ssname() {
	return $_SESSION['userdata']['sc_name'];
}
//投稿のIDから投稿者のIDを取得する
function get_postid_to_name($pdo, $id) {
	$stmt = $pdo->prepare("select * from dream where id = ?");
	$stmt->execute( array($id) );
	$result = $stmt->fetch();
	return $result['user_id'];
}
//投稿のIDから投稿内容を取得する
function get_postid_to_body($pdo, $id) {
	$stmt = $pdo->prepare("select * from dream where id = ?");
	$stmt->execute( array($id) );
	$result = $stmt->fetch();
	return $result['body'];
}
//非ログイン状態ではログインフォームへ転送する
function forward_login() {
	if ( empty($_SESSION['userdata']) ) {
		header("Location: index.php");
	}
}

function forward_main() {
	if ( !empty($_SESSION['userdata']) ) { // <-ログイン時のみ実行される
		header("Location: index.php");
	}
}

function get_name_to_mail($pdo, $name) {
	$stmt = $pdo->prepare("select mailadd from account where id = ?");
	$stmt->execute( array( get_name_to_id($pdo, $name) ) );
	$mailladd = $stmt->fetchColumn();
  	return $mailladd;
}

function get_name_to_pass($pdo, $name) {
	$stmt = $pdo->prepare("select password from account where id = ?");
	$stmt->execute( array( get_name_to_id($pdo, $name) ) );
	$mailladd = $stmt->fetchColumn();
  	return $mailladd;
}

function get_isliked_thepost($pdo, $like_user_id, $liked_post_id) {
	$stmt = $pdo->prepare("select count(*) from likes where like_user_id = ? and liked_post_id = ?");
	$stmt->execute( array($like_user_id, $liked_post_id) );
	$islike = $stmt->fetchColumn();
	return $islike;
}

//フォローしているか確認
function get_isfollow($pdo, $me, $other) {
	$stmt = $pdo->prepare("select count(*) from follow where follow_user_id = ? and followed_user_id = ?");
	$stmt->execute( array($me, $other) );
	$isfollow = $stmt->fetchColumn();
	return $isfollow;
}

//ある投稿のいいね数を取得する
function get_like_count_of_the_post($pdo, $postid) {
	$stmt = $pdo->prepare("select count(liked_post_id=? or null) from likes"); 
	$stmt->execute( array( $postid ) ); 
	$likecount = $stmt->fetchColumn(); 
	return $likecount;
}

function session_update($pdo) {
	if ( !empty($_SESSION['userdata']) ) {
		$stmt = $pdo->prepare("select * from account where id = ?");
		$stmt->execute( array($_SESSION['userdata']['id']) );
		$row = $stmt->fetch();
		$_SESSION['userdata']= array(
			'id' => $row['id'],
			'name' => $row['name'],
			'mailadd' => $row['mailadd'],
			'sc_name' => $row['screen_name']
		);
	}
}

//画像処理系
function imageconverter($orig_path, $out_path, $mimetype) {
	if ( /* jpegのときはpngに変換する */$mimetype == 'image/jpeg' ) {
		$tmp = imagecreatefromjpeg($orig_path);
		imagepng($tmp, $out_path); 
		imagedestroy($tmp);
	}
	if ( /* pngのときはjpegに変換する */$mimetype == 'image/png' ) {
		$tmp = imagecreatefrompng($orig_path);
		imagejpeg($tmp, $out_path); 
		imagedestroy($tmp);
	}
	return $out_path;
}

function trimfillimage($iconimage, $widthlength, $heightlength) {
	//縦横､正方形画像を入力すると中央に配置された指定した解像度の正方形画像を返す｡
	list($width, $height) = getimagesize($iconimage);
	$tmp = imagecreatetruecolor($widthlength, $heightlength);
	if ( /* 要求された画像サイズが横長のとき */$widthlength > $heightlength ) {
		$aspect = $widthlength/$heightlength;
		if ( /* 横長画像のとき */$width > $height ) {
			$aspect_target = $width/$height;
			if ( /* トリミング対象画像が要求される画像の比より細長いとき */$aspect_target > $aspect ) {
				//見切れるのは左右である
				imagecopyresampled(
					$tmp, imagecreatefrompng($iconimage),
					0, 0, /*コピー先座標*/
					//($width/2)-($height*$aspect/2), 0, /*コピー元座標($width/2)-($height/$aspect*2)*/
					($width-$height*$aspect)/2, 0, /*コピー元座標($width/2)-($height/$aspect*2)*/
					$widthlength, $heightlength, /*コピー先幅*/
					$height*$aspect, $height /*コピー元幅*/
				);
			} /* トリミング対象画像が要求される画像の比より太いとき */else {
				//見切れるのは上下である
				imagecopyresampled(
					$tmp, imagecreatefrompng($iconimage),
					0, 0, /*コピー先座標*/
					//($width/2)-($height*$aspect/2), 0, /*コピー元座標($width/2)-($height/$aspect*2)*/
					0, ($height-$width/$aspect)/2, /*コピー元座標($width/2)-($height/$aspect*2)*/
					$widthlength, $heightlength, /*コピー先幅*/
					$width, $width/$aspect /*コピー元幅*/
				);
			}
		} else if ( /* 縦長画像のとき */$height > $width ) {
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				0, ($height/2)-(($width/$aspect)/2), /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width, $width/$aspect /*コピー元幅*/
			);
		} else if ( /* 正方形画像のとき */$width == $height ) { 
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				0, ($height/2)-(($width/$aspect)/2), /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width, $width/$aspect /*コピー元幅*/
			);
		}
	}
	if ( /* 要求された画像サイズが縦長のとき */$heightlength > $widthlength ) {
		$aspect = $heightlength/$widthlength;
		if ( /* 横長画像のとき */$width > $height ) {
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				($width/2)-(($height/$aspect)/2), 0, /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$height/$aspect, $height /*コピー元幅*/
			);
		} else if ( /* 縦長画像のとき */$height > $width ) {
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				0, ($height/2)-(($width*$aspect)/2), /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width, $height/$aspect /*コピー元幅*/
			);
		} else if ( /* 正方形画像のとき */$width == $height ) { 
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				($width/2)-(($width/$aspect)/2), 0, /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width/$aspect, $height /*コピー元幅*/
			);
		}
	}
	if ( /* 要求された画像サイズが正方形のとき */$widthlength == $heightlength ) {
		if ( /* 横長画像のとき */$width > $height ) {
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				($width/2)-($height/2), 0, /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$height, $height /*コピー元幅*/
			);
		} else if ( /* 縦長画像のとき */$height > $width ) {
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				0, ($height/2)-($width/2), /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width, $width /*コピー元幅*/
			);
		} else if ( /* 正方形画像のとき */$width == $height ) { 
			imagecopyresampled(
				$tmp, imagecreatefrompng($iconimage),
				0, 0, /*コピー先座標*/
				0, 0, /*コピー元座標*/
				$widthlength, $heightlength, /*コピー先幅*/
				$width, $height /*コピー元幅*/
			);
		}
	}
	
	imagepng($tmp, $iconimage,9);
	imagedestroy($tmp);
	return $iconimage;
}

function getImageRotateDegFromExif($orig_path) {
	//画像を正しい回転にするための角度を返す
	$exif_data = exif_read_data($orig_path);
	if ( $exif_data != null ) {
		$orientation = $exif_data['Orientation'];
		$degrees = 0;
		switch($orientation) {
			case 1:		//回転なし（↑）
				return;
			case 8:		//右に90度（→）
				$degrees = 90;
				break;
			case 3:		//180度回転（↓）
				$degrees = 180;
				break;
			case 6:		//右に270度回転（←）
				$degrees = 270;
				break;
			case 2:		//反転　（↑）
				$mode = IMG_FLIP_HORIZONTAL;
				break;
			case 7:		//反転して右90度（→）
				$degrees = 90;
				$mode = IMG_FLIP_HORIZONTAL;
				break;
			case 4:		//反転して180度なんだけど縦反転と同じ（↓）
				$mode = IMG_FLIP_VERTICAL;
				break;
			case 5:		//反転して270度（←）
				$degrees = 270;
				$mode = IMG_FLIP_HORIZONTAL;
				break;
		}
		return $degrees;
	} else {
		return;
	}
}

function rotateImage($orig_path, $out_path, $deg) {
	//画像を回転する
	if ($deg > 0) {
		$tmp = imagecreatefrompng($orig_path);
		$tmp = imagerotate($tmp, $deg, 0);
		imagepng($tmp, $out_path);
		return $out_path;
	}
}
?>