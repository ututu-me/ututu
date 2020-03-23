<?php
//通知の取得
$sql = "select likes.id, likes.created_at, likes.like_user_id, likes.liked_post_id, likes.notif, account.name as liked_user_name, account.screen_name as liked_user_scname, dream.body, dream.user_id, 'like' as type from likes inner join account on likes.like_user_id = account.id inner join dream on likes.liked_post_id = dream.id where dream.user_id = ? union select follow.id, follow.created_at, follow.follow_user_id, follow. followed_user_id, followed_user_id as notif,  account.name, account.screen_name, NULL as body, NULL as user_id, 'follow' as type from follow inner join account on follow.follow_user_id = account.id where followed_user_id = ? order by created_at desc limit 5";
$stmt = $pdo->prepare($sql);
$stmt->execute( array($_SESSION['userdata']['id'], $_SESSION['userdata']['id']) );
$notif = $stmt->fetchAll();
?>
<header class="navbar" role="navigation" aria-label="main navigation" style="box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1), 0 0 0 1px rgba(10, 10, 10, 0.1); z-index: 100;">
    <div class="navbar-brand">
        <?php if ( isset($_SESSION['userdata']) ) : ?>
        <input id="nav-input" type="checkbox" style="display: none;">
        <label class="nav-open navbar-item is-hidden-desktop" for="nav-input"><span class="" style="font-size: 1.5rem;"><img src="https://ututu.me/image/icon/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%"></span></label>
        <label class="nav-unshown" id="nav-close" for="nav-input"></label>
        <div id="nav-content">
            <section class="hero" style="background-image: url('https://ututu.me/image/header/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>'); background-size: cover;">
                <div class="hero-body">
                    <div class="media">
                        <div class="media-left">
                            <span class="icon" style="font-size: 1.5rem;"><img src="https://ututu.me/image/icon/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%"></span>
                        </div>
                        <div class="media-content">
                            <div class="menu">
                                <ul class="menu-list">
                                    <li><p class="title has-background-black has-text-white is-inline-block" style="font-size: 1.3rem;"><?php echo showText($_SESSION['userdata']['sc_name']) ?></p></li>
                                    <li><p class="subtitle has-background-black has-text-white is-inline-block" style="font-size: 1.0rem;">@<?php echo showText($_SESSION['userdata']['name']) ?></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="">
                <div class="menu">
                    <p class="menu-label">Menu</p>
                    <ul class="menu-list">
                        <li>
                            <a href="userpage.php?name=<?php echo showText($_SESSION['userdata']['name']) ?>">
                                <div class="level is-mobile" style="padding: 0 1rem;">
                                    <div  class="level-left">
                                        <span class="icon" style="font-size: 1.5rem; padding-right: 0.75rem;"><i class="fas fa-user-circle"></i></span>
                                        <span>マイページ</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="latest.php">
                                <div class="level is-mobile" style="padding: 0 1rem;">
                                    <div class="level-left">
                                        <span class="icon" style="font-size: 1.2rem; padding-right: 0.75rem;"><i class="fas fa-newspaper"></i></span>
                                        <span>新着投稿</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="notification.php">
                                <div class="level is-mobile" style="padding: 0 1rem;">
                                    <div class="level-left">
                                        <span class="icon" style="font-size: 1.35rem; padding-right: 0.75rem;"><i class="fas fa-bell"></i></span>
                                        <span>通知</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php">
                                <div class="level is-mobile" style="padding: 0 1rem;">
                                    <div class="level-left">
                                        <span class="icon" style="font-size: 1.23rem; padding-right: 0.75rem;"><i class="fas fa-wrench"></i></span>
                                        <span>設定</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <p class="menu-label">サポート</p>
                    <ul class="menu-list">
                        
                        <li>
                            <a href="logout.php">
                                <div class="level is-mobile" style="padding: 0 1rem;">
                                    <div class="level-left">
                                        <span class="icon" style="font-size: 1.5rem; padding-right: 0.75rem;"><i class="fas fa-sign-out-alt"></i></span>
                                        <span>ログアウト</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
        <?php endif; ?>
        <div class="navbar-item"><a href="index.php"><img src="image/ututu_logo.svg" alt=""></a></div>
        <?php if ( isset($_SESSION['userdata']) ) : ?>
            <div class="navbar-item is-hidden-desktop" style="position: absolute; right: 0;">
                <a href="dreaming.php" class="button">
                    <span style="padding-right: 0.75rem;"><i class="fas fa-pen-nib"></i></span>
                    <p>夢を残す</p>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="navbar-start">
        <?php if ( isset($_SESSION['userdata']) ) : ?>
        <div class="navbar-item is-hidden-touch">
            <div class="user-open-button button">
                <div class="level">
                    <span class="level-item" style="font-size: 1.5rem; padding-right:0.7rem;"><img src="https://ututu.me/image/icon/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%"></span>
                    <h3 class="level-item"><?php echo showText($_SESSION['userdata']['sc_name']) ?></h3>
                </div>
            </div>
            <menu class="box">
                    <div id="user-content" style="box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1), 0 0 0 1px rgba(10, 10, 10, 0.1);">
                        <section class="hero" style="background-image: url('https://ututu.me/image/header/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>'); background-size: cover;">
                            <div class="hero-body">
                                <div class="media">
                                    <div class="media-left">
                                        <span class="icon" style="font-size: 1.5rem;"><img src="https://ututu.me/image/icon/<?php echo showText($_SESSION['userdata']['name']) ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%"></span>
                                    </div>
                                    <div class="media-content">
                                        <div class="menu">
                                            <ul class="menu-list">
                                                <li><p class="title has-background-black has-text-white is-inline-block" style="font-size: 1.3rem;"><?php echo showText($_SESSION['userdata']['sc_name']) ?></p></li>
                                                <li><p class="subtitle has-background-black has-text-white is-inline-block" style="font-size: 1.0rem;">@<?php echo showText($_SESSION['userdata']['name']) ?></p></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="">
                            <div class="menu">
                                <p class="menu-label">Menu</p>
                                <ul class="menu-list">
                                    <li>
                                        <a href="userpage.php?name=<?php echo showText($_SESSION['userdata']['name']) ?>">
                                            <div class="level is-mobile" style="padding: 0 1rem;">
                                                <div class="level-left">
                                                    <span class="icon" style="font-size: 1.5rem; padding-right: 0.75rem;"><i class="fas fa-user-circle"></i></span>
                                                    <span>マイページ</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="logout.php">
                                            <div class="level is-mobile" style="padding: 0 1rem;">
                                                <div class="level-left">
                                                    <span class="icon" style="font-size: 1.5rem; padding-right: 0.75rem;"><i class="fas fa-sign-out-alt"></i></span>
                                                    <span>ログアウト</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </section>
                    </div>
            </menu>
            <div class="overlay"></div>
        </div>
        <?php endif; ?>
    </div>
    <?php if ( isset($_SESSION['userdata']) ) : ?>
    <div class="navbar-end">
        <div class="navbar-item is-hidden-touch">
            <div class="level is-mobile">
                <div class="level-right">
                    <!-- 検索 -->
                    <span class="level-item search-button has-text-link" style="font-size: 1.2rem; cursor: pointer;"><span class="icon"><i class='fas fa-search'></i></span></span>
                    <menu class="box" style="0.5rem;">
                        <div class="content">
                            <form action="search.php" method="get" style="margin: 0" id="searchbox">
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input class="input" type="text" placeholder="検索" name="search" value="<?php echo showText($search_word) ?>" style="width: 10rem;">
                                    </div>
                                    <div class="control">
                                        <input type="submit" id="search" name="from" value="header" style="display: none;">
                                        <label class="button is-info" for="search"><span class="icon"><i class="fas fa-search"></i></span></label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </menu>
                    <div class="overlay"></div>
                    <!-- 通知 -->
                    <span class="level-item notif-button has-text-link" style="font-size: 1.2rem; cursor: pointer;"><span class="icon"><i class='fas fa-bell'></i></span></span>
                    <menu class="box notif-list has-background-white-bis" style="width: 20rem;">
                        <div class="notif-header media">
                            <div class="media-left">
                                <span class="icon is-size-5 has-text-link"><i class="fas fa-bell"></i></span>
                            </div>
                            <div class="media-content">
                                <div class="content">
                                    <span class="is-size-5">最近の通知</span>
                                    <a href="notification.php">もっと見る</a>
                                </div>
                            </div>
                        </div>
                        <?php foreach ( $notif as $loop ) : ?>
                            <?php include("notif.php");?>
                        <?php endforeach; ?>
                    </menu>
                    <div class="overlay"></div>
                    <span class="level-item article-menu-button" style="font-size: 1.2rem; cursor: pointer;"><a href="settings.php"><span class="icon"><i class='fas fa-wrench'></i></span></a></span>
                </div>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="navbar-end">
        <div class="navbar-item is-hidden-touch">
            <a href="login.php" class="button">ログイン</a>
        </div>
        <div class="navbar-item is-hidden-touch">
            <a href="signon.php" class="button is-link">新規登録する</a>
        </div>
    </div>
    <?php endif; ?>
</header>