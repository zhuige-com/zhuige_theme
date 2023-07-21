<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();

global $wp_query;
$query_obj = $wp_query->get_queried_object();

$title = '';
$description = '';

$cur_cat_id = '';
if (is_category()) {
	$cur_cat_id = $query_obj->term_id;
	$title = $query_obj->name;
	$description = $query_obj->description;
}

$cur_tag_id = '';
if (is_tag()) {
	$cur_tag_id = $query_obj->term_id;
	$title = $query_obj->name;
	$description = $query_obj->description;
}

$cur_author_id = '';
if (is_author()) {
	$cur_author_id = $query_obj->ID;
}

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
		$list_header_background = '';
		if (is_category()) {
			$options = get_term_meta($cur_cat_id, 'zhuige_category_options', true);
			if (isset($options['cover']) && $options['cover']['url']) {
				$list_header_background = $options['cover']['url'];
			}
		}

		if (empty($list_header_background)) {
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
		} else { ?>

			<img src="<?php echo $list_header_background ?>" alt="背景" />

		<?php }
		?>
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20">

	<input type="hidden" class="zhuige-theme-cat" value="<?php echo $cur_cat_id ?>" />
	<input type="hidden" class="zhuige-theme-tag" value="<?php echo $cur_tag_id ?>" />
	<input type="hidden" class="zhuige-theme-tag" value="<?php echo $cur_tag_id ?>" />

	<!-- container md-12用于控制页面宽度，各类列表md-会有差异 -->
	<div class="container pt-20 md-12">
		<?php
		$result = zhuige_theme_get_posts(0, ['cat' => $cur_cat_id, 'tag' => $cur_tag_id, 'author' => $cur_author_id]);
		?>
		<!-- 面包屑导航 宽度跟随 container 其他宽度和列表类不在展示 -->
		<div class="zhuige-breadcrumb d-flex justify-content-center pb-20">
			<div class="row d-flex md-9 justify-content-between align-items-center">
				<div class="d-flex align-items-center">
					<?php zhuige_theme_breadcrumbs() ?>
				</div>
				<div>共 <?php echo $result['count']; ?> 篇文章</div>
			</div>
		</div>
		<div class="row d-flex justify-content-center">
			<article class="md-9">
				<!-- 分类列表样式-1（非全宽单列）  ps:搜索、标签 列表用本列表样式  -->
				<div class="zhuige-box zhuige-arc-list pl-20 pr-20" class="zhuige-list-container"><!-- 白背景基础块 -->
					<?php echo $result['content']; ?>
				</div>

				<!-- 数据为空 -->
				<?php
				if (!$result['content']) {
				?>
					<div class="main-cont-block p-20 mb-20">
						<div class="zhuige-none-tips">
							<img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt=" " />
							<p>暂无数据，随便逛逛...</p>
						</div>
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