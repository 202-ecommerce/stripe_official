$(function(){
    $('.remove_card').click(function(event) {
        var element = $(this).closest('tr');
        $.ajax({
            type: 'POST',
            dataType: 'text',
            async: false,
            url: stripe_remove_card_url,
            data: {
                id_payment_method: $(this).data('id_payment_method')
            },
            success: function(datas) {
                element.remove();
            },
            error: function(err) {
                console.log(err);
            }
        });
    });
})