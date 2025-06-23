jQuery(document).ready(function($) {
    // Cart page scripts

    $('#check_pincode_btn').on('click', function() {
        const pincode = $('#pincode_input').val();
        const resultDiv = $('#delivery_estimate_result');
        const button = $(this);

        if (!pincode) {
            resultDiv.text('Please enter a pincode.').css('color', 'red');
            return;
        }

        $.ajax({
            url: wc_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'check_pincode_delivery',
                pincode: pincode
            },
            beforeSend: function() {
                button.prop('disabled', true);
                resultDiv.text('Checking...').css('color', '');
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.text(response.data.message).css('color', 'green');
                } else {
                    resultDiv.text(response.data.message).css('color', 'red');
                }
            },
            error: function() {
                resultDiv.text('An error occurred. Please try again.').css('color', 'red');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
}); 