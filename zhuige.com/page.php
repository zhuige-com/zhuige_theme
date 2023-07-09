<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();

$background = zhuige_theme_option('about_bg');
if ($background && $background['url']) {
	$background = $background['url'];
} else {
	$background = ZHUIGE_THEME_URL . '/images/home_header_background.jpg';
}

the_post();
global $post;

?>

<!-- 通栏banner -->
<div class="zhuige-advert zhuige-advert-about relative">
	<div class="container d-flex align-items-center justify-content-center">
		<h1><?php echo $post->post_title ?></h1>
	</div>
	<div class="zhuige-advert-bg absolute">
		<img src="<?php echo $background ?>" alt="背景" />
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20 pt-20">
	<?php $sticky_top = 'position: sticky; top: ' . (zhuige_theme_is_show_admin_bar() ? 114 : 60) . 'px;z-index:99;'; ?>
	<div class="container d-flex justify-content-center" style="<?php echo $sticky_top; ?>">
		<div class="zhuige-page-menu zhuige-about-menu mb-20 md-9">
			<div class="zhuige-box p-20 d-flex align-items-center justify-content-center">
				<?php
				$about_nav = zhuige_theme_option('about_nav');
				if (is_array($about_nav)) {
					foreach ($about_nav as $page_id) {
						$page = get_page($page_id);
						if ($page) {
							$class = ($page_id == $post->ID ? 'active' : '');
				?>
							<a href="<?php echo get_page_link($page_id) ?>" class="<?php echo $class ?>" title="<?php echo $page->post_title ?>"><?php echo $page->post_title ?></a>
				<?php
						}
					}
				}
				?>
			</div>
		</div>
	</div>

	<div class="container d-flex justify-content-center">

		<article class="md-9">
			<div class="zhuige-box zhuige-view-article zhuige-page p-20">
				<?php echo apply_filters('the_content', $post->post_content); ?>
			</div>
		</article>

	</div>
</div>

<?php get_footer(); ?>