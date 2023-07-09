<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();


$title = zhuige_theme_option('tags_title');
$background = zhuige_theme_option('tags_bg');
if ($background && $background['url']) {
	$background = $background['url'];
} else {
	$background = ZHUIGE_THEME_URL . '/images/home_header_background.jpg';
}

$terms = get_terms(array(
	'taxonomy'   => 'post_tag',
	'hide_empty' => false,
));

?>

<!-- 标签/资源分类头部 -->
<div class="zhuige-resource-header relative">
	<div class="container p-20 zhuige-resource-header-text">
		<h2 class="mb-20"><?php echo $title ?></h2>
		<p>总计<?php echo count($terms) ?>个标签</p>
	</div>
	<div class="zhuige-resource-header-bg absolute">
		<img src="<?php echo $background ?>" alt="" />
	</div>
</div>

<!-- 主内容区 -->
<div class="main-body mb-20 pt-20">
	<div class="container">
		<article class="zhuige-tags-group zhuige-box p-20">
			<div class="row d-flex flex-wrap">

				<?php
				foreach ($terms as $term) {
				?>
					<div class="md-3 p-10">
						<div class="zhuige-tags p-20">
							<h4 class="d-flex align-items-center mb-20">
								<a href="<?php echo get_term_link($term->term_id) ?>" title=""><?php echo $term->name ?></a>
								<span><?php echo $term->count ?>篇文章</span>
							</h4>
							<p>
								<?php
								$the_query = new WP_Query(['offset' => 0, 'posts_per_page ' => 1, 'ignore_sticky_posts' => 1, 'tag_id' => $term->term_id,]);
								if ($the_query->have_posts()) {
									$the_query->the_post();
									global $post;
									echo '<a href="' . get_the_permalink() . '" title="' . $post->post_title . '">' . $post->post_title . '</a>';
								} else {
								}
								wp_reset_postdata();
								?>
							</p>
						</div>
					</div>
				<?php
				}
				?>

			</div>
		</article>
	</div>
</div>

<?php get_footer(); ?>