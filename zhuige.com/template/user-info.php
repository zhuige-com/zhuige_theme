<?php
if (!defined('ABSPATH')) {
	exit;
}

//还没登录 跳转到登录页
if (!is_user_logged_in()) {
	wp_safe_redirect(home_url('/?lref=' . urlencode(home_url('/user-info'))));
	exit;
}

$user = wp_get_current_user();


$my_user_id = $user->ID;

$avatar = zhuige_user_avatar($my_user_id);
$nickname = get_user_meta($my_user_id, 'nickname', true);
$cover = get_user_meta($my_user_id, 'zhuige_theme_cover', true);
if (empty($cover)) {
	$cover = ZHUIGE_THEME_URL . '/images/placeholder.png';;
}
$gender = get_user_meta($my_user_id, 'zhuige_theme_gender', true);
$city = get_user_meta($my_user_id, 'zhuige_theme_city', true);
$web = get_user_meta($my_user_id, 'zhuige_theme_web', true);
$weixin = get_user_meta($my_user_id, 'zhuige_theme_weixin', true);

$user_email = $user->user_email;

$wx_code = get_user_meta($my_user_id, 'zhuige_theme_wx_code', true);
$reward_code = get_user_meta($my_user_id, 'zhuige_theme_reward_code', true);


get_header();
?>

<div class="zhuige-advert zhuige-advert-about zhuige-user-page relative">
	<div class="zhuige-advert-info d-flex d-flex relative justify-content-center">
		<div class="md-12 d-flex align-items-center justify-content-between">
			<div class="zhuige-user d-flex align-items-center mb-30-sm mb-10-xs">
				<div class="user-avatar mr-10">
					<?php echo $avatar; ?>
				</div>
				<div class="user-info">
					<h6 class="d-flex align-items-center mb-10">
						<a href="javascript:void(0)" target="_blank"><?php echo $nickname ?></a>
					</h6>
					<!-- 用户简介 -->
					<p><?php echo zhuige_theme_user_sign($my_user_id) ?></p>
				</div>
			</div>

			<div class="user-act d-flex">
				<a href="<?php echo zhuige_theme_user_site($my_user_id) ?>" target="_blank">个人主页</a>
				<input style="display: none;" id="cover_picker" type="file" name="cover" accept="image/*" />
				<a href="javascript:void(0)" class="cover_picker_trigger" title="修改封面">修改封面</a>
			</div>
		</div>
	</div>
	<div class="zhuige-advert-bg absolute">
		<img src="<?php echo $cover ?>" alt="用户背景" />
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20 pt-20">
	<div class="container">
		<div class="row d-flex flex-wrap">
			<!-- 侧边栏 -->
			<aside class="md-3">
				<div class="zhuige-user-menu zhuige-box mb-20">
					<p class="menu-activ">
						<a href="<?php echo home_url('/user-info'); ?>" title="">个人资料</a>
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

				<!-- 个人资料 -->
				<div class="zhuige-box p-20 mb-20">
					<h1 class="d-flex pb-20 align-items-center justify-content-between">
						<text>个人资料</text>
					</h1>

					<div class="zhuige-forum pt-20">

						<div class="zhuige-user d-flex align-items-center mb-30">
							<div class="user-avatar mr-10 zhuige-avatar-container">
								<?php echo $avatar; ?>
							</div>
							<div class="user-info">
								<h6 class="d-flex align-items-center mb-10">
									<text><?php echo $nickname ?></text>
									<input style="display: none;" id="avatar_picker" type="file" name="avatar" accept="image/*" />
									<a href="javascript:void(0)" class="avatar_picker_trigger" title="修改头像">修改头像</a>
								</h6>
								<p>注册时间：<?php echo $user->user_registered ?></p>
							</div>
						</div>

						<div class="row d-flex flex-wrap">
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>用户昵称</h6>
								<p>
									<input type="text" id="nickname" name="nickname" value="<?php echo $nickname ?>" placeholder="请输入昵称" />
								</p>
							</div>
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>注册邮箱</h6>
								<p>
									<input type="text" placeholder="www@zhuige.com" value="<?php echo $user_email ?>" readonly />
								</p>
							</div>
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>性别</h6>
								<p>
									<select id="gender" name="gender">
										<option value="保密" <?php echo $gender == '保密' ? 'selected' : '' ?>>保密</option>
										<option value="男" <?php echo $gender == '男' ? 'selected' : '' ?>>男</option>
										<option value="女" <?php echo $gender == '女' ? 'selected' : '' ?>>女</option>
									</select>
								</p>
							</div>
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>所在城市</h6>
								<p>
									<input type="text" id="city" name="city" value="<?php echo $city ?>" placeholder="如:北京" />
								</p>
							</div>
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>网址</h6>
								<p>
									<input type="text" id="web" name="web" value="<?php echo $web ?>" placeholder="如:www.zhuige.com" />
								</p>
							</div>
							<div class="zhuige-forum-line md-6 mb-20">
								<h6>微信</h6>
								<p>
									<input type="text" id="weixin" name="weixin" value="<?php echo $weixin ?>" placeholder="请输入微信号" />
								</p>
							</div>

						</div>
						<div class="zhuige-forum-line mb-20">
							<h6 class="d-flex justify-content-between">
								<text>个性签名</text>
								<span>140字以内</span>
							</h6>
							<p>
								<textarea id="sign" name="sign" placeholder="请简短介绍一下自己吧…" maxlength="140"><?php echo $sign ?></textarea>
							</p>
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
		$(document).on('click', '.cover_picker_trigger', function() {
			$("#cover_picker").trigger('click')
		});

		$(document).on('click', '.avatar_picker_trigger', function() {
			$("#avatar_picker").trigger('click')
		});

		$(document).on('click', '.wx_code_picker_trigger', function() {
			$("#wx_code_picker").trigger('click')
		});

		$(document).on('click', '.reward_code_picker_trigger', function() {
			$("#reward_code_picker").trigger('click')
		});

		$(document).on('click', '.zhuige-btn-delete-wx-code', function() {
			$(this).parent().parent().html('<div class="wx_code_picker_trigger"><i class="fa fa-file-image-o mt-30"></i><p class="mt-10">请上传</p></div>')
			return false;
		});

		$(document).on('click', '.zhuige-btn-delete-reward-code', function() {
			$(this).parent().parent().html('<div class="reward_code_picker_trigger"><i class="fa fa-file-image-o mt-30"></i><p class="mt-10">请上传</p></div>')
			return false;
		});

		/**
		 * 封面
		 */
		var cover = '';
		$('#cover_picker').change(function() {
			var formData = new FormData();
			formData.append("image", $(this)[0].files[0]);
			formData.append("action", "ajax_upload_image");
			formData.append("type", "user_cover");
			var loading = layer.load();
			$.ajax({
				url: "/wp-admin/admin-ajax.php",
				type: "post",
				data: formData,
				processData: false, // 告诉jQuery不要去处理发送的数据
				contentType: false, // 告诉jQuery不要去设置Content-Type请求头
				dataType: 'text',
				success: function(data) {
					layer.close(loading);

					var params = JSON.parse(data)
					if (params.url) {
						$('.zhuige-advert-bg').html('<img src="' + params.url + '" alt="" />');
						cover = params.url;
					}
				},
				error: function(data) {}
			});
		});

		/**
		 * 头像
		 */
		var avatar = '';
		$('#avatar_picker').change(function() {
			var formData = new FormData();
			formData.append("image", $(this)[0].files[0]);
			formData.append("action", "ajax_upload_image");
			var loading = layer.load();
			$.ajax({
				url: "/wp-admin/admin-ajax.php",
				type: "post",
				data: formData,
				processData: false, // 告诉jQuery不要去处理发送的数据
				contentType: false, // 告诉jQuery不要去设置Content-Type请求头
				dataType: 'text',
				success: function(data) {
					layer.close(loading);

					var params = JSON.parse(data)
					if (params.url) {
						$('.zhuige-avatar-container').html('<img src="' + params.url + '" alt="" />');
						avatar = params.url;
					}
				},
				error: function(data) {}
			});
		});

		/**
		 * 微信二维码
		 */
		var wx_code = '';
		$('#wx_code_picker').change(function() {
			var formData = new FormData();
			formData.append("image", $(this)[0].files[0]);
			formData.append("action", "ajax_upload_image");
			var loading = layer.load();
			$.ajax({
				url: "/wp-admin/admin-ajax.php",
				type: "post",
				data: formData,
				processData: false, // 告诉jQuery不要去处理发送的数据
				contentType: false, // 告诉jQuery不要去设置Content-Type请求头
				dataType: 'text',
				success: function(data) {
					layer.close(loading);

					var params = JSON.parse(data)
					if (params.url) {
						$('.wx_code_picker_trigger').html('<div class="uploaded"><p><img src="' + params.url + '" alt="" /></p><i class="fa fa-plus-circle zhuige-btn-delete-wx-code"></i></div>');
						wx_code = params.url;
					}
				},
				error: function(data) {}
			});
		});

		/**
		 * 赞赏码
		 */
		var reward_code = '';
		$('#reward_code_picker').change(function() {
			var formData = new FormData();
			formData.append("image", $(this)[0].files[0]);
			formData.append("action", "ajax_upload_image");
			var loading = layer.load();
			$.ajax({
				url: "/wp-admin/admin-ajax.php",
				type: "post",
				data: formData,
				processData: false, // 告诉jQuery不要去处理发送的数据
				contentType: false, // 告诉jQuery不要去设置Content-Type请求头
				dataType: 'text',
				success: function(data) {
					layer.close(loading);

					var params = JSON.parse(data)
					if (params.url) {
						$('.reward_code_picker_trigger').html('<div class="uploaded"><p><img src="' + params.url + '" alt="" /></p><i class="fa fa-plus-circle zhuige-btn-delete-reward-code"></i></div>');
						reward_code = params.url;
					}
				},
				error: function(data) {}
			});
		});

		/**
		 * 提交修改
		 */
		$('#btn_submit').click(function() {
			var loading = layer.load();
			$.post("/wp-admin/admin-ajax.php", {
				action: 'ajax_set_user_info',
				nickname: $('#nickname').val(),
				gender: $('#gender').val(),
				city: $('#city').val(),
				web: $('#web').val(),
				avatar: avatar,
				cover: cover,
				sign: $('#sign').val(),
				reward_code: reward_code,
				wx_code: wx_code,
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

				layer.msg('提交成功');
				setTimeout(() => {
					window.location.reload();
				}, 1500);
			});
		});
	});
</script>

<?php wp_footer(); ?>

</body>

</html>