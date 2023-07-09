/**
 * 追格主题
 */

jQuery(document).ready(function ($) {

    /**
     * 首页 加载更多
     */
    $('.zhuige-more-btn').click(function () {
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'get_posts',
            offset: $('.zhuige-post-for-ajax-count').length,
            cat: $('.zhuige-theme-cat').val(),
            page_count: $('.zhuige-theme-page-count').val(),
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

    /**
     * 首页 切换分类TAB
     */
    $('.zhuige-home-cat-tab').click(function () {
        $('.zhuige-home-cat-tab').removeClass('active');
        $(this).addClass('active');

        let cat_id = $(this).data('cat_id');
        $('.zhuige-theme-cat').val(cat_id)

        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'get_posts',
            offset: 0,
            cat: cat_id,
            page_count: $('.zhuige-theme-page-count').val(),
        }, (res) => {
            layer.close(loading);

            if (res.success) {
                $('.zhuige-base-list').remove();

                $('.zhuige-list-container').append(res.data.content);

                if (res.data.content.length > 0) {
                    $('.zhuige-theme-no-data').hide();
                } else {
                    $('.zhuige-theme-no-data').show();
                }

                if (res.data.more) {
                    $('.zhuige-more-btn').show();
                } else {
                    $('.zhuige-more-btn').hide();
                }
            }
        });
    })

    $(window).scroll(function (event) {
        let scrollTop = $(this).scrollTop();
        if (scrollTop == 0) {
            $(".index-header").removeClass('header-bg');
        } else {
            $(".index-header").addClass('header-bg');
        }
    });
});