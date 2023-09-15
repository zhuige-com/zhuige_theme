<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('Please do not load this page directly. Thanks!');
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password,
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}

?>

<div id="zhuige-comment-container" class="zhuige-comment zhuige-box p-20">

    <?php
    if (comments_open()) {
    ?>

        <h3 class="d-flex align-items-center justify-content-between mb-20">
            <p>
                <text>发表点评</text>
                <span>（<?php echo $post->comment_count ?>条）</span>
            </p>
            <p class="zhuige-comment-reply-container" style="display:none;">
                <cite>回复：<text class="zhuige-comment-reply-nickname"></text></cite>
                <a href="javascript:void(0)" class="zhuige-btn-comment-reply-cancel" title="">取消回复</a>
            </p>
        </h3>

        <!-- 评论框 -->
        <div class="zhuige-comment-input d-flex flex-nowrap mb-20">
            <input type="text" class="zhuige-comment-content" placeholder="友善是交流的起点" />
            <input type="hidden" class="zhuige-comment-post_id" value="<?php echo $post->ID ?>" />
            <input type="hidden" class="zhuige-comment-parent" value="" />
            <?php if (is_user_logged_in()) { ?>
                <a class="zhuige-btn-comment-submit">提交</a>
            <?php } else { ?>
                <a class="zhuige-btn-comment-submit">提交</a>
        </div>
        <div class="zhuige-comment-login d-flex align-items-center">
            <a href="javascript:void(0)" class="zhuige-btn-pop-login" title="">登录</a>
            <text>后参与评论</text>
        <?php } ?>
        </div>

    <?php } else {
        echo '评论已关闭';
    } ?>

    <!-- 评论列表 -->
    <div class="zhuige-user-msg pt-20">
        <?php wp_list_comments([
            'type' => 'comment',
            'reverse_top_level' => true,
            'callback' => 'zhuige_theme_comment_list'
        ]) ?>
    </div>

    <!-- 无评论提示 -->
    <?php if (get_comment_count($post->ID)['all'] == 0) { ?>
        <div class="zhuige-none-tips">
            <img src="<?php echo ZHUIGE_THEME_URL . '/images/not_found.png' ?>" alt="not found" />
            <p>暂无评论，你要说点什么吗？</p>
        </div>
    <?php } ?>
</div>