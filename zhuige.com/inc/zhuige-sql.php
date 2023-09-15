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

    // 点赞过的文章
    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
    $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
        `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
        `time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);

    // 收藏的文章
    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_favorite';
    $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
        `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
        `time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);

    // 消费记录
    $table_spend_log = $wpdb->prefix . 'zhuige_theme_spend_log';
    $spend_log_sql = "CREATE TABLE IF NOT EXISTS `$table_spend_log` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `type` enum('post','resource','vip') DEFAULT 'post' COMMENT '类型',
        `title` varchar(100) NOT NULL COMMENT '标题',
        `amount` decimal(10,2) NOT NULL COMMENT '金额',
        `extra` varchar(100) NOT NULL COMMENT '扩展信息',
        `user_id` bigint(20) NOT NULL COMMENT '用户ID',
        `createtime` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($spend_log_sql);

}

add_action('after_switch_theme', 'zhuige_theme_sql_build');
