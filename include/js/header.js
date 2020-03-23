//各投稿のメニュー開閉
$(document).on('click', '.article-menu-button, .search-button, .notif-button, .user-open-button', function(){
    var button;
    var menu;
    var overlay;
    button = $(this);
    menu = button.next();
    overlay = menu.next();
    if ( overlay.css('display') != 'none' ) {
        menu.hide();
        overlay.hide();
    } else {
        menu.show();
        overlay.show();
    }
});

//通知リストを展開したら既読を送信
$(function() {
    var notiflist = $('.notif-button').next();
    notiflist.on('inview', function(event, isInView) {
        if (isInView) {
        }
    });
});

//通知パネルの新着自動確認
setInterval( function() {
    var top;
    var html;
    top = $('.notif-header').next().data('date');
    $.ajax({
        url:'https://ututu.me/ajax/notif_read.php',
        type: 'post',
        dataType: 'json',
        data: {
            top: top,
        }
    }).done( function(data){
        if ( data.new != 0 ) {
            for ( let i in data.notif ) {
                if ( data.notif[i].type == 'like' ) {
                    html = `
                    <div class="notif media" data-date="${data.notif[i].created_at}">
                        <div class="media-left">
                            <span class="icon" style="color: lightcoral;"><i class="fas fa-heart"></i></span>
                        </div>
                        <div class="media-content">
                            <div class="content">
                                <span><a href="userpage.php?name=${data.notif[i].liked_user_name}">${data.notif[i].liked_user_scname}</a>さんが<a href="dream.php?id=${data.notif[i].liked_post_id}">あなたの投稿</a>にいいねしました｡</span>
                                <span class="has-text-grey-light" style="word-break: break-all;">${data.notif[i].body}</span>
                            </div>
                        </div>
                    </div>
                    `;
                }
                if ( data.notif[i].type == 'follow' ) {
                    html = `
                    <div class="notif media" data-date="${data.notif[i].created_at}">
                        <div class="media-left">
                            <span class="icon"><i class="fas fa-user-plus"></i></span>
                        </div>
                        <div class="media-content">
                            <div class="content">
                                <span><a href="userpage.php?name=${data.notif[i].liked_user_name}">${data.notif[i].liked_user_scname}</a>さんがあなたをフォローしました｡</span>
                            </div>
                        </div>
                    </div>
                    `;
                }
                $('.notif-header').after(html);
                if ( $('.notif').length > 5 ) {
                    while ($('.notif').length > 5) {
                        $(".notif:last").remove();
                    }
                }
            }
        }
    }).fail(function(data){

    });
    }, 5000
);