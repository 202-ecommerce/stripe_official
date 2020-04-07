$(function(){
    $('.remove_card').click(function(event) {
        console.log($(this).data('id_payment_method'));
        $.ajax({
            type: 'POST',
            dataType: 'text',
            async: false,
            url: stripe_remove_card_url,
            data: {
                id_payment_method: $(this).data('id_payment_method')
            },
            success: function(datas) {
                console.log('success');
                console.log(datas);
                $(this).closest('tr').remove();
            },
            error: function(err) {
                console.log(err);
            }
        });
    });
})