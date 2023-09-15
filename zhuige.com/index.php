<?php

if (!defined('ABSPATH')) {
	exit;
}

get_header();

$home_header = zhuige_theme_option('home_header');
if (!$home_header) {
	$home_header = [
		'bg_image' => '',
		'bg_video' => '',
		'title' => '追格主题',
		'slogons' => '',
		'tip' => '搜索原来如此简单...',
		'hot_words' => '追格'
	];
}
$hot_words = explode(',', $home_header['hot_words']);

?>

<!-- 自定义搜索框大标题、视频+图片 -->
<div class="zhuige-base-block relative">
	<div class="zhuige-search-box multistage-search absolute">
		<div class="container">
			<!--主搜索区-->
			<div class="zhuige-main-search relative">
				<h1><?php echo $home_header['title'] ?></h1>
				<!-- 打字机效果 -->
				<div class="zhuige-type-text d-flex justify-content-center align-items-center p-30">
					<div id="type-box"></div>
				</div>
				<!-- 二级 -->
				<!-- <ol class="zhuige-search-multistage absolute">
					<li>
						<a href="">全部</a>
					</li>
					<li>
						<a href="">资讯</a>
					</li>
					<li>
						<a href="">其他模块内容</a>
					</li>
				</ol> -->

				<div class="zhuige-search-form">
					<!--<cite>全部</cite>-->
					<input type="search" class="input-keyword" placeholder="<?php echo $home_header['tip'] ?>" required value="" autocomplete="off">
					<a href="javascript:void(0)" class="zhuige-btn-search" title="搜索">
						搜索
					</a>
				</div>
				<div class="zhuige-search-key justify-content-center d-flex mt-30">
					<?php foreach ($hot_words as $hot_word) {
						if (!empty($hot_word)) {
							echo '<a href="' . home_url('/?s=' . $hot_word) . '">' . $hot_word . '</a>';
						}
					} ?>
				</div>
			</div>
		</div>
	</div>
	<div class="zhuige-search-bg">
		<?php
		if (!wp_is_mobile() && $home_header['bg_video']) {
		?>
			<!-- 背景视频自动循环播放 -->
			<video autoplay muted loop>
				<source src="<?php echo $home_header['bg_video'] ?>" type="video/mp4" />
			</video>
		<?php
		} else {
			if ($home_header['bg_image'] && $home_header['bg_image']['url']) {
				$home_header_background = $home_header['bg_image']['url'];
			} else {
				$home_header_background = ZHUIGE_THEME_URL . '/images/header_background.jpg';
			}
		?>
			<!-- 搜索区背景可以设置为图片或视频 -->
			<img src="<?php echo $home_header_background ?>" alt="背景" />
		<?php
		}
		?>
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20 pt-20">
	<div class="container">
		<div class="row d-flex flex-wrap">
			<!-- 左侧大列表区 -->
			<article class="md-9">

				<!-- 热门资讯 -->
				<div class="zhuige-box p-20 mb-20 zhuige-list-container">
					<h1 class="d-flex align-items-center pb-20 pt-10 justify-content-between flex-nowrap-md flex-wrap-xs">
						<text>热门资讯</text>
						<p class="zhuige-list-type">
							<a class="active" href="<?php echo home_url() ?>" title="">全部</a>
							<?php
							$cat_ids = zhuige_theme_option('home_cat_show');
							if (is_array($cat_ids)) {
								foreach ($cat_ids as $cat_id) {
									$term = get_term($cat_id);
							?>
									<a class="zhuige-home-cat-tab <?php echo ($query_obj && ($cat_id == $query_obj->term_id)) ? 'active' : '' ?>" href="javascript:void(0)" data-cat_id="<?php echo $cat_id ?>" title="<?php echo $term->name ?>">
										<?php echo $term->name ?>
									</a>
							<?php
								}
							}
							?>
						</p>
					</h1>

					<?php
					echo zhuige_theme_get_sticky_posts();

					$page_count = zhuige_theme_option('home_page_count', 10);
					$result = zhuige_theme_get_posts(0, ['page_count' => $page_count]);
					echo $result['content'];
					?>

					<input type="hidden" class="zhuige-theme-cat" value="" />
					<input type="hidden" class="zhuige-theme-page-count" value="<?php echo $page_count ?>" />

					<!-- 数据为空 -->
					<div class="main-cont-block p-20 mb-20 zhuige-theme-no-data" style="display:none;">
						<div class="zhuige-none-tips">
							<img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt="not found" />
							<p>暂无数据，随便逛逛...</p>
						</div>
					</div>
				</div>

				<?php if ($result['more']) { ?>
					<div class="zhuige-list-more d-flex justify-content-center mt-20 mb-20">
						<a href="javascript:void(0)" class="zhuige-more-btn" title="更多">加载更多</a>
					</div>
				<?php } ?>
			</article>

			<!-- 右侧边栏 -->
			<aside class="md-3">
				<?php $sticky_top = 'position: sticky; top: ' . (zhuige_theme_is_show_admin_bar() ? 114 : 82) . 'px;'; ?>
				<div style="<?php echo $sticky_top ?>">
					<!-- 猜你喜欢 -->
					<?php
					$home_right_news = zhuige_theme_option('home_right_news');
					if (!is_array($home_right_news)) {
						$home_right_news = [
							'count' => 4,
							'title' => '最新新闻',
							'ids' => []
						];
					}

					$query = new WP_Query();
					$result = $query->query([
						'post__in' => $home_right_news['ids'],
						'orderby' => 'post__in',
						'ignore_sticky_posts' => 1
					]);
					?>

					<div class="zhuige-box p-20 mb-20">
						<h5 class="d-flex align-items-baseline justify-content-between">
							<text><?php echo $home_right_news['title'] ?></text>
						</h5>

						<?php
						foreach ($result as $post) {
							$item = zhuige_theme_format_post($post, true);
						?>
							<div class="zhuige-list align-items-center d-flex">
								<!-- 封面图/头像 -->
								<div class="zhuige-list-img relative">
									<a class="zhuige-list-cover" href="<?php echo $item['link'] ?>">
										<img alt="cover" src="<?php echo $item['thumb'] ?>">
									</a>
								</div>
								<!-- 文本 -->
								<div class="zhuige-list-text">
									<h5 class="text-title set-top mb-10">
										<a href="<?php echo $item['link'] ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
									</h5>
									<div class="text-info">
										<div class="data-info d-flex align-items-center">
											<text><?php echo get_the_time('Y-m-d', $post) ?></text>
											<text>浏览 <?php echo $item['view_count'] ?></text>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>

					<!-- 热门标签 -->
					<?php
					$home_right_tags = zhuige_theme_option('home_right_tags');
					if (!is_array($home_right_tags)) {
						$home_right_tags = [
							'count' => 6,
							'title' => '热门标签'
						];
					}
					$tags_list = get_terms([
						'taxonomy' => 'post_tag',
						'number' => $home_right_tags['count'],
						'orderby' => 'count',
						'order' => 'DESC'
					]);
					?>
					<div class="zhuige-box p-20 mb-20">
						<h5 class="d-flex align-items-baseline justify-content-between">
							<text><?php echo $home_right_tags['title'] ?></text>
							<a href="<?php echo home_url('/tags') ?>" title="">查看更多</a>
						</h5>
						<div class="zhuige-side-tags mt-10 d-flex flex-wrap">
							<?php foreach ($tags_list as $tag) { ?>
								<a href="<?php echo get_tag_link($tag) ?>" title="<?php echo $tag->name ?>">
									<span><?php echo $tag->name ?></span>
									<cite><?php echo $tag->count ?>篇文章</cite>
								</a>
							<?php } ?>
						</div>
					</div>

					<!-- 单图广告 -->
					<?php
					$home_right_ad = zhuige_theme_option('home_right_ad');
					if (is_array($home_right_ad)) {
						foreach ($home_right_ad as $item) {
					?>
							<div class="zhuige-single-img mb-20">
								<a href="<?php echo $item['link'] ?>" target="_blank" title="单图广告">
									<img alt="" src="<?php echo $item['image']['url'] ?>">
								</a>
							</div>
					<?php
						}
					}
					?>
				</div>
			</aside>
		</div>
	</div>
</div>

<?php
if (!empty($home_header['slogons'])) {
	$slogons = explode(',', $home_header['slogons']);
?>
	<script>
		<?php echo 'var slogons = ' . json_encode($slogons) . ';' ?>
		var boxObj = document.getElementById('type-box');
		if (boxObj && Typed) {
			new Typed(boxObj, {
				// 注意：输出的可以是标签，将标签当节点运行。
				// 比如下面的“我是打印文字内容可”以放入h4调整字号，颜色等
				// 第一个显示的是打字内容初始显示，后面的是删除后替换的，最后一个是动画后不变的
				// 后面重复的文本内容程序会不做删除处理，如2，3里的“追格-”
				strings: slogons,
				typeSpeed: 120, // 可调节速度
				loop: true
			});
		}
	</script>
<?php
}
?>

<?php get_footer(); ?>