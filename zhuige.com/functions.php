<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once TEMPLATEPATH . '/vendor/autoload.php';

use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use GuzzleHttp\HandlerStack;

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        if ('' === $needle) {
            return true;
        }

        return 0 === strpos($haystack, $needle);
    }
}

/**
 * 追格主题
 * 文档：https://www.zhuige.com
 */

require_once TEMPLATEPATH . '/inc/codestar-framework/codestar-framework.php';
require_once TEMPLATEPATH . '/inc/admin-options.php';
require_once TEMPLATEPATH . '/inc/zhuige-market.php';
require_once TEMPLATEPATH . '/inc/zhuige-aes-util.php';
require_once TEMPLATEPATH . '/inc/zhuige-ajax.php';
require_once TEMPLATEPATH . '/inc/zhuige-dashboard.php';
require_once TEMPLATEPATH . '/inc/zhuige-user-column.php';
require_once TEMPLATEPATH . '/inc/zhuige-user-property.php';
//require_once TEMPLATEPATH . '/inc/zhuige-plugins.php';
require_once TEMPLATEPATH . '/inc/zhuige-sql.php';


define('ZHUIGE_THEME_ADDONS_DIR', TEMPLATEPATH . '/addons/');
define('ZHUIGE_THEME_URL', get_template_directory_uri());

require_once TEMPLATEPATH . '/inc/zhuige-addon.php';
ZhuiGe_Theme_Addon::load();
foreach (ZhuiGe_Theme_Addon::$addons as $addon) {
    $file_path = ZHUIGE_THEME_ADDONS_DIR . $addon . '/functions.php';
    if (file_exists($file_path)) {
        require_once($file_path);
    }
}

// ----

remove_filter('template_redirect', 'redirect_canonical');
add_action('init',  function () {

    add_rewrite_rule('^user-info$', 'index.php?zhuige_page=user-info', 'top');
    add_rewrite_rule('^user-pwd$', 'index.php?zhuige_page=user-pwd', 'top');
    add_rewrite_rule('^user-reset$', 'index.php?zhuige_page=user-reset', 'top');
    add_rewrite_rule('^user-spend-log$', 'index.php?zhuige_page=user-spend-log', 'top');

    add_rewrite_rule('^user/([^/]*)/([^/]*)\\.html$', 'index.php?zhuige_page=user&track=$matches[1]&user_slug=$matches[2]', 'top');

    add_rewrite_rule('^tags$', 'index.php?zhuige_page=tags', 'top');

    // update_option('rewrite_rules', '');
});

add_filter('query_vars', function ($query_vars) {
    $query_vars[] = 'zhuige_plugin';
    $query_vars[] = 'zhuige_page';

    $query_vars[] = 'track';
    $query_vars[] = 'user_slug';

    return $query_vars;
});

add_action('template_include', function ($template) {
    $zhuige_plugin = get_query_var('zhuige_plugin');
    $zhuige_page = get_query_var('zhuige_page');
    if ($zhuige_plugin || $zhuige_page == false || $zhuige_page == '') {
        return $template;
    }

    $page_template = TEMPLATEPATH . '/template/' . $zhuige_page . '.php';
    if (file_exists($page_template)) {
        return $page_template;
    }

    return $template;
});

// ---

/**
 * 非管理员隐藏后台入口
 */
if (!current_user_can('manage_options')) {
    add_filter('show_admin_bar', '__return_false');
}
function zhuige_theme_is_show_admin_bar()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    if (!current_user_can('manage_options')) {
        return false;
    }

    if (get_user_meta($user_id, 'show_admin_bar_front', true) == 'false') {
        return false;
    }

    return true;
}

/**
 * wp编辑器增加字体和字体大小设置
 */
function MBT_add_editor_buttons($buttons)
{
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'copy';
    $buttons[] = 'paste';
    $buttons[] = 'cut';
    $buttons[] = 'image';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    $buttons[] = 'wp_page';
    $buttons[] = 'charmap';
    return $buttons;
}
add_filter("mce_buttons_2", "MBT_add_editor_buttons");

/**
 * 切换经典小工具
 */
add_filter('gutenberg_use_widgets_block_editor', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');

/**
 * 移除图片的宽高属性
 */
add_filter('post_thumbnail_html', 'remove_width_attribute', 10);
add_filter('image_send_to_editor', 'remove_width_attribute', 10);
function remove_width_attribute($html)
{
    $html = preg_replace('/(width|height)="\d*"\s/', "", $html);
    return $html;
}

/**
 * 开启特色图功能
 */
if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
}

// 在init action处注册脚本，可以与其它逻辑代码放在一起
function zhuige_theme_init()
{
    $url = ZHUIGE_THEME_URL;

    // 注册脚本
    wp_register_script('lib-script', $url . '/js/lib/lb.js', [], '0.1');
    wp_register_script('lib-typed', $url . '/js/lib/typed.js', [], '0.1');
    wp_register_script('lib-swiper', $url . '/js/lib/swiper.min.js', [], '5.4.5');
    wp_register_script('lib-layer', $url . '/js/layer/layer.js', ['jquery'], '1.0', false);
    wp_register_script('zhuige-footer-script', $url . '/js/zhuige.footer.js', ['jquery'], '0.1', true);
    wp_register_script('zhuige-index-script', $url . '/js/zhuige.index.js', ['jquery'], '0.1', true);
    wp_register_script('zhuige-archive-script', $url . '/js/zhuige.archive.js', ['jquery'], '0.1', true);
    wp_register_script('zhuige-search-script', $url . '/js/zhuige.search.js', ['jquery'], '0.1', true);
    wp_register_script('zhuige-single-script', $url . '/js/zhuige.single.js', ['lib-layer'], '0.1', true);

    // 其它需要在init action处运行的脚本
}
add_action('init', 'zhuige_theme_init');


function zhuige_theme_scripts()
{
    //全局加载js脚本
    wp_enqueue_script('jquery');
    wp_enqueue_script('lib-script');
    wp_enqueue_script('lib-layer');
    wp_enqueue_script('zhuige-footer-script');

    if (is_archive()) {
        wp_enqueue_script('zhuige-archive-script');
    }

    if (is_search()) {
        wp_enqueue_script('zhuige-search-script');
    }

    if (is_home()) {
        $zhuige_page = get_query_var('zhuige_page');
        if (empty($zhuige_page)) {
            wp_enqueue_script('lib-typed');
            wp_enqueue_script('zhuige-index-script');
        }
    }

    if (is_single()) {
        wp_enqueue_script('zhuige-single-script');
    }
}
add_action('wp_enqueue_scripts', 'zhuige_theme_scripts');

add_action('admin_print_scripts-profile.php', 'enqueue_script_zhuige_user_property');
add_action('admin_print_scripts-user-edit.php', 'enqueue_script_zhuige_user_property');

/**
 * 加载js 用户属性编辑
 */
function enqueue_script_zhuige_user_property()
{
    wp_enqueue_media();
    wp_enqueue_script('zhuige-user-property', ZHUIGE_THEME_URL . "/js/zhuige-user-property.js", array('jquery'), '1.0.0', true);
}

/**
 *  清除谷歌字体 
 */
function jiangqie_remove_open_sans_from_wp_core()
{
    wp_deregister_style('open-sans');
    wp_register_style('open-sans', false);
    wp_enqueue_style('open-sans', '');
}
add_action('init', 'jiangqie_remove_open_sans_from_wp_core');

/**
 * 清除wp_head无用内容 
 */
function remove_dns_prefetch($hints, $relation_type)
{
    if ('dns-prefetch' === $relation_type) {
        return array_diff(wp_dependencies_unique_hosts(), $hints);
    }
    return $hints;
}
function zhuige_theme_remove_laji()
{
    remove_action('wp_head', 'wp_generator'); //移除WordPress版本
    remove_action('wp_head', 'rsd_link'); //移除离线编辑器开放接口
    remove_action('wp_head', 'wlwmanifest_link'); //移除离线编辑器开放接口
    remove_action('wp_head', 'index_rel_link'); //去除本页唯一链接信息
    remove_action('wp_head', 'feed_links', 2); //移除feed
    remove_action('wp_head', 'feed_links_extra', 3); //移除feed
    remove_action('wp_head', 'rest_output_link_wp_head', 10); //移除wp-json链
    remove_action('wp_head', 'print_emoji_detection_script', 7); //头部的JS代码
    remove_action('wp_head', 'wp_print_styles', 8); //emoji载入css
    remove_action('wp_head', 'rel_canonical'); //rel=canonical
    add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2); //头部加载DNS预获取（dns-prefetch）
}
add_action('init', 'zhuige_theme_remove_laji');


function zhuige_theme_setup()
{
    //关键字
    add_action('wp_head', 'zhuige_theme_seo_keywords');

    //页面描述 
    add_action('wp_head', 'zhuige_theme_seo_description');

    //网站图标
    add_action('wp_head', 'zhuige_theme_favicon');
}
add_action('after_setup_theme', 'zhuige_theme_setup');

add_action('admin_init', 'zhuige_theme_on_admin_init');
add_action('admin_menu', 'zhuige_theme_add_admin_menu', 20);
function zhuige_theme_add_admin_menu()
{
    add_submenu_page('zhuige-theme', '', '安装文档', 'manage_options', 'zhuige_theme_setup', 'zhuige_theme_handle_external_redirects');
    add_submenu_page('zhuige-theme', '', '更多产品', 'manage_options', 'zhuige_theme_upgrade', 'zhuige_theme_handle_external_redirects');
}

function zhuige_theme_on_admin_init()
{
    zhuige_theme_handle_external_redirects();
}

function zhuige_theme_handle_external_redirects()
{
    $page = isset($_GET['page']) ? $_GET['page'] : '';

    if ('zhuige_theme_setup' === $page) {
        wp_redirect('https://www.zhuige.com/docs/zgtheme.html');
        die;
    }

    if ('zhuige_theme_upgrade' === $page) {
        wp_redirect('https://www.zhuige.com/product.html?cat=23');
        die;
    }
}

function zhuige_theme_sanitize_user($username, $raw_username, $strict)
{
    if (!$strict)
        return $username;

    return sanitize_user(stripslashes($raw_username), false);
}
add_filter('sanitize_user', 'zhuige_theme_sanitize_user', 10, 3);

/**
 * 缩略图
 */
function zhuige_theme_thumbnail_src()
{
    global $post;
    return zhuige_theme_thumbnail_src_d($post->ID, $post->post_content);
}

function zhuige_theme_thumbnail_src_d($post_id, $post_content)
{
    $post_thumbnail_src = '';
    if (has_post_thumbnail($post_id)) {    //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
        if ($thumbnail_src) {
            $post_thumbnail_src = $thumbnail_src[0];
        }
    }

    if (empty($post_thumbnail_src)) {
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
        if ($matches && isset($matches[1]) && isset($matches[1][0])) {
            $post_thumbnail_src = $matches[1][0];   //获取该图片 src
        }
    }

    return $post_thumbnail_src;
}

/**
 * 美化时间
 */
function zhuige_theme_time_ago($ptime)
{
    $ptime = strtotime($ptime);
    $etime = time() - $ptime;
    if ($etime < 1) return '刚刚';
    $interval = array(
        12 * 30 * 24 * 60 * 60  =>  '年前 (' . wp_date('Y-m-d', $ptime) . ')',
        30 * 24 * 60 * 60       =>  '个月前 (' . wp_date('m-d', $ptime) . ')',
        7 * 24 * 60 * 60        =>  '周前 (' . wp_date('m-d', $ptime) . ')',
        24 * 60 * 60            =>  '天前',
        60 * 60                 =>  '小时前',
        60                      =>  '分钟前',
        1                       =>  '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}

/**
 * 设置项的值
 */
$zhuige_theme_options = null;
if (!function_exists('zhuige_theme_option')) {
    function zhuige_theme_option($key, $default = '')
    {
        global $zhuige_theme_options;
        if (!$zhuige_theme_options) {
            $zhuige_theme_options = get_option('zhuige-theme');
        }

        if (isset($zhuige_theme_options[$key])) {
            return $zhuige_theme_options[$key];
        }

        return $default;
    }
}

/**
 * 设置文章浏览量
 */
function zhuige_theme_inc_post_view($post_id)
{
    $view_count = (int) get_post_meta($post_id, 'view_count', true);
    if (!update_post_meta($post_id, 'view_count', ($view_count + 1))) {
        add_post_meta($post_id, 'view_count', 1, true);
    }
}

/**
 * 获取浏览数
 */
function zhuige_theme_get_view_count($post_id)
{
    $view_count = get_post_meta($post_id, "view_count", true);
    if (!$view_count) {
        $view_count = 0;
    }
    return $view_count;
}

/**
 * 点赞个数
 */
function zhuige_theme_get_like_count($post_id)
{
    global $wpdb;

    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) FROM `$table_post_like` WHERE `post_id`=%d", $post_id));
}

/**
 * 摘要
 */
function zhuige_theme_excerpt($post, $length = 50)
{
    if ($post->post_excerpt) {
        return html_entity_decode(wp_trim_words($post->post_excerpt, $length, '...'));
    } else {
        return html_entity_decode(wp_trim_words($post->post_content, $length, '...'));
    }
}

/**
 * 面包屑导航
 */
function zhuige_theme_breadcrumbs()
{
    $delimiter = '<em> > </em>'; // 分隔符
    $before = '<span class="current">'; // 在当前链接前插入
    $after = '</span>'; // 在当前链接后插入
    if (!is_home() && !is_front_page() || is_paged()) {
        echo '<div class="base-list-nav" itemscope="">' . __('', 'cmp');
        global $post;
        $homeLink = home_url() . '/';
        echo '<a itemprop="breadcrumb" href="' . $homeLink . '">' . __('首页', 'cmp') . '</a> ' . $delimiter . ' ';
        if (is_404()) { // 404 页面
            echo $before;
            _e('404', 'cmp');
            echo $after;
        } else if (is_category()) { // 分类 存档
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) {
                $cat_code = get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
                echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
            }
            echo $before . '' . single_cat_title('', FALSE) . '' . $after;
        } elseif (is_day()) { // 天 存档
            echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a itemprop="breadcrumb"  href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif (is_month()) { // 月 存档
            echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif (is_year()) { // 年 存档
            echo $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) { // 文章
            if (get_post_type() != 'post') { // 自定义文章类型
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a itemprop="breadcrumb" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } else { // 文章 post
                $cat = get_the_category();
                $cat = $cat[0];
                $cat_code = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
                // echo '<a itemprop="breadcrumb" href="/news/cat/' . $cat->term_id . '">' . $cat->name . '</a>' . $delimiter . ' ';
                echo $before . '正文' . $after;
            }
        } elseif (is_attachment()) { // 附件
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo '<a itemprop="breadcrumb" href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif (is_page() && !$post->post_parent) { // 页面
            echo $before . get_the_title() . $after;
        } elseif (is_page() && $post->post_parent) { // 父级页面
            $parent_id = $post->post_parent;
            $breadcrumbs = [];
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a itemprop="breadcrumb" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif (is_search()) { // 搜索结果
            printf(__('搜索：%s', 'cmp'), get_search_query());
        } elseif (is_tag()) { //标签 存档
            echo $before;
            printf(__('标签：%s', 'cmp'), single_tag_title('', FALSE));
            echo $after;
        } elseif (is_author()) { // 作者存档
            global $author;
            $userdata = get_userdata($author);
            echo $before;
            printf(__('作者：%s', 'cmp'), $userdata->display_name);
            echo $after;
        } elseif (!is_single() && !is_page() && get_post_type() != 'post') {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        }

        if (get_query_var('paged')) { // 分页
            if (is_category() || is_day() || is_month() || is_year()  || is_tag() || is_author())
                echo sprintf(__('( Page %s )', 'cmp'), get_query_var('paged'));
        }
        echo '</div>';
    }
}


/* ---- SEO start ---- */
/**
 * 标题
 */
function zhuige_theme_seo_title()
{
    $seo_home = zhuige_theme_option('seo_home');
    $site_title = get_bloginfo('name');
    if (is_array($seo_home) && $seo_home['title']) {
        $site_title = $seo_home['title'];
    }

    $title = $site_title;

    $zhuige_page = get_query_var('zhuige_page');
    $zhuige_plugin = get_query_var('zhuige_plugin');
    if ($zhuige_plugin) {
        if ($zhuige_plugin == 'resource') {
            if ($zhuige_page == 'index') {
                $resource_seo_home = zhuige_theme_resource_option('resource_seo_home');
                if ($resource_seo_home && $resource_seo_home['title']) {
                    $title = $resource_seo_home['title'];
                }
            } else if ($zhuige_page == 'detail') {
                $resource_id = get_query_var('resource_id');
                $post = get_post($resource_id);
                if ($post) {
                    $title = $post->post_title . '_' . $site_title;
                }
            } else if ($zhuige_page == 'cat') {
                $cat_id = get_query_var('cat_id');
                $term = get_term($cat_id, 'zt_resource_cat');
                $title = $term->name . '_' . $site_title;
            } else if ($zhuige_page == 'tag') {
                $tag_id = get_query_var('tag_id');
                $term = get_term($tag_id, 'zt_resource_tag');
                $title = $term->name . '_' . $site_title;
            } else if ($zhuige_page == 'search') {
                $search = get_query_var('search');
                $title = '搜索：' . urldecode($search) . '_' . $site_title;
            }
        } else if ($zhuige_plugin == 'vip') {
            if ($zhuige_page == 'vip') {
                $vip_seo_home = zhuige_theme_vip_option('vip_seo_home');
                if ($vip_seo_home && $vip_seo_home['title']) {
                    $title = $vip_seo_home['title'];
                }
            }
        }
    } else if (is_home()) {
        if ($zhuige_page == 'user') {
            $user_slug = get_query_var('user_slug');
            if ($user_slug) {
                $user = get_user_by('slug', $user_slug);
                if ($user) {
                    $nickname = get_user_meta($user->ID, 'nickname', true);
                    $track = get_query_var('track');
                    if ('like' == $track) {
                        $title = $nickname . '的喜欢_' . $site_title;
                    } else if ('favorite' == $track) {
                        $title = $nickname . '的收藏_' . $site_title;
                    } else if ('comment' == $track) {
                        $title = $nickname . '的评论_' . $site_title;
                    } else {
                        $title = $nickname . '_' . $site_title;
                    }
                }
            }
        } else if ($zhuige_page == 'tags') {
            $tags_title = zhuige_theme_option('tags_title', '标签聚合');
            $title = $tags_title . '_' . $site_title;
        }
    } else if (is_search()) {
        global $s;
        $title = '搜索：' . $s . '_' . $site_title;
    } else if (is_category() || is_tag()) {
        global $wp_query;
        $query_obj = $wp_query->get_queried_object();
        $title = $query_obj->name . '_' . $site_title;
    } else if (is_single()) {
        global $post;
        $title = $post->post_title . '_' . $site_title;
    } else if (is_page()) {
        global $post;
        $title = $post->post_title . '_' . $site_title;
    }

    global $page, $paged;
    if ($paged >= 2 || $page >= 2) {
        $title .= ' - ' . sprintf('第%s页', max($paged, $page));
    }

    echo $title;
}

/**
 * 关键字
 */
function zhuige_theme_seo_keywords()
{
    $keywords = '';
    $seo_home = zhuige_theme_option('seo_home');
    if (is_array($seo_home) && !empty($seo_home['keywords'])) {
        $keywords = $seo_home['keywords'];
    }

    $zhuige_page = get_query_var('zhuige_page');
    $zhuige_plugin = get_query_var('zhuige_plugin');
    if ($zhuige_plugin) {
        if ($zhuige_plugin == 'resource') {
            if ($zhuige_page == 'index') {
                $resource_seo_home = zhuige_theme_resource_option('resource_seo_home');
                if ($resource_seo_home && $resource_seo_home['keywords']) {
                    $keywords = $resource_seo_home['keywords'];
                }
            } else if ($zhuige_page == 'detail') {
                $resource_id = get_query_var('resource_id');
                $resource_tags = get_the_terms($resource_id, 'zt_resource_tag');
                if ($resource_tags) {
                    foreach ($resource_tags as $tag) {
                        $tags[] = $tag->name;
                    }
                }
                if (!empty($tags)) {
                    $keywords = implode(',', $tags);
                }
            } else if ($zhuige_page == 'cat') {
                $cat_id = get_query_var('cat_id');
                $term = get_term($cat_id, 'zt_resource_cat');
                $options = get_term_meta($cat_id, 'zhuige_category_options', true);
                $keywords = (is_array($options) && !empty($options['keywords']) ? $options['keywords'] : $term->name);
            } else if ($zhuige_page == 'tag') {
                $tag_id = get_query_var('tag_id');
                $term = get_term($tag_id, 'zt_resource_tag');
                $keywords = $term->name . ',' . $keywords;
            } else if ($zhuige_page == 'search') {
                $search = get_query_var('search');
                $keywords = urldecode($search) . ',' . $keywords;
            }
        } else if ($zhuige_plugin == 'vip') {
            if ($zhuige_page == 'vip') {
                $vip_seo_home = zhuige_theme_vip_option('vip_seo_home');
                if ($vip_seo_home && $vip_seo_home['keywords']) {
                    $keywords = $vip_seo_home['keywords'];
                }
            }
        }
    } else if (is_home()) {
        if ($zhuige_page == 'user') {
            $user_slug = get_query_var('user_slug');
            if ($user_slug) {
                $user = get_user_by('slug', $user_slug);
                if ($user) {
                    $nickname = get_user_meta($user->ID, 'nickname', true);
                    $track = get_query_var('track');
                    if ('like' == $track) {
                        $keywords = $nickname . '的喜欢,' . $keywords;
                    } else if ('favorite' == $track) {
                        $keywords = $nickname . '的收藏,' . $keywords;
                    } else if ('comment' == $track) {
                        $keywords = $nickname . '的评论,' . $keywords;
                    } else {
                        $keywords = $nickname . ',' . $keywords;
                    }
                }
            }
        }
    } else if (is_search()) {
        global $s;
        $keywords = $s . ',' . $keywords;
    } else if (is_category()) {
        global $wp_query;
        $query_obj = $wp_query->get_queried_object();
        $options = get_term_meta($query_obj->term_id, 'zhuige_category_options', true);
        $keywords = (is_array($options) && !empty($options['keywords']) ? $options['keywords'] : $query_obj->name);
    } else if (is_tag()) {
        global $wp_query;
        $query_obj = $wp_query->get_queried_object();
        $options = get_term_meta($query_obj->term_id, 'zhuige_post_tag_options', true);
        $keywords = (is_array($options) && !empty($options['keywords']) ? $options['keywords'] : $query_obj->name);
    } else if (is_singular()) {
        global $post;
        $tags = [];
        if ($post->post_type == 'post') {
            $terms = get_the_tags($post->ID);
            if ($terms) {
                foreach ($terms as $tag) {
                    $tags[] = $tag->name;
                }
            }

            $cats = get_the_category($post->ID);
            foreach ($cats as $category) {
                $tags[] = $category->cat_name;
            }

            if (!empty($tags)) {
                $keywords = implode(',', $tags);
            }
        }
    }

    if ($keywords) {
        echo "<meta name=\"keywords\" content=\"$keywords\">\n";
    }
}

/**
 * 描述
 */
function zhuige_theme_seo_description()
{
    $description = get_bloginfo('description');
    $seo_home = zhuige_theme_option('seo_home');
    if (is_array($seo_home) && !empty($seo_home['description'])) {
        $description = $seo_home['description'];
    }

    $zhuige_page = get_query_var('zhuige_page');
    $zhuige_plugin = get_query_var('zhuige_plugin');
    if ($zhuige_plugin) {
        if ($zhuige_plugin == 'resource') {
            if ($zhuige_page == 'index') {
                $resource_seo_home = zhuige_theme_resource_option('resource_seo_home');
                if ($resource_seo_home && $resource_seo_home['description']) {
                    $description = $resource_seo_home['description'];
                }
            } else if ($zhuige_page == 'detail') {
                $resource_id = get_query_var('resource_id');
                $post = get_post($resource_id);
                if ($post) {
                    $description = html_entity_decode(wp_trim_words($post->post_content, 120, '...'));
                }
            } else if ($zhuige_page == 'cat') {
                $cat_id = get_query_var('cat_id');
                $term = get_term($cat_id, 'zt_resource_cat');
                if ($term->description) {
                    $description = $term->description;
                } else {
                    $description = '分类：' . $term->name . ' 下的资源';
                }
            } else if ($zhuige_page == 'tag') {
                $tag_id = get_query_var('tag_id');
                $term = get_term($tag_id, 'zt_resource_tag');
                if ($term->description) {
                    $description = $term->description;
                } else {
                    $description = '标签：' . $term->name . ' 下的资源';
                }
            } else if ($zhuige_page == 'search') {
                $search = get_query_var('search');
                $description = '在' . get_bloginfo('name') . '搜索：' . urldecode($search) . ' 的结果';
            }
        } else if ($zhuige_plugin == 'vip') {
            if ($zhuige_page == 'vip') {
                $vip_seo_home = zhuige_theme_vip_option('vip_seo_home');
                if ($vip_seo_home && $vip_seo_home['description']) {
                    $description = $vip_seo_home['description'];
                }
            }
        }
    } else if (is_home()) {
        if ($zhuige_page == 'user') {
            $user_slug = get_query_var('user_slug');
            if ($user_slug) {
                $user = get_user_by('slug', $user_slug);
                if ($user) {
                    $nickname = get_user_meta($user->ID, 'nickname', true);
                    $track = get_query_var('track');
                    if ('like' == $track) {
                        $description = $nickname . '喜欢的文章';
                    } else if ('favorite' == $track) {
                        $description = $nickname . '收藏的文章';
                    } else if ('comment' == $track) {
                        $description = $nickname . '评论的文章';
                    }
                }
            }
        }
    } else if (is_search()) {
        global $s;
        $description = '在' . get_bloginfo('name') . '搜索：' . $s . ' 的结果';
    } else if (is_category()) {
        global $wp_query;
        $query_obj = $wp_query->get_queried_object();
        if ($query_obj->description) {
            $description = $query_obj->description;
        } else {
            $description = '分类：' . $query_obj->name . ' 下的文章';
        }
    } else if (is_tag()) {
        global $wp_query;
        $query_obj = $wp_query->get_queried_object();
        if ($query_obj->description) {
            $description = $query_obj->description;
        } else {
            $description = '标签：' . $query_obj->name . ' 下的文章';
        }
    } else if (is_single()) {
        global $post;
        $description = html_entity_decode(wp_trim_words($post->post_content, 120, '...'));
    } else if (is_page()) {
        global $post;
        $description = html_entity_decode(wp_trim_words($post->post_content, 120, '...'));
    }

    if ($description) {
        $description = mb_substr($description, 0, 220, 'utf-8');
        echo "<meta name=\"description\" content=\"$description\">\n";
    }
}
/* ---- SEO end ---- */

/**
 * 站点LOGO
 */
function zhuige_theme_logo()
{
    $logo = zhuige_theme_option('site_logo');
    if ($logo && $logo['url']) {
        echo '<img alt="picture loss" src="' . $logo['url'] . '" alt="' . get_bloginfo('name') . '" />';
    } else {
        echo '<img alt="picture loss" src="' . ZHUIGE_THEME_URL . '/images/default_logo.png' . '" alt="' . get_bloginfo('name') . '" />';
    }
}

/**
 * favicon
 */
function zhuige_theme_favicon()
{
    $favicon = zhuige_theme_option('site_favicon');
    if ($favicon && $favicon['url']) {
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . $favicon['url'] . '" />';
    } else {
        echo '';
    }
}

/**
 * 评论样式
 */
function zhuige_theme_comment_list($comment, $args, $depth)
{
?>
    <div class="zhuige-user d-flex mb-30 zhuige-comment-depth-<?php echo $depth ?>">
        <?php
        if ($comment->user_id) {
            $nickname = get_user_meta($comment->user_id, 'nickname', true);
        } else {
            $nickname = $comment->comment_author;
        }
        ?>
        <div class="user-avatar mr-10">
            <a href="<?php echo zhuige_theme_user_site($comment->user_id); ?>" title="<?php echo $nickname ?>" target="_blank">
                <?php echo zhuige_user_avatar($comment->user_id); ?>
            </a>
        </div>
        <div class="user-info">
            <h6 class="d-flex align-items-center mb-10">
                <a class="mr-10" href="<?php echo zhuige_theme_user_site($comment->user_id); ?>" title="<?php echo $nickname ?>" target="_blank">
                    <?php echo $nickname ?>
                </a>
                <?php
                $comment_parent = false;
                if ($comment->comment_parent) {
                    $comment_parent = get_comment($comment->comment_parent);
                }

                if ($comment_parent) {
                    if ($comment_parent->user_id) {
                        $nickname_parent = get_user_meta($comment_parent->user_id, 'nickname', true);
                    } else {
                        $nickname_parent = $comment_parent->comment_author;
                    }
                ?>
                    &nbsp;回复&nbsp;
                    <a href="<?php echo zhuige_theme_user_site($comment_parent->user_id); ?>" title="<?php echo $nickname_parent ?>" target="_blank">
                        <?php echo $nickname_parent ?>
                    </a>
                <?php
                } else {
                    global $post;
                    if ($post->post_type == 'zt_resource') {
                        $score = get_comment_meta($comment->comment_ID, 'zhuige_theme_resource_score', true);
                        echo zhuige_theme_reource_score_string($score);
                    }
                }
                ?>
            </h6>
            <p><?php echo get_comment_text() ?></p>
            <div class="data-info d-flex mt-10">
                <text><?php echo zhuige_theme_time_ago(get_comment_time('Y-m-d H:i:s', true)) ?></text>
                <?php if ($depth < $args['max_depth']) { ?>
                    <a href="javascript:void(0)" data-comment_id="<?php echo $comment->comment_ID ?>" data-nickname="<?php echo get_user_meta($comment->user_id, 'nickname', true) ?>" class="zhuige-comment-btn-reply" title="回复">回复</a>
                <?php } ?>
            </div>
        </div>
    </div>

<?php
}

/**
 * 追格头像
 */
function zhuige_user_avatar_src($user_id)
{
    $avatar = get_user_meta($user_id, 'zhuige_user_avatar', true);
    if (empty($avatar)) {
        $avatar = ZHUIGE_THEME_URL . '/images/avatar.png';
    }

    return $avatar;
}
function zhuige_user_avatar($user_id)
{
    $avatar = zhuige_user_avatar_src($user_id);
    return '<img alt="picture loss" src="' . $avatar . '" />';
}

/**
 * 是否喜欢
 */
function zhuige_theme_is_like($user_id, $post_id)
{
    if (!$user_id) {
        return 0;
    }

    global $wpdb;
    $table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
    return $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `$table_post_like` WHERE `user_id`=%d AND `post_id`=%d", $user_id, $post_id));
}

/**
 * 是否收藏
 */
function zhuige_theme_is_favorite($user_id, $post_id)
{
    if (!$user_id) {
        return 0;
    }

    global $wpdb;
    $table_post_favorite = $wpdb->prefix . 'zhuige_theme_post_favorite';
    return $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `$table_post_favorite` WHERE `user_id`=%d AND `post_id`=%d", $user_id, $post_id));
}

/**
 * 格式化文章列表项
 */
function zhuige_theme_format_post($post, $require_thumb = false)
{
    $item = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'link' => get_permalink($post->ID)
    ];

    $thumb = zhuige_theme_thumbnail_src_d($post->ID, $post->post_content);
    if ($require_thumb && empty($thumb)) {
        $thumb = ZHUIGE_THEME_URL . '/images/placeholder.png';
    }
    $item["thumb"] = $thumb;

    $item["excerpt"] = zhuige_theme_excerpt($post, zhuige_theme_option('home_excerpt_length', 120));

    $item['view_count'] = zhuige_theme_get_view_count($post->ID);

    $item['time'] = zhuige_theme_time_ago($post->post_date_gmt);

    $item['author'] = get_user_meta($post->post_author, 'nickname', true);

    $item['author_link'] = zhuige_theme_user_site($post->post_author);

    $item['badge'] = get_post_meta($post->ID, 'zhuige_theme_post_badge', true);

    return $item;
}

/**
 * 获取当前的URL
 */
function zhuige_theme_url()
{
    $pageURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * 用户签名
 */
function zhuige_theme_user_sign($user_id)
{
    $sign = get_user_meta($user_id, 'description', true);
    if (empty($sign)) {
        $sign = '这个用户有点懒，什么都没写~';
    }
    return $sign;
}

/**
 * 用户小站
 */
function zhuige_theme_user_site($user_id)
{
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return home_url();
    }

    return "/user/like/" . $user->user_nicename . ".html";
}

/**
 * 截取url中的模块 第一个/和第二个/之间的认为是模块
 */
function zhuige_url_module($url)
{
    $index = stripos($url, '://');
    if ($index > -1) {
        $url = substr($url, $index + strlen('://'));
    }
    // echo $url;
    $index = stripos($url, '/');
    if ($index > -1 && ($index != strlen($url) - 1)) {
        $url = substr($url, $index + strlen('/'));
    } else {
        return $url;
    }

    // echo $url;
    $index = stripos($url, '/');
    if ($index > -1) {
        $url = substr($url, 0, $index);
    }
    // echo $url;
    $index = stripos($url, '?');
    if ($index > -1) {
        $url = substr($url, 0, $index);
    }
    // echo $url;
    return $url;
}

/**
 * 格式化文章
 */
function zhuige_theme_post_string($post, $show_sticky = false)
{
    $content = '';
    $item = zhuige_theme_format_post($post);

    // 背景悬停变色
    if (is_sticky($post->ID)) {
        $content .= '<div class="zhuige-base-list">';
    } else {
        $content .= '<div class="zhuige-base-list zhuige-post-for-ajax-count">';
    }

    // 追格列表块
    $content .= '<div class="zhuige-list align-items-center d-flex pt-20 pb-20">';

    // 封面图/头像
    if ($item['thumb']) {
        $content .= '<div class="zhuige-list-img relative">';

        // 封面角标/vip
        if ($item['badge']) {
            $content .= '<div class="zhuige-list-mark absolute d-flex">';
            $content .= '<span>' . $item['badge'] . '</span>';
            $content .= '</div>';
        }

        // 封面
        $content .= '<a class="zhuige-list-cover" href="' . $item['link'] . '">';
        $content .= '<img alt="cover" src="' . $item['thumb'] . '">';
        $content .= '</a>';
        $content .= '</div>';
    }

    // 文本
    $content .= '<div class="zhuige-list-text">';
    $content .= '<h5 class="text-title set-top mb-20 mb-10-xs">';
    if ($show_sticky && is_sticky($post->ID)) {
        $content .= '<span>置顶</span>';
    }
    $content .= '<a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
    $content .= '</h5>';

    // 简介 默认1行，css控制最高2行
    $content .= '<div class="sub-title overFlow-n mb-20 mb-10-xs">';
    $content .= '<a href="' . $item['link'] . '" title="描述简介">' . $item['excerpt'] . '</a>';
    $content .= '</div>';

    $content .= '<div class="text-info">';
    $content .= '<div class="data-info d-flex align-items-center">';

    // 用户信息块
    $content .= '<div class="zhuige-user d-flex align-items-center">';
    $content .= '<div class="user-avatar">';

    $content .= '<a href="' . $item['author_link'] . '">' . zhuige_user_avatar($post->post_author) . '</a>';

    $content .= '</div>';
    $content .= '<div class="user-info">';
    $content .= '<h6 class="d-flex align-items-center">';
    $content .= '<a href="' . $item['author_link'] . '">' . $item['author'] . '</a>';
    $content .= '</h6>';
    $content .= '</div>';
    $content .= '</div>';

    $categories = get_the_category($post->ID);
    $content .= '<text><a href="' . get_term_link($categories[0]) . '">' . $categories[0]->cat_name . '</a></text>';
    $content .= '<text>' . $item['time'] . '</text>';
    $content .= '<text>浏览 ' . $item['view_count'] . '</text>';
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</div>';

    $content .= '</div>';
    $content .= '</div>';

    return $content;
}

/**
 * 获取置顶的文章
 */
function zhuige_theme_get_sticky_posts()
{
    $sticks = get_option('sticky_posts');
    if (empty($sticks)) {
        return '';
    }

    $query = new WP_Query();
    $args = [
        'post_type' => 'post',
        'orderby' => 'date',
        'post__in' => $sticks,
        'ignore_sticky_posts' => 1
    ];

    $result = $query->query($args);
    $content = '';
    foreach ($result as $post) {
        $content .= zhuige_theme_post_string($post, true);
    }

    return $content;
}

/**
 * 获取文章列表
 */
function zhuige_theme_get_posts($offset, $params)
{
    $query = new WP_Query();
    $posts_per_page = (isset($params['page_count']) ? $params['page_count'] : 10);
    $args = [
        'post_type' => 'post',
        'offset' => $offset,
        'posts_per_page' => $posts_per_page,
        'orderby' => 'date',
        'ignore_sticky_posts' => 1
    ];

    if (isset($params['cat']) && $params['cat']) {
        $args['cat'] = $params['cat'];
    } else if (isset($params['ss']) && $params['ss']) {
        $args['s'] = $params['ss'];
    } else if (isset($params['tag']) && $params['tag']) {
        $args['tag_id'] = $params['tag'];
    } else if (isset($params['author']) && $params['author']) {
        $args['author'] = $params['author'];
    } else {
        $args['post__not_in'] = get_option('sticky_posts');
    }

    $result = $query->query($args);
    $content = '';
    foreach ($result as $post) {
        $content .= zhuige_theme_post_string($post);
    }

    return ['content' => $content, 'count' => $query->post_count, 'more' => (count($result) >= $posts_per_page)];
}

/**
 * 获取喜欢的文章
 */
function zhuige_theme_posts_user_like_fav($user_id, $offset, $t = 'like')
{
    global $wpdb;
    $per_page_count = 10;

    if ($t == 'like') {
        $table_like = $wpdb->prefix . 'zhuige_theme_post_like';
        $post_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT `post_id` FROM `$table_like` WHERE user_id=%d ORDER BY `id` DESC LIMIT %d, %d",
                $user_id,
                $offset,
                $per_page_count
            )
        );
    } else {
        $table_favorite = $wpdb->prefix . 'zhuige_theme_post_favorite';
        $post_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT `post_id` FROM `$table_favorite` WHERE user_id=%d ORDER BY `id` DESC LIMIT %d, %d",
                $user_id,
                $offset,
                $per_page_count
            )
        );
    }

    if (empty($post_ids)) {
        return ['content' => '', 'more' => 0,];
    }

    $args = [
        'post_type' => ['post', 'zt_resource'],
        'post__in' => $post_ids,
        'orderby' => 'post__in',
        'ignore_sticky_posts' => 1,
    ];

    $query = new WP_Query();
    $result = $query->query($args);

    $content = '';
    foreach ($result as $post) {
        // <!-- 文章 -->
        if ($post->post_type == 'post' || $post->post_type == 'zt_resource') {
            $item = zhuige_theme_format_post($post, true);

            $content .= '<div class="zhuige-list align-items-center d-flex flex-wrap p-20 mb-20 zhuige-post-for-ajax-count">';

            $content .= '<div class="zhuige-list-img relative">';

            // 基础角标
            // if ($item['badge']) {
            //     $content .= '<div class="zhuige-list-mark absolute d-flex">';
            //     $content .= '<span>' . $item['badge'] . '</span>';
            //     $content .= '</div>';
            // }
            $content .= '<div class="zhuige-list-mark absolute d-flex">';
            if ($post->post_type == 'post') {
                $content .= '<span>资讯</span>';
            } else if ($post->post_type == 'zt_resource') {
                $content .= '<span style="background:#FF8600;">资源</span>';
            }
            $content .= '</div>';

            // 封面
            $content .= '<a class="zhuige-list-cover" href="' . $item['link'] . '">';
            $content .= '<img alt="cover" src="' . $item['thumb'] . '">';
            $content .= '</a>';

            $content .= '</div>';

            // 文本
            $content .= '<div class="zhuige-list-text">';
            $content .= '<h5 class="text-title set-top mb-10">';
            $content .= '<a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
            $content .= '</h5>';

            // 简介 默认1行，css控制最高2行
            $content .= '<div class="sub-title overFlow-n mb-10">';
            $content .= '<a href="' . $item['link'] . '" title="描述简介">' . $item['excerpt'] . '</a>';
            $content .= '</div>';

            $content .= '<div class="text-info">';
            $content .= '<div class="data-info d-flex align-items-center">';

            // 用户信息块
            $content .= '<div class="zhuige-user d-flex align-items-center">';
            $content .= '<div class="user-avatar">';
            $content .= '<a href="' . $item['author_link'] . '">' . zhuige_user_avatar($post->post_author) . '</a>';
            $content .= '</div>';
            $content .= '<div class="user-info">';
            $content .= '<h6 class="d-flex align-items-center">';
            $content .= '<a href="' . $item['author_link'] . '">' . $item['author'] . '</a>';
            $content .= '</h6>';
            $content .= '</div>';
            $content .= '</div>';
            // $content .= '<text>' . $item['time'] . '</text>';
            $content .= '<text>浏览 ' . $item['view_count'] . '</text>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
        }
    }

    return ['content' => $content, 'more' => (count($result) >= $per_page_count) ? 1 : 0];
}

/**
 * 获取我评论的文章
 */
function zhuige_theme_posts_user_comment($user_id, $offset = 0)
{
    $is_my_site = ($user_id == get_current_user_id());

    global $wpdb;
    $per_page_count = 10;

    $table_comments = $wpdb->prefix . 'comments';

    if ($is_my_site) {
        $sql = $wpdb->prepare(
            "SELECT `comment_ID`,`comment_post_ID`,`comment_content`,`comment_approved` FROM `$table_comments` "
                . "WHERE `comment_approved` IN ('1', '0', 'trash') AND `user_id`=%d ORDER BY `comment_ID` DESC LIMIT %d, %d",
            $user_id,
            $offset,
            $per_page_count
        );
    } else {
        $sql = $wpdb->prepare(
            "SELECT `comment_ID`,`comment_post_ID`,`comment_content`,`comment_approved` FROM `$table_comments` "
                . "WHERE `comment_approved`='1' AND `user_id`=%d ORDER BY `comment_ID` DESC LIMIT %d, %d",
            $user_id,
            $offset,
            $per_page_count
        );
    }
    $result = $wpdb->get_results($sql);

    if (empty($result)) {
        return ['content' => '', 'more' => 0,];
    }

    $content = '';
    foreach ($result as $comment) {
        $post = get_post($comment->comment_post_ID);

        // <!-- 文章 -->
        if ($post->post_type == 'post' || $post->post_type == 'zt_resource') {
            $item = zhuige_theme_format_post($post, true);

            $content .= '<div class="zhuige-list">';
            $content .= '<div class="zhuige-list-bg align-items-center d-flex flex-wrap p-20 zhuige-post-for-ajax-count">';

            $content .= '<div class="zhuige-list-img relative">';

            // 基础角标
            // if ($item['badge']) {
            //     $content .= '<div class="zhuige-list-mark absolute d-flex">';
            //     $content .= '<span>' . $item['badge'] . '</span>';
            //     $content .= '</div>';
            // }
            $content .= '<div class="zhuige-list-mark absolute d-flex">';
            if ($post->post_type == 'post') {
                $content .= '<span>资讯</span>';
            } else if ($post->post_type == 'zt_resource') {
                $content .= '<span style="background:#FF8600;">资源</span>';
            }
            $content .= '</div>';


            // 封面
            $content .= '<a class="zhuige-list-cover" href="' . $item['link'] . '">';
            $content .= '<img alt="cover" src="' . $item['thumb'] . '">';
            $content .= '</a>';

            $content .= '</div>';

            // 文本
            $content .= '<div class="zhuige-list-text">';
            $content .= '<h5 class="text-title set-top mb-10">';
            $content .= '<a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
            $content .= '</h5>';

            // 简介 默认1行，css控制最高2行
            $content .= '<div class="sub-title overFlow-n mb-10">';
            $content .= '<a href="' . $item['link'] . '" title="描述简介">' . $item['excerpt'] . '</a>';
            $content .= '</div>';

            $content .= '<div class="text-info">';
            $content .= '<div class="data-info d-flex align-items-center">';

            // 用户信息块
            $content .= '<div class="zhuige-user d-flex align-items-center">';
            $content .= '<div class="user-avatar">';
            $content .= '<a href="' . $item['author_link'] . '">' . zhuige_user_avatar($post->post_author) . '</a>';
            $content .= '</div>';
            $content .= '<div class="user-info">';
            $content .= '<h6 class="d-flex align-items-center">';
            $content .= '<a href="' . $item['author_link'] . '">' . $item['author'] . '</a>';
            $content .= '</h6>';
            $content .= '</div>';
            $content .= '</div>';
            // $content .= '<text>' . $item['time'] . '</text>';
            $content .= '<text>浏览 ' . $item['view_count'] . '</text>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
        }
    }

    return ['content' => $content, 'more' => (count($result) >= $per_page_count)];
}

/**
 * 文章属性
 */
$prefix_zhuige_post_opts = 'zhuige_theme_post_options';
CSF::createMetabox($prefix_zhuige_post_opts, array(
    'title'        => '追格文章设置',
    'post_type'    => 'post',
    'data_type'    => 'unserialize'
));
CSF::createSection($prefix_zhuige_post_opts, array(
    'fields' => array(
        array(
            'id'    => 'zhuige_theme_post_badge',
            'type'  => 'text',
            'title' => '角标',
        ),
    )
));

/**
 * 文章分类属性
 */
$zhuige_category_options = 'zhuige_category_options';
CSF::createTaxonomyOptions($zhuige_category_options, array(
    'taxonomy' => 'category',
));
CSF::createSection($zhuige_category_options, array(
    'fields' => array(
        array(
            'id'      => 'cover',
            'type'    => 'media',
            'title'   => '封面',
            'library' => 'image',
        ),

        array(
            'id'          => 'keywords',
            'type'        => 'text',
            'title'       => '关键词',
            'placeholder' => '关键词',
            'after'    => '<p>请用英文逗号分割.</p>',
        ),
    )
));

/**
 * 在评论列表中 增加积分
 */
add_filter('manage_edit-comments_columns', 'zhuige_theme_comments_columns');
add_action('manage_comments_custom_column', 'output_zhuige_theme_comments_columns', 10, 2);
function zhuige_theme_comments_columns($columns)
{
    $columns['zhuige_theme_resource_score'] = '产品打分';
    return $columns;
}

function output_zhuige_theme_comments_columns($column_name, $column_id)
{
    if ($column_name == 'zhuige_theme_resource_score') {
        echo get_comment_meta($column_id, 'zhuige_theme_resource_score', true);
    }
}

/**
 * 生成微信支付二维码
 */
function zhuige_theme_gen_weixin_qrcode($amount, $description, $trade_no, $cb_url)
{
    $weixin_pay = zhuige_theme_option('weixin_pay');
    if (!is_array($weixin_pay)) {
        return false;
    }

    $appid = isset($weixin_pay['appid']) ? $weixin_pay['appid'] : '';
    $mchid = isset($weixin_pay['mchid']) ? $weixin_pay['mchid'] : '';
    $key = isset($weixin_pay['key']) ? $weixin_pay['key'] : '';
    $private_serial = isset($weixin_pay['private_serial']) ? $weixin_pay['private_serial'] : '';
    $public_serial = isset($weixin_pay['public_serial']) ? $weixin_pay['public_serial'] : '';
    $private_cert = isset($weixin_pay['private_cert']) ? $weixin_pay['private_cert'] : '';
    $public_cert = isset($weixin_pay['public_cert']) ? $weixin_pay['public_cert'] : '';
    $private_cert = TEMPLATEPATH . '/cert/pro_key.pem';
    $public_cert = TEMPLATEPATH . '/cert/pingtai.pem';

    if (empty($appid) || empty($mchid) || empty($key) || empty($private_serial) || empty($public_serial) || empty($private_cert) || empty($public_cert)) {
        return false;
    }

    // 商户相关配置
    $merchantId = $mchid; // 商户号
    $merchantSerialNumber = $private_serial; // 商户API证书序列号
    // "D:\www\test.wordpress1.com/wp-content/themes/zhuige.com/cert/pro_key.pem"
    $merchantPrivateKey = PemUtil::loadPrivateKey($private_cert); // 商户私钥文件路径

    // 微信支付平台配置
    $wechatpayCertificate = PemUtil::loadCertificate($public_cert); // 微信支付平台证书文件路径

    // 构造一个WechatPayMiddleware
    $wechatpayMiddleware = WechatPayMiddleware::builder()
        ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
        ->withWechatPay([$wechatpayCertificate]) // 可传入多个微信支付平台证书，参数类型为array
        ->build();

    // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
    $stack = HandlerStack::create();
    $stack->push($wechatpayMiddleware, 'wechatpay');

    // 创建Guzzle HTTP Client时，将HandlerStack传入，接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
    $client = new \GuzzleHttp\Client(['handler' => $stack]);
    // $client->setDefaultOption('verify', false);
    // $client->setDefaultOption('headers', array('verify' => false));

    try {
        $resp = $client->request(
            'POST',
            'https://api.mch.weixin.qq.com/v3/pay/transactions/native', //请求URL
            [
                // JSON请求体
                'json' => [
                    // "time_expire" => "2018-06-08T10:34:56+08:00",
                    "amount" => [
                        "total" => $amount,
                        "currency" => "CNY",
                    ],
                    "mchid" => $mchid,
                    "description" => $description,
                    "notify_url" => $cb_url,
                    // "notify_url" => rest_url('zhuige_theme/weixin_vip_notify'),
                    "out_trade_no" => $trade_no,
                    // "goods_tag" => "WXG",
                    "appid" => $appid,
                    "attach" => '',
                    // "detail" => [
                    // 	"invoice_id" => "wx123",
                    // 	"goods_detail" => [
                    // 		[
                    // 			"goods_name" => "iPhoneX 256G",
                    // 			"wechatpay_goods_id" => "1001",
                    // 			"quantity" => 1,
                    // 			"merchant_goods_id" => "商品编码",
                    // 			"unit_price" => 828800,
                    // 		],
                    // 		[
                    // 			"goods_name" => "iPhoneX 256G",
                    // 			"wechatpay_goods_id" => "1001",
                    // 			"quantity" => 1,
                    // 			"merchant_goods_id" => "商品编码",
                    // 			"unit_price" => 828800,
                    // 		],
                    // 	],
                    // 	"cost_price" => 608800,
                    // ],
                    // "scene_info" => [
                    // 	"store_info" => [
                    // 		"address" => "广东省深圳市南山区科技中一道10000号",
                    // 		"area_code" => "440305",
                    // 		"name" => "腾讯大厦分店",
                    // 		"id" => "0001",
                    // 	],
                    // 	"device_id" => "013467007045764",
                    // 	"payer_client_ip" => "14.23.150.211",
                    // ]
                ],
                'headers' => ['Accept' => 'application/json']
            ]
        );
        $statusCode = $resp->getStatusCode();
        if ($statusCode == 200) { //处理成功
            // echo "success,return body = " . $resp->getBody()->getContents() . "\n";
            $content = $resp->getBody()->getContents();
            $json = json_decode($content, true);
            if (isset($json['code_url'])) {
                return $json['code_url'];
            } else {
                return false;
            }
        } else {
            // return "success";
            return false;
        }
        // else if ($statusCode == 204) { //处理成功，无返回Body
        // 	echo "success";
        // }
    } catch (RequestException $e) {
        // 进行错误处理
        $msg = $e->getMessage() . "\n";
        if ($e->hasResponse()) {
            $msg .= "failed,resp code = " . $e->getResponse()->getStatusCode() . " return body = " . $e->getResponse()->getBody() . "\n";
        }
        return $msg;
        // return false;
    }
}

/**
 * 消费记录
 */
function zhuige_theme_spend_log_output($user_id, $offset = 0)
{
    $per_page = 10;

    global $wpdb;
    $table_spend_log = $wpdb->prefix . 'zhuige_theme_spend_log';
    $sql = $wpdb->prepare("SELECT * FROM `$table_spend_log` WHERE `user_id`=%d ORDER BY `id` DESC LIMIT %d OFFSET %d", $user_id, $per_page, $offset);
    $result = $wpdb->get_results($sql, ARRAY_A);
    if (empty($result)) {
        return ['content' => '', 'more' => 0,];
    }

    $content = '';
    foreach ($result as $log) {
        $content .= '<div class="zhuige-order-list">';

        $content .= '<div class="zhuige-list-bg align-items-center d-flex justify-content-between p-20">';

        $content .= '<div class="zhuige-list align-items-center d-flex">';

        // 封面图/头像 -- start
        $content .= '<div class="zhuige-list-img relative">';

        // 封面角标/vip -- start
        $content .= '<div class="zhuige-list-mark absolute d-flex">';

        $content .= '<span class="order-vip">';
        $link = '';
        if ($log['type'] == 'post') {
            $content .= '资讯';
            $link = get_permalink($log['extra']);
        } else if ($log['type'] == 'resource') {
            $content .= '资源';
            $link = get_permalink($log['extra']);
        } else if ($log['type'] == 'vip') {
            $content .= 'VIP';
            $link = home_url('/vip');
        }
        $content .= '</span>';

        $content .= '</div>';
        // 封面角标/vip -- end

        // 封面
        $content .= '<a class="zhuige-list-cover" href="' . $link . '" target="_blank">';
        $thumb = '';
        if ($log['type'] == 'post' || $log['type'] == 'resource') {
            $post = get_post($log['extra']);
            $thumb = zhuige_theme_thumbnail_src_d($post->ID, $post->post_content);
        } else if ($log['type'] == 'vip') {

        }
        if (empty($thumb)) {
            $thumb = ZHUIGE_THEME_URL . '/addons/vip/images/default_thumb.png';
        }
        $content .= '<img alt="cover" src="' . $thumb . '">';
        $content .= '</a>';

        $content .= '</div>';
        // 封面图/头像 -- end

        // 文本
        $content .= '<div class="zhuige-list-text">';
        $content .= '<h5 class="text-title set-top mb-10">';
        $content .= '<a href="' . $link . '" target="_blank" title="' . $log['title'] . '">' . $log['title'] . '</a>';
        $content .= '</h5>';

        $content .= '<div class="text-info">';
        $content .= '<div class="data-info d-flex align-items-center">';
        $content .= '<text>' . date('Y-m-d H:i:s', $log['createtime']) . '</text>';
        $content .= '</div>';
        $content .= '</div>';
		
		$content .= '<div class="zhuige-order-pay">';
		$content .= '<span>实付</span>';
		$content .= '<cite>￥</cite>';
		$content .= '<text>' . $log['amount'] . '</text>';
		$content .= '</div>';		
		
        $content .= '</div>';		
        $content .= '</div>';

        $content .= '<div class="zhuige-order-pay">';
        $content .= '<span>实付</span>';
        $content .= '<cite>￥</cite>';
        $content .= '<text>' . $log['amount'] . '</text>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
    }

    return ['content' => $content, 'more' => (count($result) >= $per_page)];
}
