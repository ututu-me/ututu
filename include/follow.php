<?php
if ( $_GET['page'] == 'following' ) {
    if ( get_isfollow($pdo, $_SESSION['userdata']['id'], $loop['followed_user_id']) == 1 ) {
        $button_text = 'フォロー中';
        $isfollow = 1;
    } else {
        $button_text = 'フォロー';
        $isfollow = 0;
    }
}
if ( $_GET['page'] == 'followers' ) {
    if ( get_isfollow($pdo, $_SESSION['userdata']['id'], $loop['follow_user_id'] ) == 1 ) {
        $button_text = 'フォロー中';
        $isfollow = 1;
    } else {
        $button_text = 'フォロー';
        $isfollow = 0;
    }
}
?>
<article class="media article">
    <div class="media-left">
        <div class="image is-48x48">
            <img src="https://ututu.me/image/icon/<?php echo $_SESSION['userdata']['name'] ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%">
        </div>
    </div>
    <div class="media-content">
        <div class="content">
            <a class="is-size-6" href="userpage.php?name=<?php echo $loop['name'] ?>"><span><?php echo showText($loop['screen_name']) ?></span><span>@<?php echo $loop['name']?></span></a>
            <p class=""><?php echo showText($loop['bio']) ?></p>
        </div>
    </div>
    <div class="media-right">
        <?php if ( $_SESSION['userdata']['name'] != $loop['name'] ) : ?>
            <div class="follow-button button <?php if ( $isfollow == 1 ) echo 'is-primary' ?> "data-name="<?php echo $loop['name'] ?>">
                <span class="icon" style="margin-right: 0.75rem;"><i class="fas fa-user-plus"></i></span>
                <span class="is-text-4"><?php echo $button_text ?></span>
            </div>
        <?php endif; ?>
    </div>
</article>