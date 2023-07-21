/**
 * 追格主题
 */

jQuery(document).ready(function ($) {

    /** -- 点赞 -- start -- */
    $('.zhuige-btn-like').click(function () {
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'like',
            post_id: $(this).data('post_id')
        }, (res) => {
            layer.close(loading);

            if (!res.success) {
                if (res.data.error && res.data.error == 'login') {
                    show_login_pop();
                    return;
                }
                layer.msg(res.data);
                return;
            }

            window.location.reload();
        });
    });
    /** -- 点赞 -- end -- */

    /** -- 收藏 -- start -- */
    $('.zhuige-btn-favorite').click(function () {
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'favorite',
            post_id: $(this).data('post_id')
        }, (res) => {
            layer.close(loading);

            if (!res.success) {
                if (res.data.error && res.data.error == 'login') {
                    show_login_pop();
                    return;
                }
                layer.msg(res.data);
                return;
            }

            window.location.reload();
        });
    });
    /** -- 点赞 -- end -- */

    /**
     * 评论
     */
    $('.zhuige-btn-comment-submit').click(() => {
        let post_id = $('.zhuige-comment-post_id').val();
        let content = $('.zhuige-comment-content').val();
        let parent = $('.zhuige-comment-parent').val();

        var params = {
            action: 'zhuige_theme_event',
            zgaction: "comment",
            post_id: post_id,
            content: content,
            parent: parent
        };
        $.post("/wp-admin/admin-ajax.php", params, (res) => {
            if (!res.success) {
                if (res.data.error && res.data.error == 'login') {
                    show_login_pop();
                    return;
                }
                layer.msg(res.data);
                return;
            }

            window.location.reload();
        });

        return false;
    });


    /**
     * 评论 回复
     */
    $('.zhuige-comment-btn-reply').click(function () {
        $('.zhuige-comment-reply-nickname').text($(this).data('nickname'));
        $('.zhuige-comment-parent').val($(this).data('comment_id'));
        $('.zhuige-comment-reply-container').show();
    });

    /**
     * 评论 回复 取消
     */
    $('.zhuige-btn-comment-reply-cancel').click(() => {
        $('.zhuige-comment-reply-container').hide();
    })


    /**
     * 生成文章目录
     */
    let catalog = '';
    $('.zhuige-view-article h2').each(function (index, element) {
        $(element).attr('id', 'ariticle-section-' + index)

        catalog += '<li class="zhuige-catalog-item" data-section="' + index + '" data-offset="' + $(element).offset().top + '">';
        catalog += '<a href="javascript:void(0)" title="' + $(element).text() + '">' + $(element).text() + '</a>';
        catalog += '</li>';
    });
    if (catalog.length > 0) {
        $(".zhuige-view-menu ol").append(catalog);
        $(".zhuige-menu-aside").show();
    }

    /**
     * 目录点击
     */
    $(document).on("click", '.zhuige-catalog-item', function () {
        $('html, body').animate({
            scrollTop: ($('#ariticle-section-' + $(this).data('section')).offset().top - 100)
        }, 1000);
    });

    $('.zhuige-catalog-item:first').addClass('active');

    /**
     * 页面滚动-修改目录状态
     */
    $(window).scroll(function (event) {
        let scrollTop = $(this).scrollTop();

        let current_catalog_item = undefined;
        $('.zhuige-catalog-item').each(function (index, element) {
            if (scrollTop + 101 > $(element).data('offset')) {
                current_catalog_item = $(element);
            }
        });
        if (current_catalog_item) {
            $('.zhuige-catalog-item').removeClass('active');
            current_catalog_item.addClass('active');
        }
    });
}); 