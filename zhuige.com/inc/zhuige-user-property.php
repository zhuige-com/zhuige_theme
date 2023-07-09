<?php

/**
 * 追格小站点评主题
 */

if (!class_exists('ZhuiGe_Theme_User_Property')) {
	/**
	 * add field to user profiles
	 */
	class ZhuiGe_Theme_User_Property
	{
		public function __construct()
		{
			add_action('show_user_profile', array($this, 'edit_user_profile'));
			add_action('edit_user_profile', array($this, 'edit_user_profile'));

			add_action('personal_options_update', array($this, 'edit_user_profile_update'));
			add_action('edit_user_profile_update', array($this, 'edit_user_profile_update'));
		}

		public function edit_user_profile($profileuser)
		{
?>
			<h3>追格属性</h3>

			<table class="form-table">
				<tr>
					<th><label for="zhuige-btn-select-image">用户头像</label></th>
					<td style="width: 64px;" valign="top">
						<?php
						$avatar = get_user_meta($profileuser->ID, 'zhuige_user_avatar', true);
						if (empty($avatar)) {
							$avatar = ZHUIGE_THEME_URL . '/images/avatar.png';
						}
						echo '<img id="zhuige-img-user-avatar" alt="picture loss" src="' . $avatar . '" width="64" height="64" />';
						?>
					</td>
					<td>
						<input id="zhuige-btn-select-image" type="button" class="button" value="选择图片" />
						<input id="zhuige-btn-reset-image" data-default="<?php echo ZHUIGE_THEME_URL . '/images/avatar.png' ?>" type="button" class="button" value="恢复默认头像" />
						<input id="zhuige-user-avatar-value" type="hidden" name="zhuige-user-avatar" value="<?php echo get_user_meta($profileuser->ID, 'zhuige_user_avatar', true); ?>" class="regular-text" /><br />
					</td>
				</tr>
			</table>
<?php
		}

		public function edit_user_profile_update($user_id)
		{
			$new_user_avatar = isset($_POST['zhuige-user-avatar']) ? $_POST['zhuige-user-avatar'] : '';
			if ($new_user_avatar) {
				update_user_meta($user_id, 'zhuige_user_avatar', $new_user_avatar);
			}
		}
	}
}

if (!isset($ZhuiGe_Theme_User_Property)) {
	$zhuige_theme_user_property = new ZhuiGe_Theme_User_Property;
}
