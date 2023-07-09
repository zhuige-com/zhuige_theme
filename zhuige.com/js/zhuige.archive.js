/**
 * 追格主题
 */

jQuery(document).ready(function ($) {

    /**
     * 归档页 加载更多
     */
    $('.zhuige-more-btn').click(e => {
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'get_posts',
            offset: $('.zhuige-post-for-ajax-count').length,
            cat: $('.zhuige-theme-cat').val(),
            tag: $('.zhuige-theme-tag').val(),
            author: $('.zhuige-theme-author').val(),
        }, (res) => {
            layer.close(loading);

            if (res.success) {
                $('.zhuige-list-container').append(res.data.content);

                if (res.data.more) {
                    $('.zhuige-more-btn').show();
                } else {
                    $('.zhuige-more-btn').hide();
                }
            }
        });
    })

});