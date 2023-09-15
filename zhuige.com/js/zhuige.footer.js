/**
 * 追格主题
 */

jQuery(document).ready(function ($) {

    /** 返回顶部 start */
    $(window).scroll(function (event) {
        let scrollTop = $(this).scrollTop();
        if (scrollTop == 0) {
            $(".zhuige-float-gotop").hide();
        } else {
            $(".zhuige-float-gotop").show();
        }
    });

    $(".zhuige-float-gotop").click(function (event) {
        $("html,body").animate(
            { scrollTop: "0px" },
            666
        )
    });
    /** 返回顶部 end */


    // 搜索
    {
        $('.zhuige-btn-search').click(function () {
            let keyword = $('.input-keyword').val();
            keyword = keyword.trim();
            if (keyword.length == 0) {
                layer.msg('请输入关键字');
                return;
            }
            window.location.href = '/?s=' + keyword;
        });

        $('.input-keyword').keydown(function (event) {
            if (event.keyCode == 13) {
                let keyword = $(this).val();
                keyword = keyword.trim();
                if (keyword.length == 0) {
                    layer.msg('请输入关键字');
                    return;
                }
                window.location.href = '/?s=' + keyword;
            };
        });
    }
    // 搜索

    /**
     * 注册
     */
    function zhuige_user_register() {
        let username = $('.zhuige-text-reg-username').val();
        let email = $('.zhuige-text-reg-email').val();
        let pwd = $('.zhuige-text-reg-pwd').val();
        let repwd = $('.zhuige-text-reg-repwd').val();
        if (!$('.zhuige-text-reg-agreement').prop('checked')) {
            layer.msg('需要同意用户协议');
            return;
        }

        var params = {
            action: 'zhuige_theme_event',
            zgaction: "register",
            username: username,
            email: email,
            pwd: pwd,
            repwd: repwd
        };
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", params, (res) => {
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
    }
    $('.zhuige-btn-register').click(() => {
        zhuige_user_register();
        return false;
    });
    $('.zhuige-text-reg-repwd').keydown(function (event) {
        if (event.keyCode == 13) {
            zhuige_user_register();
        };
    });

    /**
     * 登录
     */
    function zhuige_user_login() {
        let login = $('.zhuige-text-login-login').val();
        let pwd = $('.zhuige-text-login-pwd').val();

        if (!$('.zhuige-text-login-agreement').prop('checked')) {
            layer.msg('需要同意用户协议');
            return;
        }

        var params = {
            action: 'zhuige_theme_event',
            zgaction: "login",
            log: login,
            pwd: pwd
        };
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", params, (res) => {
            layer.close(loading);
            if (!res.success) {
                if (res.data.error && res.data.error == 'login') {
                    show_login_pop();
                    return;
                }
                layer.msg(res.data);
                return;
            }

            if (lref) {
                window.location.href = lref;
            } else {
                window.location.reload();
            }
        });
    }
    $('.zhuige-btn-login').click(() => {
        zhuige_user_login();
        return false;
    });
    $('.zhuige-text-login-pwd').keydown(function (event) {
        if (event.keyCode == 13) {
            zhuige_user_login();
        };
    });

    /**
     * 显示扫码窗口
     */
    $('.zhuige-btn-pop-qrcode').click(() => {
        if ($('.zhuige-text-reg-agreement').is(":visible") && !$('.zhuige-text-reg-agreement').prop('checked')) {
            layer.msg('需要同意用户协议');
            return;
        }

        if ($('.zhuige-text-login-agreement').is(":visible") && !$('.zhuige-text-login-agreement').prop('checked')) {
            layer.msg('需要同意用户协议');
            return;
        }

        $('.zhuige-pop-login').hide();
        $('.zhuige-pop-register').hide();
        $('.zhuige-pop-forgot').hide();
        $('.zhuige-pop-search').hide();

        // $('.zhuige-pop-mask').show();
        $('.zhuige-pop-qrcode').show();

        let code_attr = $('#zhuige-wechat-login');
        let r_url = '';
        if (lref) {
            r_url = lref;
        } else {
            r_url = window.location.href;
        }

        $.getScript('https://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js', function () {
            var obj = new WxLogin({
                self_redirect: false,
                id: "zhuige-wechat-login",
                appid: code_attr.data('appid'),
                scope: "snsapi_login",
                redirect_uri: code_attr.data('wxruri'),
                state: encodeURIComponent(r_url),
                style: "",
                href: code_attr.data('css')
            });
        });

        return false;
    });


    /**
     * 显示注册窗口
     */
    $('.zhuige-btn-pop-register').click(() => {
        $('.zhuige-pop-login').hide();
        $('.zhuige-pop-qrcode').hide();
        $('.zhuige-pop-forgot').hide();
        $('.zhuige-pop-search').hide();

        // $('.zhuige-pop-mask').show();
        $('.zhuige-pop-register').show();

        return false;
    });

    /**
     * 显示忘记密码窗口
     */
    $('.zhuige-btn-pop-forgot').click(() => {
        $('.zhuige-pop-login').hide();
        $('.zhuige-pop-qrcode').hide();
        $('.zhuige-pop-register').hide();
        $('.zhuige-pop-search').hide();

        // $('.zhuige-pop-mask').show();
        $('.zhuige-pop-forgot').show();

        return false;
    });

    /**
     * 显示登录窗口
     */
    show_login_pop = function () {
        $('.zhuige-pop-qrcode').hide();
        $('.zhuige-pop-register').hide();
        $('.zhuige-pop-forgot').hide();
        $('.zhuige-pop-search').hide();

        // $('.zhuige-pop-mask').show();
        $('.zhuige-pop-login').show();
    }
    $('.zhuige-btn-pop-login').click(() => {
        show_login_pop();
        return false;
    });

    var lref = $('.zhuige-login-lref').val();
    if (lref) {
        show_login_pop();
    }

    /**
     * 忘记密码
     */
    $('.zhuige-btn-forgot-send-email').click(function () {
        let email = $('.zhuige-register-email').val();
        let reg = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
        if (!reg.test(email)) {
            layer.msg('请填写正确的邮箱地址');
            return;
        }

        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php", {
            action: "zhuige_theme_event",
            zgaction: 'forgot_send_email',
            email: email
        }, (res) => {
            layer.close(loading);

            if (!res.success) {
                layer.msg(res.data);
                return;
            }

            layer.msg('邮件已发送~');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        });
    });

    /**
     * 关闭弹框
     */
    $('.zhuige-pop-mask, .zhuige-btn-close-pop').click(() => {
        // $('.zhuige-pop-mask').hide();
        $('.zhuige-pop-login').hide();
        $('.zhuige-pop-qrcode').hide();
        $('.zhuige-pop-register').hide();
        $('.zhuige-pop-forgot').hide();
        $('.zhuige-pop-search').hide();

        return false;
    });

    /**
     * 阻止控件点击事件向上传递
     */
    $('.zhuige-pop-box').click(e => {
        e.stopPropagation()
    });

});