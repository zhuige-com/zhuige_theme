<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();

if (have_posts()) :
	the_post();

	global $post;
	zhuige_theme_inc_post_view($post->ID);
	$view_count = zhuige_theme_get_view_count($post->ID);

	global $wpdb;

	$my_user_id = get_current_user_id();

	$table_post_favorite = $wpdb->prefix . 'zhuige_theme_post_favorite';
	$favorite_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) FROM `$table_post_favorite` WHERE `post_id`=%d", $post->ID));

	$table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
	$users = $wpdb->get_results($wpdb->prepare("SELECT `user_id` FROM `$table_post_like` WHERE `post_id`=%d", $post->ID));
	$like_count = count($users);
endif;
?>

<!-- 主内容区 -->
<div class="main-body mb-20 pt-30 header-fix">
	<div class="container">
		<div class="row d-flex justify-content-center">

			<!-- 左侧边栏 -->
			<aside class="md-1 pt-20">

				<!--操作图标 -->
				<?php $sticky_top = 'position: sticky; top: ' . (zhuige_theme_is_show_admin_bar() ? 114 : 82) . 'px;'; ?>
				<div class="zhuige-opt-side mt-30" style="<?php echo $sticky_top ?>">
					<ul>
						<li class="<?php echo zhuige_theme_is_favorite($my_user_id, $post->ID) ? 'active' : '' ?>">
							<span><?php echo $favorite_count ?></span>
							<a class="zhuige-btn-favorite" href="javascript:void(0)" data-post_id="<?php echo $post->ID ?>">收藏</a>
						</li>
						<li class="<?php echo zhuige_theme_is_like($my_user_id, $post->ID) ? 'active' : '' ?>">
							<span><?php echo $like_count ?></span>
							<a class="zhuige-btn-like" href="javascript:void(0)" data-post_id="<?php echo $post->ID ?>">点赞</a>
						</li>
						<li>
							<span><?php echo $post->comment_count; ?></span>
							<a class="zhuige-btn-to-comment" href="javascript:void(0)">评论</a>
						</li>
					</ul>
				</div>

			</aside>


			<!-- 内容区 -->
			<article class="main-cont md-9">

				<!-- 面包屑 -->
				<div class="zhuige-cooky mb-30 pl-20 pr-20">
					<div class="base-list-nav">
						<?php zhuige_theme_breadcrumbs() ?>
					</div>
				</div>

				<!-- 内容区 -->
				<div class="zhuige-box p-20 mb-20">

					<!-- 标题块 -->
					<div class="zhuige-view-title relative mt-10 mb-30">
						<!-- 右侧解锁块 -->
						<!-- <span>已解锁</span> -->
						<h1><?php the_title() ?></h1>
						<div class="data-info d-flex align-items-center mb-30 mt-30">
							<text><?php the_category('&gt; '); ?></text>
							<text><?php echo zhuige_theme_time_ago($post->post_date_gmt) ?></text>
							<text>浏览 <?php echo $view_count ?></text>
						</div>
					</div>

					<!-- 文章内容 -->
					<div class="zhuige-view-article">
						<?php the_content() ?>
					</div>

					<!-- 标签 -->
					<div class="zhuige-view-tags">
						<h6 class="p-20">- END -</h6>
						<div class="d-flex align-items-center align-items-center flex-wrap pb-20 mb-20">
							<?php the_tags('', '', '') ?>
						</div>
					</div>

					<!-- 用户信息 -->
					<div class="zhuige-poster d-flex justify-content-between align-items-center p-20 mb-30">
						<div class="zhuige-user d-flex align-items-center">
							<div class="user-avatar">
								<a href="<?php echo zhuige_theme_user_site($post->post_author); ?>">
									<?php echo zhuige_user_avatar($post->post_author); ?>
								</a>
							</div>
							<div class="user-info">
								<h6 class="d-flex align-items-center mb-10">
									<a href="<?php echo zhuige_theme_user_site($post->post_author); ?>"><?php echo get_user_meta($post->post_author, 'nickname', true); ?></a>
									<!-- vip 图标 -->
									<!-- <img src="./images/vip.png" alt="vip" /> -->
								</h6>
								<p><?php echo zhuige_theme_user_sign($post->post_author); ?></p>
							</div>
						</div>
						<!-- <span>
							<a href="#" title="">+ 关注/已关注</a>
						</span> -->
					</div>

					<!-- 点赞 -->
					<div class="zhuige-praise-list">
						<h6>- <?php echo $like_count ?>人点赞 -</h6>
						<ul class="d-flex flex-wrap justify-content-center align-items-center p-20">
							<?php
							foreach ($users as $user) {
								$nickname = get_user_meta($user->user_id, 'nickname', true);
							?>
								<li>
									<a href="<?php echo zhuige_theme_user_site($user->user_id) ?>" title="<?php echo $nickname ?>">
										<?php echo zhuige_user_avatar($user->user_id) ?>
									</a>
								</li>
							<?php
							}
							?>
						</ul>
					</div>

				</div>

				<!-- 操作图标 h5 -->
				<div class="zhuige-opt-side zhuige-opt-mobile mb-20">
					<ul>
						<li class="<?php echo zhuige_theme_is_favorite($my_user_id, $post->ID) ? 'active' : '' ?>">
							<span><?php echo $favorite_count ?></span>
							<a class="zhuige-btn-favorite" href="javascript:void(0)" data-post_id="<?php echo $post->ID ?>">收藏</a>
						</li>
						<li class="<?php echo zhuige_theme_is_like($my_user_id, $post->ID) ? 'active' : '' ?>">
							<span><?php echo $like_count ?></span>
							<a class="zhuige-btn-like" href="javascript:void(0)" data-post_id="<?php echo $post->ID ?>">点赞</a>
						</li>
						<li>
							<span><?php echo $post->comment_count; ?></span>
							<a class="zhuige-btn-to-comment" href="javascript:void(0)">评论</a>
						</li>
					</ul>
				</div>

				<!-- 上下篇 -->
				<?php
				$prev_post = get_previous_post();
				$is_prev_post = is_a($prev_post, 'WP_Post');
				$next_post = get_next_post();
				$is_next_post = is_a($next_post, 'WP_Post');
				if ($is_prev_post || $is_next_post) :
				?>
					<div class="zhuige-box d-flex align-items-center justify-content-between p-20 mb-20">

						<div class="zhuige-next">
							<?php if ($is_prev_post) : ?>
								<p>
									<a href="<?php echo get_permalink($prev_post->ID); ?>" title="上一篇">上一篇</a>
								</p>
								<h5>
									<a href="<?php echo get_permalink($prev_post->ID); ?>" title="<?php echo $prev_post->post_title; ?>"><?php echo $prev_post->post_title; ?></a>
								</h5>
							<?php endif; ?>
						</div>

						<div class="zhuige-next">
							<?php if ($is_next_post) : ?>
								<p>
									<a href="<?php echo get_permalink($next_post->ID); ?>" title="下一篇">下一篇</a>
								</p>
								<h5>
									<a href="<?php echo get_permalink($next_post->ID); ?>" title="<?php echo $next_post->post_title; ?>"><?php echo $next_post->post_title; ?></a>
								</h5>
							<?php endif; ?>
						</div>

					</div>
				<?php endif; ?>

				<!-- 相关推荐 -->
				<?php
				$args = array(
					'post__not_in' => [$post->ID],
					'ignore_sticky_posts' => 1,
					'orderby' => 'comment_date',
					'posts_per_page' => 4
				);
				$posttags = get_the_tags();
				if ($posttags) {
					$tags = '';
					foreach ($posttags as $tag) {
						$tags .= $tag->term_id . ',';
					}
					$args['tag__in'] = explode(',', $tags);
				}
				$query = new WP_Query();
				$result = $query->query($args);
				if (count($result) > 0) {
				?>
					<div class="zhuige-recommend">
						<h2>相关推荐</h2>
						<div class="zhuige-recom-list d-flex flex-wrap">

							<?php
							foreach ($result as $item) {
								$item = zhuige_theme_format_post($item, true);
							?>
								<div class="zhuige-list zhuige-box p-20 align-items-center d-flex">
									<!-- 封面图 -->
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
												<text><?php echo $item['time'] ?></text>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

						</div>
					</div>
				<?php
				}
				?>

				<!-- 评论 -->
				<?php comments_template(); ?>

			</article>

			<!-- 右侧边栏 -->
			<aside class="md-2 pt-20 zhuige-menu-aside" style="display:none;">

				<!--文章目录 -->
				<div class="zhuige-view-menu mt-30" style="<?php echo $sticky_top ?>">
					<h2 class="pb-20">文章目录</h2>
					<ol class="pl-20">

					</ol>
				</div>

			</aside>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('.zhuige-btn-to-comment').click(function() {
			$([document.documentElement, document.body]).animate({
				scrollTop: $('#zhuige-comment-container').offset().top - 100
			})
		});
	});
</script>

<?php get_footer(); ?>