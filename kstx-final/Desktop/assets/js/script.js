$(document).ready(function() {

    // Left Bar Triggers
    $('.left-bar-open-trigger').on('click', function() {
        $('.left-bar').addClass('active');
        $('body').css('overflow-y', 'hidden');
    });

    $('.left-bar-close-trigger').on('click', function() {
        $('.left-bar').removeClass('active');
        $('body').css('overflow-y', 'scroll');
    });

    // Right Bar Triggers
    $('.right-bar-open-trigger').on('click', function() {
        $('.right-bar').addClass('active');
        $('body').css('overflow-y', 'hidden');
    });

    $('.right-bar-close-trigger').on('click', function() {
        $('.right-bar').removeClass('active');
        $('body').css('overflow-y', 'scroll');
    });

});