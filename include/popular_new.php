<?php
$sql = "select dream.id as id, title, body, user_id, name, screen_name from dream inner join account on dream.user_id = account.id where delete_flag is null order by dream.id desc limit 3";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll();
?>
<div class="colomn is-hidden-touch">
    <div class="section">
        <div class="content image">
            <!-- <img src="https://via.placeholder.com/600"> -->
        </div>
        <nav class="navbar">
            <div class="navbar-brand" style="align-items: center;">
                <span class="navbar-item"></span>
            </div>
        </nav>
        <div class="box">
        <div class="notif-header media">
            <div class="media-left">
                <span class="icon is-size-5 has-text-link"><i class="far fa-newspaper"></i></span>
            </div>
            <div class="media-content">
                <div class="content">
                    <span class="is-size-5">新着投稿</span>
                </div>
            </div>
        </div>
        <?php foreach( $data as $loop ) : ?>
            <div class="media">
                <div class="media-content">
                    <h4 class="subtitle"><a href="dream.php?id=<?php echo $loop['id'] ?>"><?php echo showText($loop['title']) ?></h4>
                    <h5 title="subtitle"><a href="userpage.php?name=<?php echo $loop['name'] ?>"><?php echo showText($loop['screen_name']) ?>@<?php echo $loop['name'] ?></a></h5>
                </div>
            </div>
            
        <?php endforeach; ?>
        </div>
    </div>
</div>