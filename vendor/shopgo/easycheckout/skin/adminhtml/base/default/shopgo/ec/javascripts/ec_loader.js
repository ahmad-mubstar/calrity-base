function showMessage(txt, type) {
    var html = '<ul class="messages"><li class="'+type+'-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
}