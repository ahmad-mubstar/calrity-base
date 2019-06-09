var tcjq = jQuery.noConflict();
tcjq(document).ready(function() {
    tcjq('.top-cart .block-title').click(function(){
        tcjq(this).toggleClass('active');
        tcjq('#topCartContent').slideToggle(500).toggleClass('active');
    })
});
