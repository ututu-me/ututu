<div class="notif media" data-date="<?php echo $loop['created_at'] ?>">
    <?php if ( $loop['type'] == 'like' ) : ?>
        <div class="media-left">
            <span class="icon" style="color: lightcoral;"><i class="fas fa-heart"></i></span>
        </div>
        <div class="media-content">
            <div class="content">
                <span><a href="userpage.php?name=<?php echo showText($loop['liked_user_name']) ?>"><?php echo showText($loop['liked_user_scname']) ?></a>さんが<a href="dream.php?id=<?php echo $loop['liked_post_id'] ?>">あなたの投稿</a>にいいねしました｡</span>
                <span class="has-text-grey-light" style="word-break: break-all;"><?php echo showText($loop['body']) ?></span>
            </div>
        </div>
    <?php elseif ( $loop['type'] == 'follow' ) : ?>
        <div class="media-left">
            <span class="icon"><i class="fas fa-user-plus"></i></span>
        </div>
        <div class="media-content">
            <div class="content">
                <span><a href="userpage.php?name=<?php echo showText($loop['liked_user_name']) ?>"><?php echo showText($loop['liked_user_scname']) ?></a>さんがあなたをフォローしました｡</span>
            </div>
        </div>
    <?php endif; ?>
</div>