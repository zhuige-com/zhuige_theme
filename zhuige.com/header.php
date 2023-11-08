<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<title><?php zhuige_theme_seo_title() ?></title>
	<?php wp_head(); ?>
	<link href="<?php echo ZHUIGE_THEME_URL . '/fontawesome/css/all.min.css' ?>" rel="stylesheet" />
	<link href="<?php echo ZHUIGE_THEME_URL . '/fontawesome/css/v4-shims.min.css' ?>" rel="stylesheet" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>?ver=<?php echo filemtime(get_stylesheet_directory() . '/style.css') ?>">
</head>

<?php
$my_user_id = get_current_user_id();
?>

<body>
	<?php
	$lref = isset($_GET['lref']) ? urldecode($_GET['lref']) : '';
	if ($lref) {
		echo '<input type="hidden" class="zhuige-login-lref" value="' . $lref . '" />';
	}

	$is_home = false;
	if (is_home()) {
		$zhuige_page = get_query_var('zhuige_page');
		if (empty($zhuige_page)) {
			$is_home = true;
		}
	}
	?>
	<header class="index-header <?php echo !$is_home ? 'header-bg' : '' ?>">
		<!--主导航 -->
		<nav class="container">
			<div class="logo pl-20">
				<a href="<?php echo home_url(); ?>"><?php zhuige_theme_logo(); ?></a>
			</div>
			<div class="zhuige-nav">
				<ul class="zhuige-nav-list">
					<?php
					$site_nav = zhuige_theme_option('site_nav');
					if (is_array($site_nav) && count($site_nav) > 0) {
						$currect_url = zhuige_theme_url();
						foreach ($site_nav as $item) {
							if ($item['switch']) {
								$class = (zhuige_url_module($currect_url) == zhuige_url_module($item['url']) ? 'nav-activ' : '');
								$target = $item['blank'] ? '_blank' : '_self';
					?>
								<li class="<?php echo $class ?>"><a href="<?php echo $item['url'] ?>" target="<?php echo $target ?>"><?php echo $item['title'] ?></a></li>
						<?php
							}
						}
					} else {
						?>
						<li><a href="<?php echo admin_url('admin.php?page=zhuige-theme') ?>">点击配置菜单</a></li>
					<?php
					}
					?>
				</ul>
			</div>
			<div class="zhuige-nav-side pr-20 d-flex align-items-center">
				<ul class="d-flex">
					<li class="nav-user d-flex align-items-center">

						<?php if ($my_user_id) { ?>
							<cite>
								<?php echo zhuige_user_avatar($my_user_id) ?>
							</cite>
							<a href="/user-info" title="注册登录"><?php echo get_user_meta($my_user_id, 'nickname', true) ?></a>
						<?php } else { ?>
							<cite>
								<img src="<?php echo ZHUIGE_THEME_URL . '/images/avatar.png' ?>" alt="用户名" />
							</cite>
							<a href="javascript:void(0)" class="zhuige-btn-pop-login" title="注册/登录">注册/登录</a>
						<?php } ?>

					</li>
				</ul>
			</div>
		</nav>
	</header>