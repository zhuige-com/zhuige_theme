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
						<a href="<?php echo home_url('/user-spend-log'); ?>" title="">消费记录</a>
					</p>
					<p>
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
				<!-- 消费记录 -->
				<div class="zhuige-box p-20 mb-20">
					<?php
					global $wpdb;
					$table_spend_log = $wpdb->prefix . 'zhuige_theme_spend_log';
					$amount_total = $wpdb->get_var($wpdb->prepare("SELECT SUM(`amount`) FROM $table_spend_log WHERE `user_id`=%d", $my_user_id));
					?>
					<h1 class="d-flex pb-20 align-items-center justify-content-between">
						<text>消费记录</text>
						<span>总计消费：￥<?php echo $amount_total ?></span>
					</h1>

					<div class="zhuige-order">
						<?
						$result = zhuige_theme_spend_log_output($my_user_id, 0);
						echo $result['content'];

						if (empty($result['content'])) {
						?>
							<!-- 无数据提示 -->
							<div class="zhuige-none-tip">
								<img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt="none" />
								<p>暂无数据，随便逛逛..</p>
							</div>
						<?php
						}
						?>
					</div>

					<?php
					if ($result['more']) {
					?>
						<div class="zhuige-list-more d-flex justify-content-center mt-20 mb-20">
							<a href="javascript:void(0)" class="zhuige-more-btn" title="更多">加载更多</a>
						</div>
					<?php
					}
					?>
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

					if (res.data) {
						layer.msg(res.data);
					}

					return;
				}

				window.location.reload();
			});
		});

		$('.zhuige-more-btn').click(function() {
			var loading = layer.load();
			$.post("/wp-admin/admin-ajax.php", {
				action: 'zhuige_theme_event',
				zgaction: 'get_spend_log',
				offset: $('.zhuige-order-list').length,
			}, function(res) {
				layer.close(loading);

				if (!res.success) {
					if (res.data.error && res.data.error == 'login') {
						show_login_pop();
						return;
					}

					if (res.data) {
						layer.msg(res.data);
					}

					return;
				}

				$('.zhuige-order').append(res.data.content);

				if (res.data.more) {
					$('.zhuige-list-more').show();
				} else {
					$('.zhuige-list-more').hide();
				}
			});
		});
	});
</script>

<?php wp_footer(); ?>

</body>

</html>