<?php

/**
 * 追格主题
 * 作者: 追格
 */

class ZhuiGe_Theme_Addon
{

	/**
	 * 已启用的插件
	 */
	public static $addons = [];

	/**
	 * 已安装的插件
	 */
	public static $install_addons = [];

	/**
	 * 获取配置
	 */
	public static function load()
	{
		$filePath = ZHUIGE_THEME_ADDONS_DIR . 'addons.json';
		if (!file_exists($filePath)) {
			return;
		}

		$content = file_get_contents($filePath);

		$addons = json_decode($content, true);

		if (isset($addons['addon'])) {
			ZhuiGe_Theme_Addon::$addons = $addons['addon'];
		}
	}

	/**
	 * 保存配置
	 */
	public static function save()
	{
		$content = json_encode([
			'addon' => ZhuiGe_Theme_Addon::$addons,
		]);

		file_put_contents(ZHUIGE_THEME_ADDONS_DIR . 'addons.json', $content);
	}

	/**
	 * 启用插件
	 */
	public static function active($addon)
	{
		$filePath = ZHUIGE_THEME_ADDONS_DIR . $addon . '/config.json';
		if (!file_exists($filePath)) {
			return;
		}

		$content = file_get_contents($filePath);

		$config = json_decode($content, true);

		if (!in_array($addon, ZhuiGe_Theme_Addon::$addons)) {
			array_push(ZhuiGe_Theme_Addon::$addons, $addon);
		}

		if (isset($config['sql'])) {
			global $wpdb;

			$charset_collate = '';
			if (!empty($wpdb->charset)) {
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			}

			if (!empty($wpdb->collate)) {
				$charset_collate .= " COLLATE {$wpdb->collate}";
			}

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


			// TODO 创建相应的数据库表
		}

		ZhuiGe_Theme_Addon::save();
	}

	/**
	 * 关闭插件
	 */
	public static function deactive($addon)
	{
		$filePath = ZHUIGE_THEME_ADDONS_DIR . $addon . '/config.json';
		if (!file_exists($filePath)) {
			return;
		}

		ZhuiGe_Theme_Addon::$addons = ZhuiGe_Theme_Addon::minus(ZhuiGe_Theme_Addon::$addons, [$addon]);

		ZhuiGe_Theme_Addon::save();
	}

	/**
	 * 判断某个插件是否已安装并激活
	 */
	public static function is_active($addon)
	{
		return in_array($addon, ZhuiGe_Theme_Addon::$addons);
	}

	/**
	 * 两个数组相减
	 */
	public static function minus($a, $b)
	{
		if (!is_array($a) || !is_array($b)) {
			return [];
		}

		$res = [];
		foreach ($a as $item) {
			if (!in_array($item, $b)) {
				$res[] = $item;
			}
		}

		return $res;
	}

	/**
	 * 是否已安装
	 */
	public static function is_installed($test)
	{
		if (empty($install_addons)) {
			$addons = scandir(ZHUIGE_THEME_ADDONS_DIR);
			foreach ($addons as $addon) {
				if ($addon == '.' || $addon == '..' || !is_dir(ZHUIGE_THEME_ADDONS_DIR . $addon)) {
					continue;
				}

				$install_addons[] = $addon;
			}
		}

		return in_array($test, $install_addons);
	}
}
