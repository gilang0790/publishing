$(function () {
    checkWindowSize();
    $(window).resize(function () {
        checkWindowSize();
    });
});

function checkWindowSize() {
    var width = $(window).width();
    var height = $('#panel-login').css('height');
    if (width > 991) {
        $('#panel-banner').css('height', height);
    } else {
        $('#panel-banner').css('height', "auto");
    }
}