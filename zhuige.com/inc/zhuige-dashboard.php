<?php
/**
 * 追格主题
 */

if (!defined('ABSPATH')) {
	exit;
}

function zhuige_theme_custom_dashboard()
{
	$content = '欢迎使用追格主题! <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.zhuige.com/product" target="_blank">更多免费产品</a>';
	$res = wp_remote_get("https://www.zhuige.com/api/ad/wordpress?id=zhuige_theme", ['timeout' => 1, 'sslverify' => false]);
	if (!is_wp_error($res) && $res['response']['code'] == 200) {
		$data = json_decode($res['body'], TRUE);
		if ($data['code'] == 1) {
			$content = $data['data'];
		}
	}

	echo $content;
}

function zhuige_theme_add_dashboard_widgets()
{
	wp_add_dashboard_widget('zhuige_theme_dashboard_widget', '追格主题', 'zhuige_theme_custom_dashboard');
}

add_action('wp_dashboard_setup', 'zhuige_theme_add_dashboard_widgets');
