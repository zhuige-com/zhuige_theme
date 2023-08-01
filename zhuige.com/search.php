<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();

global $wp_query;
$query_obj = $wp_query->get_queried_object();


$title = '';
$description = '';

$cur_search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

$list_header = zhuige_theme_option('list_header');
if (!$list_header) {
	$list_header = [
		'bg_image' => '',
		'bg_video' => '',
		'tip' => '搜索原来如此简单...',
	];
}
?>

<!-- 大搜索块 -->
<div class="zhuige-base-block mini-search relative">
	<div class="zhuige-search-box absolute">
		<div class="container">
			<!--主搜索区-->
			<div class="zhuige-main-search">
				<h1><?php echo $title; ?></h1>
				<h6><?php echo $description; ?></h6>
				<div class="zhuige-search-form">
					<input type="search" class="input-keyword" placeholder="<?php echo $list_header['tip'] ?>" required value="" autocomplete="off">
					<a href="javascript:void(0)" class="zhuige-btn-search" title="搜索">
						<!-- 背景图图占位结构 勿删 -->
						<span></span>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="zhuige-search-bg">
		<?php
		if (!wp_is_mobile() && $list_header['bg_video']) {
		?>
			<!-- 背景视频自动循环播放 -->
			<video autoplay muted loop>
				<source src="<?php echo $list_header['bg_video'] ?>" type="video/mp4" />
			</video>
		<?php
		} else {
			if ($list_header['bg_image'] && $list_header['bg_image']['url']) {
				$list_header_background = $list_header['bg_image']['url'];
			} else {
				$list_header_background = ZHUIGE_THEME_URL . '/images/header_background.jpg';
			}
		?>
			<!-- 搜索区背景可以设置为图片或视频 -->
			<img src="<?php echo $list_header_background ?>" alt="背景" />
		<?php
		}
		?>
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20">
	<input type="hidden" class="zhuige-theme-xzdp-ss" value="<?php echo $cur_search ?>" />

	<?php $result = zhuige_theme_get_posts(0, ['ss' => $cur_search]); ?>
	<div class="container pt-20 md-9">
		<div class="zhuige-breadcrumb d-flex justify-content-between align-items-center pb-20">
			<div class="d-flex align-items-center">
				<?php zhuige_theme_breadcrumbs() ?>
			</div>
			<div>共 <?php echo $result['count']; ?> 篇文章</div>
		</div>
		<div class="row d-flex">
			<article class="md-12">
				<div class="zhuige-box zhuige-arc-list pl-20 pr-20 zhuige-list-container">
					<?php echo $result['content']; ?>
				</div>

				<!-- 数据为空 -->
				<?php
				if (!$result['content']) {
				?>
					<div class="zhuige-none-tips">
						<img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt=" " />
						<p>暂无数据，随便逛逛...</p>
					</div>
				<?php } ?>
			</article>
		</div>
	</div>

	<?php if ($result['more']) { ?>
		<div class="zhuige-list-more d-flex justify-content-center mt-20">
			<a href="javascript:void(0)" class="zhuige-more-btn" title="更多">加载更多</a>
		</div>
	<?php } ?>

</div>

<?php get_footer(); ?>