jQuery(document).ready(function($) {
    $('.update-beef-item').on('click', function() {
        var button = $(this);
        var id = button.data('id');
        var price = $('input.beef-price[data-id="' + id + '"]').val();
        var available = $('input.beef-available[data-id="' + id + '"]').is(':checked') ? 1 : 0;

        button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: beefChartAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'save_beef_data',
                nonce: beefChartAdmin.nonce,
                id: id,
                price: price,
                available: available
            },
            success: function(response) {
                if (response.success) {
                    button.text('Updated!');
                    setTimeout(function() {
                        button.prop('disabled', false).text('Update');
                    }, 2000);
                } else {
                    alert('Error updating item. Please try again.');
                    button.prop('disabled', false).text('Update');
                }
            },
            error: function() {
                alert('Error updating item. Please try again.');
                button.prop('disabled', false).text('Update');
            }
        });
    });
});
