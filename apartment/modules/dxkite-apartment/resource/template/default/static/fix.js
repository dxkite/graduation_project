$(document).ready(function () {
    console.log('欢迎加入 ATD计算机协会 ATD安全团队 Q群：715061695 进群@管理，说：console');
    var h = $(window).height();
    $(window).resize(function () {
        if ($(window).height() < h) {
            $('.weui-footer').hide();
        } else {
            $('.weui-footer').show();
        }
    });
});