<?php $likecount = get_like_count_of_the_post($pdo, $loop['dreamid']); ?>
<article class="media article article-content" <?php if ( $_GET['page'] == 'likes' ) { echo "data-id=".$loop['likeid']; } ?>>
    <div class="media-left">
        <div class="image is-48x48">
            <a href="userpage.php?name=<?php echo $loop['name'] ?>">
                <img src="https://ututu.me/image/icon/<?php echo $loop['name'] ?>.png?<?php echo date('dHis') ?>" alt="usericon" style="border-radius: 50%">
            </a>
        </div>
    </div>
    <div class="media-content">
        <div class="content">
            <a class="is-size-6" href="userpage.php?name=<?php echo $loop['name'] ?>"><span><?php echo showText($loop['screen_name']) ?></span><span>@<?php echo $loop['name']?></span></a>
            <p class="is-size-5"><a href="dream.php?id=<?php echo $loop['dreamid'] ?>" class="has-text-grey-darker" style="word-break: break-all;"><?php echo showText($loop['title']) ?></a></p>
            <p style="word-break: break-all;"><?php echo nl2br( showText($loop['body']) ) ?></p>
        </div>
        <div class="level is-mobile">
                <div class="level-left">
                    <span class="level-item is-size-7"><time><?php echo $loop['created_at']?></time></span>
                </div>
                <div class="level-right">
                    <?php if ( get_isliked_thepost($pdo, $_SESSION['userdata']['id'], $loop['dreamid']) == 0 ) : ?>
                    <span class="level-item like-button is-size-5" data-postnum="<?php echo $loop['dreamid'] ?>"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;"><?php echo $likecount ?></p><span class="icon"><i class='far fa-heart'></i></span></span>
                    <?php else : ?>
                    <span class="level-item like-button is-size-5" data-postnum="<?php echo $loop['dreamid'] ?>"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;"><?php echo $likecount ?></p><span class="icon"><i class='fas fa-heart'></i></span></span>
                    <?php endif; ?>
                    <span class="level-item article-menu-button has-text-link  is-size-5" style="cursor: pointer;"><span class="icon"><i class='fas fa-ellipsis-v'></i></span></span>
                    <menu class="box" style="padding: 0.5rem;">
                        <div class="menu">
                            <ul class="menu-list">
                                <li><a href="dream.php?id=<?php echo $loop['dreamid'] ?>"><span class="icon"><i class="fas fa-info-circle"></i></span><span class="content" data-postnum="<?php echo $loop['dreamid'] ?>">詳細</span></a></li>
                                <?php /* 投稿者が他者 */ if ( $loop['name'] != $_SESSION['userdata']['name'] ) : ?>
                                <?php /* 投稿者が自分 */ else : ?>
                                <li><a class="delete-article" data-postnum="<?php echo $loop['dreamid'] ?>"><span class="icon"><i class="fas fa-trash"></i></span><span class="content">削除</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </menu>
                    <div class="overlay"></div>
                </div>
        </div>
    </div>
</article>