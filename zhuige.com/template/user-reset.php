<?php
/*
Template Name: 追格-找回密码-重置密码
*/
?>

<?php
$user_id = isset($_GET['u']) ? sanitize_text_field($_GET['u']) : '';
$token = isset($_GET['t']) ? sanitize_text_field($_GET['t']) : '';

//已经登录直接跳转到用户中心
if (is_user_logged_in()) {
    wp_safe_redirect(home_url('/user-info'));
    exit;
}

if (!$user_id || !$token) {
    wp_safe_redirect(home_url());
    exit;
}

$reset_token = get_user_meta($user_id, 'zhuige_theme_reset_token', true);
if (!$reset_token) {
    wp_safe_redirect(home_url());
    exit;
}

if (!is_array($reset_token) || $reset_token['token'] != $token || $reset_token['expire'] < time()) {
    wp_safe_redirect(home_url());
    exit;
}
?>

<?php get_header(); ?>

<!--主内容区-->

<article class="zhuige-re-pass d-flex align-items-center justify-content-center">
    <div class="zhuige-re-pass-form pt-30">
        <form class="p-30">
            <h3 class="mb-20">Hi~ 找回密码</h3>
            <p class="mb-10">
                <input id="password" type="password" name="password" placeholder="请输入新密码" />
            </p>
            <p>
                <input id="user_id" type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                <input id="token" type="hidden" name="token" value="<?php echo $token; ?>" />
                <input id="btn-submit" type="button" name="" value="下一步">
            </p>
        </form>
    </div>

</article>

<script>
    jQuery(document).ready(function($) {
        $('#btn-submit').click(function() {
            var loading = layer.load();
            $.post("/wp-admin/admin-ajax.php", {
                action: 'zhuige_theme_event',
                zgaction: 'user_reset_pwd',
                user_id: $('#user_id').val(),
                password: $('#password').val(),
                token: $('#token').val(),
            }, function(res) {
                layer.close(loading);

                if (res.error) {
                    layer.alert(res.error);
                    return;
                }

                layer.msg('密码已重置');

                setTimeout(() => {
                    window.location.href = '/user-info';
                }, 1000);
            });
        });
    });
</script>

<?php get_footer(); ?>