<?php
/*
Template Name: 追格-修改密码
*/
if (!defined('ABSPATH')) {
	exit;
}

//还没登录 跳转到登录页
if (!is_user_logged_in()) {
	wp_safe_redirect(home_url('/?lref=' . urlencode(home_url('/user-pwd'))));
	exit;
}

$my_user_id = get_current_user_id(); 

get_header();
?>

<!-- 主内容区 -->
<div class="main-body header-fix mb-20 pt-20">
	<div class="container">
		<div class="row d-flex flex-wrap">
			<!-- 侧边栏 -->
			<aside class="md-3">
				<div class="zhuige-user-menu zhuige-box mb-20">
					<p>
						<a href="<?php echo home_url('/user-info'); ?>" title="">个人资料</a>
					</p>
					<p class="menu-activ">
						<a href="<?php echo home_url('/user-pwd'); ?>" title="">账户安全</a>
					</p>
					<p>
						<a href="<?php echo wp_logout_url(home_url()); ?>" title="">退出</a>
					</p>
				</div>
			</aside>

			<!-- 大列表区 -->
			<article class="zhuige-uc md-9">

				<!-- 账户安全 -->
				<div class="zhuige-box p-20 mb-20">
					<h1 class="d-flex pb-20 align-items-center justify-content-between">
						<text>修改密码</text>
					</h1>
					<div class="pt-20">
						<div class="zhuige-forum-line mb-20">
							<h6>旧密码</h6>
							<p>
								<input id="oldpassword" type="password" name="oldpassword" placeholder="请输入旧密码" />
							</p>
						</div>
						<div class="row d-flex flex-nowrap">
							<div class="zhuige-forum-line md-6">
								<h6>新密码</h6>
								<p>
									<input id="newpassword" type="password" name="newpassword" placeholder="请输入新密码" />
								</p>
							</div>
							<div class="zhuige-forum-line md-6">
								<h6>确认密码</h6>
								<p>
									<input id="renewpassword" type="password" name="renewpassword" placeholder="请重复新密码" />
								</p>
							</div>
						</div>
					</div>
				</div>

				<div class="zhuige-forum-btn d-flex justify-content-center">
					<a href="javascript:void(0)" id="btn_submit" title="">提交</a>
				</div>

			</article>

		</div>
	</div>
</div>


<script>
    jQuery(document).ready(function($) {
        $('#btn_submit').click(function() {
            var loading = layer.load();
            $.post("/wp-admin/admin-ajax.php", {
                action: 'modify_password',
                oldpassword: $('#oldpassword').val(),
                newpassword: $('#newpassword').val(),
                renewpassword: $('#renewpassword').val(),
            }, function(res) {
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
    });
</script>

<?php wp_footer(); ?>

</body>

</html>