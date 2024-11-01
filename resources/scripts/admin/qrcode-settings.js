import $ from 'jquery';

'use strict';

$(function() {
    $('#woocommerce_ame-digital_access_token').attr('required', true);

    $("#woocommerce_ame-digital_qrcode_expiry").keypress(function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $(this).attr('placeholder', 'Somente números!');
            return false;
        }
    });
});