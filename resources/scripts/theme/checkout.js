import $ from 'jquery';

'use strict';

$(function() {
    $(document.body).on('change', 'input[name="payment_method"]', function() {
        $('body').trigger('update_checkout');
    });

});

$(function() {
    $(document).ready(function() {
        var div = $("#ame-digital-qrcode-thankyou");
        if (div.length) {
            $('html,body').animate({
                scrollTop: $("#ame-digital-qrcode-thankyou").offset().top
            }, 2000);
        }
    });
});

$(document).ready(function($) {
    var deepLink = $('#wad_deeplink').val();

    $(".wad-btn-primary").click(function() {
        window.location.replace(deepLink);
    });
});