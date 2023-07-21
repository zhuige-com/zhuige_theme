<?php
if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<article class="zhuige-fof d-flex align-items-center justify-content-center">
	<div>
		<p class="mb-20"><img src="<?php echo ZHUIGE_THEME_URL . '/images/404.jpg' ?>" alt="找不到页面"></p>
		<div>
			<h6 class="mb-20">这里好像什么也没有...</h6>
			<p>您所查看的页面不存在，返回首页看看其他的吧</p>
		</div>
	</div>
</article>

<?php wp_footer(); ?>

<script>
	setTimeout(() => {
		window.location.href = '/';
	}, 2000)
</script>

</body>
</html>