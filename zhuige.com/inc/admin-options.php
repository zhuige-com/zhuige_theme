<?php

/**
 * 追格主题
 */

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'zhuige-theme';

//
// Create options
//
CSF::createOptions($prefix, array(
    'framework_title' => '追格主题 <small>by <a href="https://www.zhuige.com" target="_blank" title="追格">www.zhuige.com</a></small>',
    'menu_title' => '追格主题',
    'menu_slug'  => 'zhuige-theme',
    'menu_position' => 2,
    'show_bar_menu' => false,
    'show_sub_menu' => false,
    'footer_credit' => 'Thank you for creating with <a href="https://www.zhuige.com/" target="_blank">追格</a>',
    'menu_icon' => 'dashicons-layout',
));

$content = '欢迎使用追格主题! <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.zhuige.com/product" target="_blank">更多免费产品</a>';
if (stripos($_SERVER["REQUEST_URI"], 'zhuige-theme')) {
    $res = wp_remote_get("https://www.zhuige.com/api/ad/wordpress?id=zhuige_theme", ['timeout' => 1, 'sslverify' => false]);
    if (!is_wp_error($res) && $res['response']['code'] == 200) {
        $data = json_decode($res['body'], TRUE);
        if ($data['code'] == 1) {
            $content = $data['data'];
        }
    }
}

//
// 概要
//
CSF::createSection($prefix, array(
    'title'  => '概要',
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'type'    => 'content',
            'content' => $content,
        ),

    )
));

//
// 全局设置
//
CSF::createSection($prefix, array(
    'id'    => 'global',
    'title' => '全局设置',
    'icon'  => 'fas fa-plus-circle',
));

//
// LOGO设置
//
CSF::createSection($prefix, array(
    'parent'    => 'global',
    'title'     => 'LOGO设置',
    'icon'      => 'fas fa-apple-alt',
    'fields'    => array(

        array(
            'id'      => 'site_logo',
            'type'    => 'media',
            'title'   => 'LOGO设置',
            'library' => 'image',
        ),
        array(
            'id'      => 'site_favicon',
            'type'    => 'media',
            'title'   => 'favicon',
            'subtitle' => '.ico格式',
            'library' => 'image',
        ),

    )
));

//
// 菜单设置
//
CSF::createSection($prefix, array(
    'parent'    => 'global',
    'title'     => '菜单设置',
    'icon'      => 'fas fa-hamburger',
    'fields'    => array(

        array(
            'id'     => 'site_nav',
            'type'   => 'group',
            'title'  => '顶部菜单',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'       => 'url',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.zhuige.com',
                    'validate' => 'csf_validate_url',
                ),
                array(
                    'id'    => 'blank',
                    'type'  => 'switcher',
                    'title' => '新页面打开',
                    'default' => ''
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '是否启用',
                    'default' => '1'
                ),
            ),
        ),
        array(
            'id'     => 'h5_tabbar',
            'type'   => 'group',
            'title'  => 'H5导航',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'      => 'icon',
                    'type'    => 'media',
                    'title'   => '图标',
                    'library' => 'image',
                ),
                array(
                    'id'      => 'icon_sel',
                    'type'    => 'media',
                    'title'   => '选中图标',
                    'library' => 'image',
                ),
                array(
                    'id'       => 'url',
                    'type'     => 'text',
                    'title'    => '广告链接',
                    'default'  => 'https://www.zhuige.com',
                    'validate' => 'csf_validate_url',
                ),
                array(
                    'id'    => 'blank',
                    'type'  => 'switcher',
                    'title' => '新页面打开',
                    'default' => ''
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '是否启用',
                    'default' => '1'
                ),
            ),
        ),

    )
));


//
// 登录注册
//
CSF::createSection($prefix, array(
    'parent'    => 'global',
    'title'     => '登录注册',
    'icon'      => 'fas fa-user-plus',
    'fields'    => array(

        array(
            'type'    => 'subheading',
            'content' => '微信登录设置',
        ),
        array(
            'id'          => 'wx_app_id',
            'type'        => 'text',
            'title'       => 'App ID',
            'placeholder' => 'App ID'
        ),
        array(
            'id'          => 'wx_app_secret',
            'type'        => 'text',
            'title'       => 'App Secret',
            'placeholder' => 'App Secret'
        ),
        array(
            'id'    => 'register_normal_switch',
            'type'  => 'switcher',
            'title' => '普通注册',
            'label' => '是否开启普通注册',
            'default' => '0'
        ),
        array(
            'id'    => 'login_weixin_switch',
            'type'  => 'switcher',
            'title' => '微信登录',
            'label' => '是否开启微信登录',
            'default' => '0'
        ),
        array(
            'id'          => 'login_yhxy',
            'type'        => 'select',
            'title'       => '用户协议',
            'chosen'      => true,
            'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '请选择用户协议',
        ),
        array(
            'id'          => 'login_yszc',
            'type'        => 'select',
            'title'       => '隐私政策',
            'chosen'      => true,
            'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '请选择隐私政策',
        ),

    )
));

//
// 找回密码
//
CSF::createSection($prefix, array(
    'parent'      => 'global',
    'title'       => '找回密码',
    'icon'        => 'fas fa-user-shield',
    'fields'      => array(

        array(
            'id'    => 'forgot_switch',
            'type'  => 'switcher',
            'title' => '启用找回密码',
            'label' => '是否显示找回密码.',
            'default' => '0'
        ),
        array(
            'id'          => 'mail_smtp_server',
            'type'        => 'text',
            'title'       => 'SMTP服务器',
        ),
        array(
            'id'          => 'mail_from_address',
            'type'        => 'text',
            'title'       => '发件人邮箱',
        ),
        array(
            'id'          => 'mail_from_auth',
            'type'        => 'text',
            'title'       => '认证密码',
        ),
        array(
            'id'          => 'mail_from_nick',
            'type'        => 'text',
            'title'       => '发件人昵称',
        ),
        array(
            'id'          => 'mail_title',
            'type'        => 'text',
            'title'       => '邮件标题',
        ),
        array(
            'id'          => 'mail_content',
            'type'        => 'wp_editor',
            'title'       => '邮件内容',
            'subtitle'    => '重置地址占位符：[zhuige-reset-url]',
            'editor'      => 'trumbowyg',
        ),

    )
));


//
// 首页设置
//
CSF::createSection($prefix, array(
    'title' => '首页设置',
    'icon'  => 'fas fa-home',
    'fields' => array(

        array(
            'id'     => 'home_header',
            'type'   => 'fieldset',
            'title'  => '头部设置',
            'fields' => array(
                array(
                    'id'      => 'bg_image',
                    'type'    => 'media',
                    'title'   => '背景图片',
                    'library' => 'image',
                ),
                array(
                    'id'    => 'bg_video',
                    'type'  => 'text',
                    'title' => '背景视频地址',
                    'subtitle' => '可选，只在PC显示'
                ),
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'       => 'slogons',
                    'type'     => 'textarea',
                    'title'    => 'slogon',
                    'subtitle' => '请使用英文逗号分割'
                ),
                array(
                    'id'          => 'tip',
                    'type'        => 'text',
                    'title'       => '搜索提示',
                    'placeholder' => '搜索提示'
                ),
                array(
                    'id'          => 'hot_words',
                    'type'        => 'text',
                    'title'       => '热门搜索词',
                    'subtitle'    => '请用英文逗号分割'
                ),
            ),
        ),
        array(
            'id'          => 'home_cat_show',
            'type'        => 'select',
            'title'       => '显示分类',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'placeholder' => 'Select an option',
            'options'     => 'categories'
        ),
        array(
            'id'     => 'home_right_news',
            'type'   => 'fieldset',
            'title'  => '热门推荐',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题',
                    'default'     => '热门推荐'
                ),
                array(
                    'id'          => 'ids',
                    'type'        => 'select',
                    'title'       => '选择文章',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'placeholder' => 'Select an option',
                    'options'     => 'post'
                ),

            ),
        ),
        array(
            'id'     => 'home_right_tags',
            'type'   => 'fieldset',
            'title'  => '热门标签',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题',
                    'default'     => '热门标签'
                ),
                array(
                    'id'       => 'count',
                    'type'     => 'spinner',
                    'title'    => '显示个数',
                    'subtitle' => 'max:100 | min:0 | step:1',
                    'max'      => 100,
                    'min'      => 0,
                    'step'     => 1,
                    'default'  => 6,
                ),
            ),
        ),
        array(
            'id'     => 'home_right_ad',
            'type'   => 'group',
            'title'  => '右侧广告',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                ),
                array(
                    'id'       => 'link',
                    'type'     => 'text',
                    'title'    => '广告链接',
                    'default'  => 'https://www.zhuige.com',
                    'validate' => 'csf_validate_url',
                ),
            ),
        ),
        array(
            'id'       => 'home_page_count',
            'type'     => 'spinner',
            'title'    => '每页文章数量',
            'subtitle' => 'max:100 | min:0 | step:1',
            'max'      => 100,
            'min'      => 0,
            'step'     => 1,
            'default'  => 10,
        ),
        array(
            'id'       => 'home_excerpt_length',
            'type'     => 'spinner',
            'title'    => '摘要长度',
            'subtitle' => 'max:100 | min:0 | step:1',
            'max'      => 100,
            'min'      => 0,
            'step'     => 1,
            'default'  => 25,
        ),

    )
));


//
// 页脚设置
//
CSF::createSection($prefix, array(
    'title' => '页脚设置',
    'icon'  => 'fas fa-chalkboard',
    'fields' => array(

        array(
            'id'    => 'footer_copyright',
            'type'  => 'wp_editor',
            'title' => '页脚版权',
        ),
        array(
            'id'     => 'footer_nav',
            'type'   => 'group',
            'title'  => '快速导航',
            'fields' => array(
                array(
                    'id'       => 'title',
                    'type'     => 'text',
                    'title'    => '标题',
                    'default'  => '',
                ),
                array(
                    'id'       => 'url',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.zhuige.com',
                    'validate' => 'csf_validate_url',
                ),
            ),
        ),
        array(
            'id'       => 'footer_statistics',
            'type'     => 'code_editor',
            'title'    => '网站统计',
            'settings' => array(
                'theme'  => 'dracula',
                'mode'   => 'javascript',
            ),
            'default' => '',
        ),

    )
));

//
// SEO设置
//
CSF::createSection($prefix, array(
    'title' => 'SEO设置',
    'icon'  => 'fas fa-bolt',
    'fields' => array(

        array(
            'id'     => 'seo_home',
            'type'   => 'fieldset',
            'title'  => '首页',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),

                array(
                    'id'          => 'keywords',
                    'type'        => 'text',
                    'title'       => '关键词',
                    'placeholder' => '关键词',
                    'after'    => '<p>请用英文逗号分割.</p>',
                ),

                array(
                    'id'          => 'description',
                    'type'        => 'textarea',
                    'title'       => '描述',
                    'placeholder' => '描述',
                ),
            ),
        ),
        array(
            'id'     => 'seo_news',
            'type'   => 'fieldset',
            'title'  => '资讯首页',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),

                array(
                    'id'          => 'keywords',
                    'type'        => 'text',
                    'title'       => '关键词',
                    'placeholder' => '关键词',
                    'after'    => '<p>请用英文逗号分割.</p>',
                ),

                array(
                    'id'          => 'description',
                    'type'        => 'textarea',
                    'title'       => '描述',
                    'placeholder' => '描述',
                ),
            ),
        ),

    )
));


//
// 其他设置
//
CSF::createSection($prefix, array(
    'id'    => 'other',
    'title' => '其他设置',
    'icon'  => 'fas fa-plus-circle',
));

//
// 列表设置
//
CSF::createSection($prefix, array(
    'parent' => 'other',
    'title' => '列表设置',
    'icon'  => 'fas fa-home',
    'fields' => array(

        array(
            'id'     => 'list_header',
            'type'   => 'fieldset',
            'title'  => '头部设置',
            'fields' => array(
                array(
                    'id'      => 'bg_image',
                    'type'    => 'media',
                    'title'   => '背景图片',
                    'library' => 'image',
                ),
                array(
                    'id'    => 'bg_video',
                    'type'  => 'text',
                    'title' => '背景视频地址',
                    'subtitle' => '可选，只在PC显示'
                ),
                array(
                    'id'          => 'tip',
                    'type'        => 'text',
                    'title'       => '搜索提示',
                    'placeholder' => '搜索提示'
                ),
            ),
        ),

    )
));

//
// 标签聚合
//
CSF::createSection($prefix, array(
    'parent' => 'other',
    'title' => '标签聚合',
    'icon'  => 'fab fa-wordpress',
    'fields' => array(

        array(
            'id'    => 'tags_title',
            'type'  => 'text',
            'title' => '标题',
        ),
        array(
            'id'      => 'tags_bg',
            'type'    => 'media',
            'title'   => '背景',
            'library' => 'image',
        ),

    )
));

//
// 关于页面
//
CSF::createSection($prefix, array(
    'parent' => 'other',
    'title' => '关于页面',
    'icon'  => 'fab fa-wordpress',
    'fields' => array(

        array(
            'id'      => 'about_bg',
            'type'    => 'media',
            'title'   => '背景',
            'library' => 'image',
        ),
        array(
            'id'          => 'about_nav',
            'type'        => 'select',
            'title'       => '选择页面',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'placeholder' => 'Select an option',
            'options'     => 'pages'
        ),

    )
));

//
// 备份
//
CSF::createSection($prefix, array(
    'title'       => '备份',
    'icon'        => 'fas fa-shield-alt',
    'fields'      => array(

        array(
            'type' => 'backup',
        ),

    )
));
