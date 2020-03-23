//フォローボタン
$(document).on('click', '.follow-button', function(){
    var button;
    var name;
    var button_text;
    button = $(this);
    name = button.data('name');
    $.ajax({
        url:'https://ututu.me/ajax/follow.php',
        type: 'post',
        dataType: 'json',
        data: {
            name: name,
        }
    }).done( function(data){
        button.toggleClass('is-primary');
        if ( data.flag == 'del' ) {
            button_text = "フォロー";
        }
        if ( data.flag == 'followed' ) {
            button_text = "フォロー中";
        }
        button.find('.is-text-4').text(button_text);
    }).fail(function(data){

    });
});
