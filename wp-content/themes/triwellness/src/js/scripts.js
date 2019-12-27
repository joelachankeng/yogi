var $ = window.jQuery;
$(function() {
    
    $( document ).ready(function() {
        $('.notification-bar').slick({
            prevArrow: '<i class="fas fa-angle-left"></i>',
            nextArrow: '<i class="fas fa-angle-right"></i>',
        }); // turn notification bar to slider

    });
});