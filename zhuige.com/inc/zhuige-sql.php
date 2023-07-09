<?php

if (!defined('ABSPATH')) {
    exit;
}

function zhuige_theme_sql_build()
{
    global $wpdb;

    $charset_collate = '';
    if (!empty($wpdb->charset)) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    }

    if (!empty($wpdb->collate)) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //点赞过的文章
    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
    $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
        `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
        `time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);

    //收藏的文章
    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_favorite';
    $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
        `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
        `time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);
}

add_action('after_switch_theme', 'zhuige_theme_sql_build');
