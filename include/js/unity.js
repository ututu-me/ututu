$(function() {
    if ( $('body').width() >= 769 && $('body').width() <= 1023 ) {
        $('.main-column').removeClass('is-9');
        $('.main-column').addClass('is-12');
    } else {
        $('.main-column').removeClass('is-12');
        $('.main-column').addClass('is-9');
        $('#nav-input').prop('checked', false);
    }
    $(window).resize(function() {
        if ( $('body').width() >= 769 && $('body').width() <= 1023 ) {
            $('.main-column').removeClass('is-9');
            $('.main-column').addClass('is-12');
        } else {
            $('.main-column').removeClass('is-12');
            $('.main-column').addClass('is-9');
            $('#nav-input').prop('checked', false);
        }
    });
});

$(document).on('click', '.overlay', function(){
    var overlay;
    var menu;
    overlay = $(this);
    menu = overlay.prev();

    overlay.hide();
    menu.hide();
});

window.onpageshow = function(event) {
	if (event.persisted) {
        window.location.reload();
	}
};

$(function(){
    //文字数カウント
    $('.post-title').keyup(function(){
        var count = $(this).val().length;
        if ( count > 25 ) {
            $('.title-c').removeClass('has-text-black-ter');
            $('.title-c').addClass('has-text-danger');
        } else {
            $('.title-c').removeClass('has-text-danger');
            $('.title-c').addClass('has-text-black-ter');
        }
        $('.title-c').text(count);
    });
    $('.post-body').keyup(function(){
        var count = $(this).val().length;
        if ( count > 400 ) {
            $('.body-c').removeClass('has-text-black-ter');
            $('.body-c').addClass('has-text-danger');
        } else {
            $('.body-c').removeClass('has-text-danger');
            $('.body-c').addClass('has-text-black-ter');
        }
        $('.body-c').text(count);
    });
});