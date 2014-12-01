;(function($) {
    $(document).ready(function(){

        $('#comment-save').on('click', function(){

            var $link, $form, $list;

            $link = $(this);
            $form = $link.parents('form').first();
            $list = $form.find('#comment-status').first();

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {status: $list.val()},
                url: $form.attr('action')
            }).done(function(data, textStatus, jqXHR){
                if (data.success) {
                    $list.val(data.status);
                } else {
                    $list.val(data.status);
                }
            }).fail(function(jqXHR, textStatus, errorThrown){

            });

        });

    });
})(jQuery);
