//いいねボタン
$(document).on('click', '.like-button', function(){
    var button;
    var postnum;
    button = $(this);
    postnum = button.data('postnum');
    $.ajax({
        url:'https://ututu.me/ajax/like.php',
        type: 'post',
        dataType: 'json',
        data: {
            postnum: postnum,
        }
    }).done( function(data){
        button.find('i').toggleClass('far fas');
        button.find('.likecount').text(data.likecount);
    }).fail(function(data){

    });
});
//削除ボタン
$(document).on('click', '.delete-article', function(){
    var button;
    var postnum;
    var post;
    var posticon;
    button = $(this);
    postnum = button.data('postnum');
    post = button.parent().parent().parent().parent().parent().parent().parent(); //.media-contentを指す
    posticon = post.prev();
    $.ajax({
        url:'https://ututu.me/ajax/delete.php',
        type: 'post',
        dataType: 'json',
        data: {
            postnum: postnum,
        }
    }).done( function(data){
        if ( data.flag == 1 ) {
            post.text(data.delmes);
            posticon.text("");
        }
    }).fail(function(data){

    });
});

$(document).on("click", '.new', function(){
    var top;
    var html;
    var likebutton;
    var repodel;
    var postnum;
    top = $('.head').next();
    postnum = top.find('.like-button').data('postnum');
    $(this).remove();
    $.ajax({
        url:'https://ututu.me/ajax/check_latest_post.php',
        type: 'post',
        dataType: 'json',
        data: {
            postnum: postnum,
            page: location.pathname
        }
    }).done( function(data){
        if ( data.new > 0 ) {
            for ( let i in data.newpost ) {
                if ( data.newpost[i].islike == 0 ) {
                    likebutton = `<span class="level-item like-button is-size-5" data-postnum="${data.newpost[i].dreamid}"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;">${data.newpost[i].likecount}</p><span class="icon"><i class='far fa-heart'></i></span></span>`;
                } else {
                    likebutton = `<span class="level-item like-button is-size-5" data-postnum="${data.newpost[i].dreamid}"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;">${data.newpost[i].likecount}</p><span class="icon"><i class='fas fa-heart'></i></span></span>`;
                }
                if ( data.newpost[i].isme == 0 ) {
                    repodel = `<li><a><span class="icon"><i class="fas fa-flag"></i></span><span class="content">報告</span></a></li>`;
                } else {
                    repodel = `<li><a class="delete-article" data-postnum="${data.newpost[i].dreamid}"><span class="icon"><i class="fas fa-trash"></i></span><span class="content">削除</span></a></li>`;
                }
                html = `
                <article class="media article">
                    <div class="media-left">
                        <div class="image is-48x48">
                            <a href="userpage.php?name=${data.newpost[i].name}">
                                <img src="https://ututu.me/image/icon/${data.newpost[i].name}.png" alt="usericon" style="border-radius: 50%">
                            </a>
                        </div>
                    </div>
                    <div class="media-content">
                        <div class="content">
                            <a class="is-size-6" href="userpage.php?name=${data.newpost[i].name}"><span>${data.newpost[i].screen_name}</span><span>@${data.newpost[i].name}</span></a>
                            <p class="is-size-5"><a href="dream.php?id=${data.newpost[i].dreamid}" class="has-text-grey-darker">${data.newpost[i].title}</a></p>
                            <p>${data.newpost[i].body}</p>
                        </div>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <span class="level-item is-size-7"><time>${data.newpost[i].created_at}</time></span>
                            </div>
                            <div class="level-right">
                                ${likebutton}
                                <span class="level-item article-menu-button has-text-link  is-size-5" style="cursor: pointer;"><span class="icon"><i class='fas fa-ellipsis-v'></i></span></span>
                                <menu class="box" style="padding: 0.5rem;">
                                <div class="menu">
                                        <ul class="menu-list">
                                            <li><a href="dream.php?id=${data.newpost[i].dreamid}"><span class="icon"><i class="fas fa-info-circle"></i></span><span class="content" data-postnum="${data.newpost[i].dreamid}">詳細</span></a></li>
                                            ${repodel}
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </menu>
                                <div class="overlay"></div>
                            </div>
                        </div>
                    </div>
                </article>
                `;
                $('.head').after(html);
            }
        }
    }).fail(function(data){

    });
});

$(document).on("click", '.old', function(){
    var bottom;
    var html;
    var likebutton;
    var repodel;
    var postnum;
    var user;
    var tab = 'default';
    var likeid = '';
    bottom = $('.tail').prev();
    user = $('.follow-button').data('name');
    postnum = bottom.find('.like-button').data('postnum');
    likeid = bottom.data('id');
    $(this).remove();

    if ( location.pathname == '/ututu/userpage.php' ) {
        tab = $('.tabs').find('.is-active').data('page');
    }
    if ( tab == 'likes' ) {
        postnum = likeid;
    }
    $.ajax({
        url:'https://ututu.me/ajax/check_old_post.php',
        type: 'post',
        dataType: 'json',
        data: {
            user: user,
            postnum: postnum,
            page: location.pathname,
            tab: tab
        }
    }).done( function(data){
        if ( data.new > 0 ) {
            for ( let i in data.newpost ) {
                if ( data.newpost[i].islike == 0 ) {
                    likebutton = `<span class="level-item like-button is-size-5" data-postnum="${data.newpost[i].dreamid}"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;">${data.newpost[i].likecount}</p><span class="icon"><i class='far fa-heart'></i></span></span>`;
                } else {
                    likebutton = `<span class="level-item like-button is-size-5" data-postnum="${data.newpost[i].dreamid}"><p class="likecount" style="color: gray; cursor: pointer; font-size: 1rem; margin-right: 0.67rem;">${data.newpost[i].likecount}</p><span class="icon"><i class='fas fa-heart'></i></span></span>`;
                }
                if ( data.newpost[i].isme == 0 ) {
                    repodel = `<li><a><span class="icon"><i class="fas fa-flag"></i></span><span class="content">報告</span></a></li>`;
                } else {
                    repodel = `<li><a class="delete-article" data-postnum="${data.newpost[i].dreamid}"><span class="icon"><i class="fas fa-trash"></i></span><span class="content">削除</span></a></li>`;
                }
                html = `
                <article class="media article">
                    <div class="media-left">
                        <div class="image is-48x48">
                            <a href="userpage.php?name=${data.newpost[i].name}">
                                <img src="https://ututu.me/image/icon/${data.newpost[i].name}.png" alt="usericon" style="border-radius: 50%">
                            </a>
                        </div>
                    </div>
                    <div class="media-content">
                        <div class="content">
                            <a class="is-size-6" href="userpage.php?name=${data.newpost[i].name}"><span>${data.newpost[i].screen_name}</span><span>@${data.newpost[i].name}</span></a>
                            <p class="is-size-5"><a href="dream.php?id=${data.newpost[i].dreamid}" class="has-text-grey-darker">${data.newpost[i].title}</a></p>
                            <p>${data.newpost[i].body}</p>
                        </div>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <span class="level-item is-size-7"><time>${data.newpost[i].created_at}</time></span>
                            </div>
                            <div class="level-right">
                                ${likebutton}
                                <span class="level-item article-menu-button has-text-link  is-size-5" style="cursor: pointer;"><span class="icon"><i class='fas fa-ellipsis-v'></i></span></span>
                                <menu class="box" style="padding: 0.5rem;">
                                <div class="menu">
                                        <ul class="menu-list">
                                            <li><a href="dream.php?id=${data.newpost[i].dreamid}"><span class="icon"><i class="fas fa-info-circle"></i></span><span class="content" data-postnum="${data.newpost[i].dreamid}">詳細</span></a></li>
                                            ${repodel}
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </menu>
                                <div class="overlay"></div>
                            </div>
                        </div>
                    </div>
                </article>
                `;
                bottom.after(html);
            }
            html = `
                <div class="box button old" style="margin: 0; margin-top: 1.5rem;">
                    <div class="content">
                        <span></span>
                    </div>
                </div>
                `;
            $('.tail').after(html);
            $('.old').find('span').text("さらに読み込む");
        }
    }).fail(function(data){

    });
});

//新着投稿自動確認
setInterval( function() {
    var top;
    var html;
    var likebutton;
    var repodel;
    var postnum;
    top = $('.head').next();
    html = `
    <div class="box button new">
        <div class="content">
            <span></span>
        </div>
    </div>
    `
    postnum = top.find('.like-button').data('postnum');
    $.ajax({
        url:'https://ututu.me/ajax/check_latest_post.php',
        type: 'post',
        dataType: 'json',
        data: {
            postnum: postnum,
            page: location.pathname
        }
    }).done( function(data){
        if ( data.new > 0 ) {
            if ( $('.new').length <= 0 ) {
                $('.head').before(html);
                $('.new').find('span').text(data.new + "件の新着投稿があります");
            } else {
                $('.new').find('span').text(data.new + "件の新着投稿があります");

            }
        }
    }).fail(function(data){

    });
    }, 5000
);

//無限スクロール
$(function() {
    var bottom;
    var postnum;
    var articlecount = $('.article-content').length;
    var html;
    bottom = $('.tail').prev();
    postnum = bottom.find('.like-button').data('postnum');
    html = `
    <div class="box button old" style="margin: 0; margin-top: 1.5rem;">
        <div class="content">
            <span></span>
        </div>
    </div>
    `
    if ( articlecount >= 50 ) {
        $('.tail').after(html);
        $('.old').find('span').text("さらに読み込む");
    }
});