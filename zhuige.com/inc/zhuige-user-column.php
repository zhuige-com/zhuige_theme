<?php

/**
 * 追格小站点评主题
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter('manage_users_columns', 'zhuige_theme_manage_user_columns', 10, 2);
add_action('manage_users_custom_column', 'zhuige_theme_manage_user_custom_columnns', 10, 3);

function zhuige_theme_manage_user_columns($columns)
{
	unset($columns['name']);

	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['username'] = $columns['username'];
	$new_columns['zgnickname'] = '昵称';
	// $new_columns['zgcertify'] = '追格认证';

	unset($columns['cb']);
	unset($columns['username']);

	return array_merge($new_columns, $columns);
}

function zhuige_theme_manage_user_custom_columnns($value, $column_name, $user_id)
{
	if ('zgnickname' == $column_name) {
		$value = get_user_meta($user_id, 'nickname', true);
	}

	return $value;
}

add_filter('get_avatar', 'zhuige_theme_get_avatar', 10, 2);
function zhuige_theme_get_avatar($avatar, $id_or_email, $size = 96, $default = '', $alt = '', $args = null)
{
	$zg_avatar = get_user_meta($id_or_email, 'zhuige_user_avatar', true);
	if (!$zg_avatar) {
		$zg_avatar = ZHUIGE_THEME_URL . '/images/avatar.png';
	}

	return "<img src='$zg_avatar' class='avatar avatar-32 photo' height='32' width='32'>";
}
