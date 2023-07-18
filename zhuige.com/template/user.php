<?php
/*
Template Name: 追格-用户主页
*/

if (!defined('ABSPATH')) {
	exit;
}

global $wp_query;
$user_id = '';
$user_slug = isset($wp_query->query_vars['user_slug']) ? $wp_query->query_vars['user_slug'] : '';
if ($user_slug) {
	$user = get_user_by('slug', $user_slug);
	if ($user) {
		$user_id = $user->ID;
	} else {
		wp_redirect(home_url());
		exit;
	}
}

$track = isset($wp_query->query_vars['track']) ? $wp_query->query_vars['track'] : '';

$my_user_id = get_current_user_id();
if (!$user_id) {
	if ($my_user_id) {
		$user_id = $my_user_id;
	} else {
		wp_redirect(home_url());
		exit;
	}
}
$is_my_site = ($user_id == $my_user_id);


$avatar = zhuige_user_avatar($user_id);
$nickname = get_user_meta($user_id, 'nickname', true);
$cover = get_user_meta($user_id, 'zhuige_theme_cover', true);
if (empty($cover)) {
	$cover = ZHUIGE_THEME_URL . '/images/placeholder.png';;
}

get_header();
?>

<div class="zhuige-advert zhuige-advert-about relative">
	<div class="zhuige-advert-info container d-flex d-flex justify-content-center">
		<div class="md-10 d-flex align-items-center justify-content-between">
			<div class="zhuige-user d-flex align-items-center mb-30">
				<div class="user-avatar mr-10">
					<?php echo $avatar; ?>
				</div>
				<div class="user-info">
					<h6 class="d-flex align-items-center mb-10">
						<a href="javascript:void(0)"><?php echo $nickname; ?></a>
					</h6>
					<!-- 用户简介 -->
					<p><?php echo zhuige_theme_user_sign($user_id) ?></p>
				</div>
			</div>

			<div class="user-act d-flex">
				<!-- <a href="#">+ 关注</a>
				<a href="#">联系TA</a> -->
			</div>
		</div>
	</div>
	<div class="zhuige-advert-bg absolute">
		<img src="<?php echo $cover ?>" alt="用户主页背景" />
	</div>
</div>

<div class="zhuige-page-menu container d-flex justify-content-center mb-20">
	<div class="md-9">
		<div class=" zhuige-box p-20 d-flex align-items-center justify-content-center">
			<a class="<?php echo $track == 'like' ? 'active' : '' ?>" href="<?php echo '/user/like/' . $user_slug . '.html' ?>" title="">点赞</a>
			<a class="<?php echo $track == 'favorite' ? 'active' : '' ?>" href="<?php echo '/user/favorite/' . $user_slug . '.html' ?>" title="">收藏</a>
			<a class="<?php echo $track == 'comment' ? 'active' : '' ?>" href="<?php echo '/user/comment/' . $user_slug . '.html' ?>" title="">评论</a>
		</div>
	</div>
</div>

<div class="container pl-20 pr-20">
	<div class="d-flex justify-content-center">

		<article class="zhuige-user-page-list md-9">
			<?php
			if ($track == 'comment') {
				$result = zhuige_theme_posts_user_comment($user_id, 0);
				echo '<div class="zhuige-box row">';
				echo $result['content'];
				echo '</div>';
			} else {
				$result = zhuige_theme_posts_user_like_fav($user_id, 0, $track);
				echo '<div class="zhuige-fourfold-list zhuige-resource-triple row d-flex flex-wrap mb-20 zhuige-list-container">';
				echo $result['content'];
				echo '</div>';
			}
			?>

			<?php
			if (empty($result['content'])) {
			?>
				<div class="main-cont-block mb-20">
					<div class="row zhuige-none-tips">
						<img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt=" " />
						<p>暂无数据，随便逛逛...</p>
					</div>
				</div>
			<?php
			}
			?>
		</article>

		<?php if ($result['more']) { ?>
			<div class="zhuige-list-more d-flex justify-content-center mt-20">
				<a href="javascript:void(0)" class="zhuige-user-more-btn" data-track="<?php echo $track ?>" data-user_id="<?php echo $user_id ?>" title="更多">加载更多</a>
			</div>
		<?php } ?>

	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('.zhuige-btn-wx-code').click(function() {
			layer.open({
				type: 1,
				title: false,
				closeBtn: 0,
				skin: 'layui-layer-nobg', //没有背景色
				shadeClose: true,
				content: '<img src="' + $(this).data('wx_code') + '">'
			});
		});

		$('.zhuige-user-more-btn').click(function() {
			console.log($('.zhuige-post-for-ajax-count').length);
			var loading = layer.load();
			$.post("/wp-admin/admin-ajax.php", {
				action: "zhuige_theme_event",
				zgaction: 'get_posts_user',
				offset: $('.zhuige-post-for-ajax-count').length,
				track: $(this).data('track'),
				user_id: $(this).data('user_id'),
			}, (res) => {
				layer.close(loading);

				if (res.success) {
					$('.zhuige-list-container').append(res.data.content);

					if (!res.data.more) {
						$('.zhuige-user-more-btn').hide();
					}
				}
			});
		})
	});
</script>

<?php get_footer(); ?>