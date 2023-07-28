<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<!-- 浮动链接 -->
<div class="zhuige-float-block">
	<div class="zhuige-float-gotop mt-20">
		<i class="fa fa-chevron-up"></i>
	</div>
</div>

<!-- 弹窗遮罩 -->
<?php
$register_normal = zhuige_theme_option('register_normal_switch');
$login_weixin = zhuige_theme_option('login_weixin_switch');
?>
<div class="zhuige-pop-mask d-flex align-items-center justify-content-center" style="display: none;">

	<!-- 账号登录 - 注册 -->
	<div class="zhuige-pop-box p-20 zhuige-pop-login" style="display: none;">
		<span class="closed zhuige-btn-close-pop">关闭</span>
		<h3 class="mb-20 mt-10">账号登录</h3>
		<div class="zhuige-pop-form">
			<p class="mb-20 d-flex justify-content-between">
				<text>用户名/邮箱</text>
				<input type="text" class="zhuige-text-login-login" placeholder="" />
			</p>
			<p class="mb-20 d-flex justify-content-between">
				<text>密码</text>
				<input type="password" class="zhuige-text-login-pwd" placeholder="" />
			</p>
		</div>
		<div class="zhuige-pop-opt">
			<p class="mb-20 d-flex align-items-center justify-content-between">
				<span>
					<?php
					if ($register_normal) {
						echo '<a href="javascript:void(0)" class="zhuige-btn-pop-register" title="账号注册">账号注册</a>';
					}
					if ($register_normal && $login_weixin) {
						echo '&nbsp;/&nbsp;';
					}
					if ($login_weixin) {
						echo '<a href="javascript:void(0)" class="zhuige-btn-pop-qrcode" title="微信登录">微信登录</a>';
					}
					?>
				</span>
				<?php if (zhuige_theme_option('forgot_switch')) { ?>
					<a href="javascript:void(0)" class="zhuige-btn-pop-forgot" title="忘记密码">忘记密码</a>
				<?php } ?>
			</p>
			<p class="mb-10 zhuige-pop-btn d-flex">
				<a href="javascript:void(0)" class="zhuige-btn-login" title="">登录</a>
			</p>
		</div>
		<div class="zhuige-pop-tips justify-content-center pt-20 d-flex align-items-center">
			<a href="<?php echo get_page_link(zhuige_theme_option('login_yhxy')) ?>" target="_blank" title="用户协议">用户协议</a>
			<text>|</text>
			<a href="<?php echo get_page_link(zhuige_theme_option('login_yszc')) ?>" target="_blank" title="隐私政策">隐私政策</a>
			<label><input type="checkbox" class="zhuige-text-login-agreement" />我已阅读并同意</label>
		</div>
	</div>

	<!-- 标准弹窗 - 扫码 -->
	<div class="zhuige-pop-box p-20 zhuige-pop-qrcode" style="display: none;">
		<span class="closed zhuige-btn-close-pop">关闭</span>
		<h3 class="mb-20 mt-10">微信扫码登录</h3>
		<div class="zhuige-pop-qr pb-20">
			<div id="zhuige-wechat-login" data-appid="<?php echo zhuige_theme_option('wx_app_id') ?>" data-wxruri="<?php echo home_url('/wp-admin/admin-ajax.php?action=weixin_login_callback') ?>" data-css="<?php echo ZHUIGE_THEME_URL . '/css/wx_login.css' ?>">
				<!-- <img class="m-20" src="" alt="" /> -->
			</div>
		</div>
		<div class="zhuige-pop-tips pt-20 d-flex align-items-center justify-content-between">
			<a href="javascript:void(0)" class="zhuige-btn-pop-login" title="账号登录">账号登录</a>
			<p class="d-flex align-items-center">
				<a href="<?php echo get_page_link(zhuige_theme_option('login_yhxy')) ?>" target="_blank" title="用户协议">用户协议</a>
				<text>|</text>
				<a href="<?php echo get_page_link(zhuige_theme_option('login_yszc')) ?>" target="_blank" title="隐私政策">隐私政策</a>
			</p>
		</div>
	</div>

	<!-- 标准弹窗 - 注册 -->
	<div class="zhuige-pop-box p-20 zhuige-pop-register" style="display: none;">
		<span class="closed zhuige-btn-close-pop">关闭</span>
		<h3 class="mb-20 mt-10">账号注册</h3>
		<div class="zhuige-pop-form">
			<p class="mb-20 d-flex justify-content-between">
				<text>用户名</text>
				<input type="text" class="zhuige-text-reg-username" placeholder="" />
			</p>
			<p class="mb-20 d-flex justify-content-between">
				<text>邮箱</text>
				<input type="text" class="zhuige-text-reg-email" placeholder="" />
			</p>
			<p class="mb-20 d-flex justify-content-between">
				<text>密码</text>
				<input type="password" class="zhuige-text-reg-pwd" placeholder="" />
			</p>
			<p class="mb-20 d-flex justify-content-between">
				<text>确认密码</text>
				<input type="password" class="zhuige-text-reg-repwd" placeholder="" />
			</p>
		</div>
		<div class="zhuige-pop-opt">
			<p class="mb-20 d-flex align-items-center justify-content-between">
				<span>
					<a href="javascript:void(0)" class="zhuige-btn-pop-login" title="">账号登录</a>
					<?php
					if ($login_weixin) {
						echo '&nbsp;/&nbsp;';
					}
					if ($login_weixin) {
						echo '<a href="javascript:void(0)" class="zhuige-btn-pop-qrcode" title="微信登录">微信登录</a>';
					}
					?>
				</span>
			</p>
			<p class="mb-10 zhuige-pop-btn d-flex">
				<a href="javascript:void(0)" class="zhuige-btn-register" title="">注册</a>
			</p>
		</div>
		<div class="zhuige-pop-tips justify-content-center pt-20 d-flex align-items-center">
			<a href="<?php echo get_page_link(zhuige_theme_option('login_yhxy')) ?>" target="_blank" title="用户协议">用户协议</a>
			<text>|</text>
			<a href="<?php echo get_page_link(zhuige_theme_option('login_yszc')) ?>" target="_blank" title="隐私政策">隐私政策</a>
			<label><input type="checkbox" class="zhuige-text-reg-agreement" />我已阅读并同意</label>
		</div>
	</div>


	<!-- 标准弹窗 - 找回密码 -->
	<div class="zhuige-pop-box p-20 zhuige-pop-forgot" style="display: none;">
		<span class="closed zhuige-btn-close-pop">关闭</span>
		<h3 class="mb-20 mt-10">找回密码</h3>
		<div class="zhuige-pop-form">
			<p class="mb-20 d-flex get-pwd">
				<input type="text" class="zhuige-register-email" placeholder="请输入注册时的邮箱" />
			</p>
		</div>
		<div class="zhuige-pop-opt">
			<p class="mb-10 zhuige-pop-btn d-flex">
				<a href="javascript:void(0)" class="zhuige-btn-forgot-send-email" title="">获取密码重置邮件</a>
			</p>
		</div>
	</div>

</div>

<footer>
	<div class="container d-flex zhuige-copy-link justify-content-between">
		<div class="zhuige-copyright">
			<?php
			$footer_copyright = zhuige_theme_option('footer_copyright', '');
			if (empty($footer_copyright)) {
				$footer_copyright = 'Copyright © zhuige.com 请在后台设置';
			}
			echo '<text>' . $footer_copyright . '</text>';
			?>
		</div>
		<div class="zhuige-foot-link d-flex align-content-baseline">
			<text>主题设计：</text>
			<a href="https://www.zhuige.com" title="追格（zhuige.com）">追格（zhuige.com）</a>
		</div>

	</div>

	<?php
	$footer_nav = zhuige_theme_option('footer_nav');
	if (!empty($footer_nav)) {
	?>
		<div class="container zhuige-site-links pt-30 pb-30 d-flex align-content-center">
			<?php
			$end_item = end($footer_nav);
			foreach ($footer_nav as $nav) :
				echo '<a href="' . $nav['url'] . '" title="' . $nav['title'] . '">' . $nav['title'] . '</a>';
				if ($end_item !== $nav) :
					echo '<span>/</span>';
				endif;
			endforeach;
			?>
		</div>
	<?php
	}
	?>
</footer>


<!-- mobile footer 默认居中，最多5个 -->
<?php if (wp_is_mobile()) { ?>
	<div class="container zhuige-mobile-footer d-flex  align-content-center justify-content-center">
		<?php
		$h5_tabbar = zhuige_theme_option('h5_tabbar');
		if (is_array($h5_tabbar)) {
			$currect_url = zhuige_theme_url();
			foreach ($h5_tabbar as $tab) {
				if ($tab['switch'] && $tab['icon'] && $tab['icon']['url'] && $tab['icon_sel'] && $tab['icon_sel']['url']) {
					$is_active = zhuige_url_module($currect_url) == zhuige_url_module($tab['url']);
					$class = ($is_active ? 'active' : '');
					$image = ($is_active ? $tab['icon_sel']['url'] : $tab['icon']['url']);
					$target = $tab['blank'] ? '_blank' : '_self';
		?>
					<div>
						<!-- 图片变换的时候 a 增加 class="active" -->
						<a class="<?php echo $class ?>" href="<?php echo $tab['url'] ?>" target="<?php echo $target ?>" title="<?php echo $tab['title'] ?>">
							<span>
								<img src="<?php echo $image ?>" />
							</span>
							<p><?php echo $tab['title'] ?></p>
						</a>
					</div>
			<?php
				}
			}
		} else { ?>
			<div>
				<a href="<?php echo home_url() ?>" title="首页">
					<span>
						<img src="<?php echo ZHUIGE_THEME_URL . '/images/default_logo.png' ?>" />
					</span>
					<p>首页</p>
				</a>
			</div>
			<div>
				<a href="<?php echo home_url('/user-info') ?>" title="我的">
					<span>
						<img src="<?php echo ZHUIGE_THEME_URL . '/images/default_logo.png' ?>" />
					</span>
					<p>我的</p>
				</a>
			</div>
		<?php
		}
		?>

	</div>
<?php } ?>

<?php wp_footer(); ?>

<div style="display: none;">
	<script>
		<?php echo zhuige_theme_option('footer_statistics'); ?>
	</script>
</div>

</body>

</html>