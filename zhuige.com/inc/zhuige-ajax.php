<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追格主题
 */

/* PHP远程下载微信头像存到本地,本地图片转base64
 * $url 微信头像链接
 * $path 要保存图片的目录
 * $user_id 用户唯一标识
 */
if (!function_exists('zhuige_theme_download_wx_avatar')) {
    function zhuige_theme_download_wx_avatar($url, $user_id)
    {
        $header = [
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) { //把URL格式的图片转成base64_encode格式的！      
            $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
        }
        $img_content = $imgBase64Code; //图片内容  
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
            $type = $result[2]; //得到图片类型png jpg gif

            // //相对路径
            // $relative_path = $path . "/";
            // //绝对路径（$_SERVER['DOCUMENT_ROOT']为网站根目录）
            // $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $relative_path;
            // if (!file_exists($absolute_path)) {
            //     //检查是否有该文件夹，如果没有就创建，并给予最高权限
            //     mkdir($absolute_path, 0700);
            // }

            $upload_dir = wp_upload_dir();
            $filename = 'jiangqie_avatar_' . $user_id . ".{$type}";
            $filepath = $upload_dir['path'] . '/' . $filename;
            if (file_put_contents($filepath, base64_decode(str_replace($result[1], '', $img_content)))) {
                return ['path' => $filepath, 'url' => $upload_dir['url'] . '/' . $filename];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('zhuige_theme_import_image2attachment')) {
    //把图片添加到媒体库
    function zhuige_theme_import_image2attachment($file, $post_id = 0, $import_date = 'current')
    {
        set_time_limit(0);

        // Initially, Base it on the -current- time.
        $time = current_time('mysql', 1);
        // Next, If it's post to base the upload off:
        if ('post' == $import_date && $post_id > 0) {
            $post = get_post($post_id);
            if ($post && substr($post->post_date_gmt, 0, 4) > 0) {
                $time = $post->post_date_gmt;
            }
        } elseif ('file' == $import_date) {
            $time = gmdate('Y-m-d H:i:s', @filemtime($file));
        }

        // A writable uploads dir will pass this test. Again, there's no point overriding this one.
        if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
            return new WP_Error('upload_error', $uploads['error']);
        }

        $wp_filetype = wp_check_filetype($file, null);

        extract($wp_filetype);

        if ((!$type || !$ext) && !current_user_can('unfiltered_upload')) {
            return new WP_Error('wrong_file_type', __('Sorry, this file type is not permitted for security reasons.', 'add-from-server'));
        }

        // Is the file allready in the uploads folder?
        // WP < 4.4 Compat: ucfirt
        if (preg_match('|^' . preg_quote(ucfirst(wp_normalize_path($uploads['basedir'])), '|') . '(.*)$|i', $file, $mat)) {

            $filename = basename($file);
            $new_file = $file;

            $url = $uploads['baseurl'] . $mat[1];

            $attachment = get_posts(array('post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/')));
            if (!empty($attachment)) {
                return new WP_Error('file_exists', __('Sorry, That file already exists in the WordPress media library.', 'add-from-server'));
            }

            // Ok, Its in the uploads folder, But NOT in WordPress's media library.
            if ('file' == $import_date) {
                $time = @filemtime($file);
                if (preg_match("|(\d+)/(\d+)|", $mat[1], $datemat)) { // So lets set the date of the import to the date folder its in, IF its in a date folder.
                    $hour = $min = $sec = 0;
                    $day = 1;
                    $year = $datemat[1];
                    $month = $datemat[2];

                    // If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
                    if ($time && wp_date('Y-m', $time) == "$year-$month") {
                        list($hour, $min, $sec, $day) = explode(';', wp_date('H;i;s;j', $time));
                    }

                    $time = mktime($hour, $min, $sec, $month, $day, $year);
                }
                $time = gmdate('Y-m-d H:i:s', $time);

                // A new time has been found! Get the new uploads folder:
                // A writable uploads dir will pass this test. Again, there's no point overriding this one.
                if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
                    return new WP_Error('upload_error', $uploads['error']);
                }
                $url = $uploads['baseurl'] . $mat[1];
            }
        } else {
            $filename = wp_unique_filename($uploads['path'], basename($file));

            // copy the file to the uploads dir
            $new_file = $uploads['path'] . '/' . $filename;
            if (false === @copy($file, $new_file))
                return new WP_Error('upload_error', sprintf(__('The selected file could not be copied to %s.', 'add-from-server'), $uploads['path']));

            // Set correct file permissions
            $stat = stat(dirname($new_file));
            $perms = $stat['mode'] & 0000666;
            @chmod($new_file, $perms);
            // Compute the URL
            $url = $uploads['url'] . '/' . $filename;

            if ('file' == $import_date) {
                $time = gmdate('Y-m-d H:i:s', @filemtime($file));
            }
        }

        // Apply upload filters
        $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type));
        $new_file = $return['file'];
        $url = $return['url'];
        $type = $return['type'];

        $title = preg_replace('!\.[^.]+$!', '', basename($file));
        $content = $excerpt = '';

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if (preg_match('#^audio#', $type)) {
            $meta = wp_read_audio_metadata($new_file);

            if (!empty($meta['title'])) {
                $title = $meta['title'];
            }

            if (!empty($title)) {

                if (!empty($meta['album']) && !empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: album title, 3: artist name */
                    $content .= sprintf(__('"%1$s" from %2$s by %3$s.', 'add-from-server'), $title, $meta['album'], $meta['artist']);
                } elseif (!empty($meta['album'])) {
                    /* translators: 1: audio track title, 2: album title */
                    $content .= sprintf(__('"%1$s" from %2$s.', 'add-from-server'), $title, $meta['album']);
                } elseif (!empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: artist name */
                    $content .= sprintf(__('"%1$s" by %2$s.', 'add-from-server'), $title, $meta['artist']);
                } else {
                    $content .= sprintf(__('"%s".', 'add-from-server'), $title);
                }
            } elseif (!empty($meta['album'])) {

                if (!empty($meta['artist'])) {
                    /* translators: 1: audio album title, 2: artist name */
                    $content .= sprintf(__('%1$s by %2$s.', 'add-from-server'), $meta['album'], $meta['artist']);
                } else {
                    $content .= $meta['album'] . '.';
                }
            } elseif (!empty($meta['artist'])) {

                $content .= $meta['artist'] . '.';
            }

            if (!empty($meta['year']))
                $content .= ' ' . sprintf(__('Released: %d.'), $meta['year']);

            if (!empty($meta['track_number'])) {
                $track_number = explode('/', $meta['track_number']);
                if (isset($track_number[1]))
                    $content .= ' ' . sprintf(__('Track %1$s of %2$s.', 'add-from-server'), number_format_i18n($track_number[0]), number_format_i18n($track_number[1]));
                else
                    $content .= ' ' . sprintf(__('Track %1$s.', 'add-from-server'), number_format_i18n($track_number[0]));
            }

            if (!empty($meta['genre']))
                $content .= ' ' . sprintf(__('Genre: %s.', 'add-from-server'), $meta['genre']);

            // Use image exif/iptc data for title and caption defaults if possible.
        } elseif (0 === strpos($type, 'image/') && $image_meta = @wp_read_image_metadata($new_file)) {
            if (trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
                $title = $image_meta['title'];
            }

            if (trim($image_meta['caption'])) {
                $excerpt = $image_meta['caption'];
            }
        }

        if ($time) {
            $post_date_gmt = $time;
            $post_date = $time;
        } else {
            $post_date = current_time('mysql');
            $post_date_gmt = current_time('mysql', 1);
        }

        // Construct the attachment array
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
        );

        $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);

        // WP < 4.4 Compat: ucfirt
        $new_file = str_replace(ucfirst(wp_normalize_path($uploads['basedir'])), $uploads['basedir'], $new_file);

        // Save the data
        $id = wp_insert_attachment($attachment, $new_file, $post_id);
        if (!is_wp_error($id)) {
            $data = wp_generate_attachment_metadata($id, $new_file);
            wp_update_attachment_metadata($id, $data);
            if (isset($data['file'])) {
                $filename = $data['file'];
            }
        }
        // update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

        return basename($filename);
    }
}

/**
 * 发送邮件
 */
if (!function_exists('zhuige_theme_send_email')) {
    function zhuige_theme_send_email($address, $subject, $body)
    {
        require_once get_theme_file_path() . '/inc/phpmailer/Exception.php';
        require_once get_theme_file_path() . '/inc/phpmailer/PHPMailer.php';
        require_once get_theme_file_path() . '/inc/phpmailer/SMTP.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            // 调试信息
            // $mail->SMTPDebug = 2;

            // 设置PHPMailer使用SMTP服务器发送Email
            $mail->IsSMTP();

            // 设置邮件的字符编码，若不指定，则为'UTF-8'
            $mail->CharSet = 'UTF-8';

            $mail_smtp_server = zhuige_theme_option('mail_smtp_server');
            if (empty($mail_smtp_server)) {
                return '未设置SMTP服务器';
            }

            // 设置SMTP服务器。这里使用网易的SMTP服务器。
            $mail->Host = $mail_smtp_server;

            // 设置为“需要验证”
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            //需要服务器开放465端口
            $mail->Port = 465;
            $mail->IsHTML(true);

            $mail_from_address = zhuige_theme_option('mail_from_address');
            if (empty($mail_from_address)) {
                return '未设置发件人邮箱';
            }
            // 设置用户名和密码，即网易邮件的用户名和密码。
            $mail->Username = $mail_from_address;

            $mail_from_auth = zhuige_theme_option('mail_from_auth');
            if (empty($mail_from_auth)) {
                return '未设置认证密码';
            }
            $mail->Password = $mail_from_auth;

            // 设置邮件头的From字段。
            // 对于网易的SMTP服务，这部分必须和你的实际账号相同，否则会验证出错。
            $mail->From = $mail_from_address;

            $mail_from_nick = zhuige_theme_option('mail_from_nick');
            if (empty($mail_from_nick)) {
                return '未设置发件人昵称';
            }
            // 设置发件人名字
            $mail->FromName = $mail_from_nick;

            // 添加收件人地址，可以多次使用来添加多个收件人
            $mail->AddAddress($address);

            // 设置邮件标题
            $mail->Subject = $subject;

            // 设置邮件正文
            $mail->Body = $body;

            // 发送邮件。
            $mail->Send();

            return '';
        } catch (PHPMailer\PHPMailer\Exception $e) {
            return '邮件发送失败: ' . $mail->ErrorInfo;
        }
    }
}

/**
 * AjAX
 */
add_action('wp_ajax_nopriv_zhuige_theme_event', 'zhuige_theme_event');
add_action('wp_ajax_zhuige_theme_event', 'zhuige_theme_event');
function zhuige_theme_event()
{
    $action = isset($_POST["zgaction"]) ? sanitize_text_field(wp_unslash($_POST["zgaction"])) : '';

    if ($action == 'register') { // 注册
        $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
        $user_email = apply_filters('user_registration_email', (isset($_POST['email']) ? sanitize_email($_POST['email']) : ''));

        // Check the username
        if ($username == '') {
            wp_send_json_error('请填写用户名');
        } elseif (!validate_username($username)) {
            wp_send_json_error('用户名包含无效字符');
        } elseif (username_exists($username)) {
            wp_send_json_error('用户名已被注册');
        }

        // Check the e-mail address
        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
        $repwd = isset($_POST['repwd']) ? $_POST['repwd'] : '';
        if ($user_email == '') {
            wp_send_json_error('请填写电子邮件地址');
        } elseif (!is_email($user_email)) {
            wp_send_json_error('电子邮件地址不正确');
        } elseif (email_exists($user_email)) {
            wp_send_json_error('电子邮件地址已被注册');
        }

        // Check the password
        if (strlen($pwd) < 6) {
            wp_send_json_error('密码长度至少6位');
        } elseif ($pwd != $repwd) {
            wp_send_json_error('两次输入的密码必须一致');
        }

        // $user_id = wp_create_user($username, $_POST['pwd'], $user_email);
        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_pass' => $pwd,
            'user_nicename' => uniqid(),
            'user_email' => $user_email,
        ]);

        if (is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
        }

        if (!is_user_logged_in()) {
            $user = get_user_by('login', $username);
            $user_id = $user->ID;

            // 自动登录
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login, $user);
        }

        wp_send_json_success();
    } else if ($action == 'login') { // 登录
        $username = sanitize_user($_POST['log']);

        // Check the username
        if ($username == '') {
            wp_send_json_error('请填写用户名');
        } elseif (!validate_username($username)) {
            wp_send_json_error('用户名包含无效字符');
        }

        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
        if (strlen($pwd) < 6) {
            wp_send_json_error('密码长度至少6位');
        }

        $user = wp_signon();
        if (is_wp_error($user)) {
            wp_send_json_error('账号或密码不正确');
        }

        wp_send_json_success();
    } else if ($action == 'forgot_send_email') {
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!$email) {
            wp_send_json_error('请输入邮箱');
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            wp_send_json_error('邮箱未注册');
        }

        $subject = zhuige_theme_option('mail_title');
        if ($subject) {
            $subject = '重置密码-' . get_bloginfo('name');
        }

        $token = md5($_SERVER['SERVER_NAME'] . uniqid(rand(), true));
        $zhuige_reset_url = home_url('/user-reset?u=' . $user->ID . '&t=' . $token);

        $body = zhuige_theme_option('mail_content');
        if (empty($body)) {
            $body = $zhuige_reset_url;
        } else {
            $body = str_replace('[zhuige-reset-url]', $zhuige_reset_url, $body);
        }

        $error = zhuige_theme_send_email($email, $subject, $body);
        if ($error) {
            wp_send_json_error($error);
        }

        $expire = time() + 600;
        update_user_meta($user->ID, 'zhuige_theme_reset_token', [
            'token' => $token,
            'expire' => $expire
        ]);

        wp_send_json_success();
    } else if ($action == 'user_reset_pwd') {
        $user_id = isset($_POST['user_id']) ? (int)($_POST['user_id']) : 0;
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';

        if (!$user_id || !$password || !$token) {
            wp_send_json_error('异常请求');
        }

        if (strlen($password) < 6) {
            wp_send_json_error('密码至少6位');
        }

        if (is_numeric($password)) {
            wp_send_json_error('密码不能是纯数字');
        }

        $reset_token = get_user_meta($user_id, 'zhuige_theme_reset_token', true);
        if (!$reset_token) {
            wp_send_json_error('请尝试重发邮件');
        }

        if (!is_array($reset_token) || $reset_token['token'] != $token || $reset_token['expire'] < time()) {
            wp_send_json_error('请尝试重发邮件');
        }

        wp_set_password($password, $user_id);

        wp_send_json_success();
    } else if ($action == 'comment') { // 评论
        $my_user_id = get_current_user_id();
        if (!$my_user_id) {
            wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
        }

        $post_id = isset($_POST["post_id"]) ? (int)($_POST["post_id"]) : 0;
        if (!$post_id) {
            wp_send_json_error('缺少参数');
        }

        $content = isset($_POST["content"]) ? sanitize_text_field(wp_unslash($_POST["content"])) : '';
        if (!$content) {
            wp_send_json_error('请输入评论内容');
        }

        $parent = isset($_POST["parent"]) ? (int)($_POST["parent"]) : 0;

        $comment_approved = 0; // 必须人工审核，以防垃圾信息
        $comment_id = wp_insert_comment([
            'comment_post_ID' => $post_id,
            'comment_content' => $content,
            'comment_parent' => $parent,
            'comment_approved' => $comment_approved,
            'user_id' => $my_user_id,
        ]);

        if ($comment_id) {
            wp_send_json_success();
        } else {
            wp_send_json_error('请稍后再试');
        }
    } else if ($action == 'user_delete_comment') {
        $my_user_id = get_current_user_id();
        if (!$my_user_id) {
            wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
        }

        $comment_id = isset($_POST["comment_id"]) ? (int)($_POST["comment_id"]) : 0;
        if (empty($comment_id)) {
            wp_send_json_error('缺少参数');
        }

        $comment = get_comment($comment_id);
        if (!$comment || $comment->user_id != $my_user_id) {
            wp_send_json_error('异常请求');
        }

        wp_delete_comment($comment_id, true);

        wp_send_json_success();
    } else if ($action == 'like') { // 点赞
        $my_user_id = get_current_user_id();
        if (!$my_user_id) {
            wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
        }

        $post_id = isset($_POST["post_id"]) ? (int)($_POST["post_id"]) : 0;
        if (!$post_id) {
            wp_send_json_error('缺少参数');
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('异常请求');
        }

        global $wpdb;
        $table_post_like = $wpdb->prefix . 'zhuige_theme_post_like';
        $post_like_id = zhuige_theme_is_like($my_user_id, $post_id);

        $is_like = 0;
        if ($post_like_id) {
            $wpdb->query("DELETE FROM `$table_post_like` WHERE `id`=$post_like_id");
            $is_like = 0;
        } else {
            $wpdb->insert($table_post_like, [
                'user_id' => $my_user_id,
                'post_id' => $post_id,
                'time' => time()
            ]);
            $is_like = 1;
        }

        wp_send_json_success(['is_like' => $is_like]);
    } else if ($action == 'favorite') { // 收藏
        $my_user_id = get_current_user_id();
        if (!$my_user_id) {
            wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
        }

        $post_id = isset($_POST["post_id"]) ? (int)($_POST["post_id"]) : 0;
        if (!$post_id) {
            wp_send_json_error('缺少参数');
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('异常请求');
        }

        global $wpdb;
        $table_post_favorite = $wpdb->prefix . 'zhuige_theme_post_favorite';
        $post_favorite_id = zhuige_theme_is_favorite($my_user_id, $post_id);

        $is_favorite = 0;
        if ($post_favorite_id) {
            $wpdb->query("DELETE FROM `$table_post_favorite` WHERE `id`=$post_favorite_id");
            $is_favorite = 0;
        } else {
            $wpdb->insert($table_post_favorite, [
                'user_id' => $my_user_id,
                'post_id' => $post_id,
                'time' => time()
            ]);
            $is_favorite = 1;
        }

        wp_send_json_success(['is_favorite' => $is_favorite]);
    } else if ($action == 'get_posts') { // 查询文章
        $offset = isset($_POST["offset"]) ? (int)($_POST["offset"]) : 0;
        $page_count = isset($_POST["page_count"]) ? (int)($_POST["page_count"]) : 10;
        $cat = isset($_POST["cat"]) ? (int)($_POST["cat"]) : '';
        $tag = isset($_POST["tag"]) ? (int)($_POST["tag"]) : '';
        $ss = isset($_POST["ss"]) ? sanitize_text_field($_POST["ss"]) : '';
        $author = isset($_POST["author"]) ? (int)($_POST["author"]) : '';
        $result = zhuige_theme_get_posts($offset, ['cat' => $cat, 'tag' => $tag, 'ss' => $ss, 'author' => $author, 'page_count' => $page_count]);
        wp_send_json_success($result);
    } else if ($action == 'get_posts_user') { // 获取我的文章
        $offset = isset($_POST["offset"]) ? (int)($_POST["offset"]) : 0;
        $user_id = isset($_POST["user_id"]) ? (int)($_POST["user_id"]) : 0;
        $track = isset($_POST["track"]) ? sanitize_text_field($_POST["track"]) : '';
        if ($track == 'like' || $track == 'favorite') {
            $result = zhuige_theme_posts_user_like_fav($user_id, $offset, $track);
        } else if ($track == 'comment') {
            $result = zhuige_theme_posts_user_comment($user_id, $offset);
        } else {
            wp_send_json_error('缺少参数');
        }

        wp_send_json_success($result);
    }

    die;
}


//----------------------------------------------
/**
 * 上传图片
 */
add_action('wp_ajax_nopriv_ajax_upload_image', 'ajax_upload_image');
add_action('wp_ajax_ajax_upload_image', 'ajax_upload_image');

function _get_rand_name($filename, $prefix = 'zg')
{
    //取出源文件后缀
    $ext = strrchr($filename, '.');
    //构建新名字
    $new_name = $prefix . time();
    //增加随机字符（6位大写字母）
    for ($i = 0; $i < 6; $i++) {
        $new_name .= chr(mt_rand(65, 90));
    }
    //返回最终结果
    return $new_name . $ext;
}

function ajax_upload_image()
{
    header("Content-Type: application/json");

    $user_id = get_current_user_id();
    if (empty($user_id)) {
        echo json_encode(['error' => '尚未登录']);
        die;
    }

    $file = $_FILES['image'];
    $upload_dir = wp_upload_dir(null, false);
    $filename = _get_rand_name($file['name']);
    $filepath = $upload_dir['path'] . '/' . $filename;
    $url = '';
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $res = zhuige_theme_import_image2attachment($filepath);
        if (!is_wp_error($res)) {
            $filename = $res;
        }
        $url = $upload_dir['url'] . '/' . $filename;
    }

    $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : '';
    if ($type == 'user_cover') {
        update_user_meta($user_id, 'zhuige_theme_cover', $url);
    }

    echo json_encode(['url' => $url]);
    die;
}
//----------------------------------------------


//----------------------------------------------
/**
 * 设置用户信息
 */
add_action('wp_ajax_nopriv_ajax_set_user_info', 'ajax_set_user_info');
add_action('wp_ajax_ajax_set_user_info', 'ajax_set_user_info');

function ajax_set_user_info()
{
    // header("Content-Type: application/json");

    $user_id = get_current_user_id();
    if (empty($user_id)) {
        wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
    }

    $nickname = isset($_POST["nickname"]) ? sanitize_text_field($_POST["nickname"]) : '';
    if (!empty($nickname)) {
        wp_update_user([
            'ID' => $user_id,
            'nickname' => $nickname,
            'user_nicename' => $nickname,
            'display_name' => $nickname,
        ]);
    }

    $gender = isset($_POST["gender"]) ? sanitize_text_field($_POST["gender"]) : '';
    update_user_meta($user_id, 'zhuige_theme_gender', $gender);

    $city = isset($_POST["city"]) ? sanitize_text_field($_POST["city"]) : '';
    update_user_meta($user_id, 'zhuige_theme_city', $city);

    $web = isset($_POST["web"]) ? sanitize_url($_POST["web"]) : '';
    update_user_meta($user_id, 'zhuige_theme_web', $web);

    $sign = isset($_POST["sign"]) ? sanitize_text_field($_POST["sign"]) : '';
    update_user_meta($user_id, 'description', $sign);

    $avatar = isset($_POST["avatar"]) ? sanitize_url($_POST["avatar"]) : '';
    if (!empty($avatar)) {
        update_user_meta($user_id, 'zhuige_user_avatar', $avatar);
    }

    $cover = isset($_POST["cover"]) ? sanitize_url($_POST["cover"]) : '';
    if (!empty($cover)) {
        update_user_meta($user_id, 'zhuige_theme_cover', $cover);
    }

    $reward_code = isset($_POST["reward_code"]) ? sanitize_text_field($_POST["reward_code"]) : '';
    if (!empty($reward_code)) {
        update_user_meta($user_id, 'zhuige_theme_reward_code', $reward_code);
    }

    $wx_code = isset($_POST["wx_code"]) ? sanitize_text_field($_POST["wx_code"]) : '';
    if (!empty($wx_code)) {
        update_user_meta($user_id, 'zhuige_theme_wx_code', $wx_code);
    }

    wp_send_json_success();
}
//----------------------------------------------

/**
 * 修改密码
 */
add_action('wp_ajax_nopriv_modify_password', 'modify_password');
add_action('wp_ajax_modify_password', 'modify_password');

function modify_password()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['error' => 'login', 'msg' => '尚未登录']);
    }

    $oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';
    $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
    $renewpassword = isset($_POST['renewpassword']) ? $_POST['renewpassword'] : '';
    if (!$oldpassword || !$newpassword || !$renewpassword) {
        wp_send_json_error('缺少参数');
    }

    global $wpdb;

    $user_data = get_userdata($user_id);
    if (is_wp_error(wp_authenticate($user_data->data->user_login, $oldpassword))) {
        wp_send_json_error('旧密码不正确');
    }

    if ($newpassword != $renewpassword) {
        wp_send_json_error('新密码不一致');
    }

    $wpdb->update($wpdb->users, array('user_pass' => md5($newpassword)), array('ID' => $user_id));

    wp_send_json_success();
}
//----------------------------------------------

/**
 * 微信登录回调
 */
add_action('wp_ajax_nopriv_weixin_login_callback', 'weixin_login_callback');
add_action('wp_ajax_weixin_login_callback', 'weixin_login_callback');
function weixin_login_callback()
{
    $code = isset($_GET['code']) ? $_GET['code'] : '';
    if (empty($code)) {
        wp_redirect(home_url());
        die;
    }

    $app_id = zhuige_theme_option('wx_app_id');
    $app_secret = zhuige_theme_option('wx_app_secret');
    $params = [
        'appid' => $app_id,
        'secret' => $app_secret,
        'code' => $code,
        'grant_type' => 'authorization_code'
    ];

    $result = wp_remote_get(add_query_arg($params, 'https://api.weixin.qq.com/sns/oauth2/access_token'));
    if (!is_array($result) || is_wp_error($result) || $result['response']['code'] != '200') {
        wp_redirect(home_url());
        die;
    }

    $body = stripslashes($result['body']);
    $wx_session = json_decode($body, true);

    if ($wx_session['errcode']) {
        wp_redirect(home_url());
        die;
    }

    $params = [
        'access_token' => $wx_session['access_token'],
        'openid' => $wx_session['openid'],
    ];

    $result = wp_remote_get(add_query_arg($params, 'https://api.weixin.qq.com/sns/userinfo'));
    if (!is_array($result) || is_wp_error($result) || $result['response']['code'] != '200') {
        wp_redirect(home_url());
        die;
    }

    $body = stripslashes($result['body']);
    $user_data = json_decode($body, true);

    // 用户登录 -- start -- 
    $nickname = $user_data['nickname'];

    $user_login = ((isset($user_data['unionid']) && $user_data['unionid']) ? $user_data['unionid'] : $user_data['openid']);
    $email_domain = '@' . zhuige_theme_option('zhuige_user_email_domain', 'zhuige.com');
    $user = get_user_by('email', $user_login . $email_domain);
    if (!$user) {
        $user_id = wp_insert_user([
            'user_login' => $user_login,
            'nickname' => $nickname,
            'user_nicename' => uniqid(),
            'display_name' => $nickname,
            'user_email' => $user_login . $email_domain,
            'role' => 'subscriber',
            'user_pass' => wp_generate_password(16, false),
        ]);

        if (is_wp_error($user_id)) {
            wp_redirect(home_url());
            die;
        }
    } else {
        $user_id = $user->ID;
    }

    //如果每次都同步微信头像 会导致小程序设置的头像失效；所以没有头像时，才同步头像
    $avatar = get_user_meta($user_id, 'zhuige_user_avatar', true);
    if (!$avatar || strstr($avatar, 'wx.qlogo.cn')) {
        $new_avatar = zhuige_theme_download_wx_avatar($user_data['headimgurl'], $user_id);
        if ($new_avatar) {
            $new_avatar_url = $new_avatar['url'];
            $dres = zhuige_theme_import_image2attachment($new_avatar['path']);
            if (!is_wp_error($dres)) {
                $upload_dir = wp_upload_dir();
                $new_avatar_url = $upload_dir['url'] . '/' . $dres;
            }
            update_user_meta($user_id, 'zhuige_user_avatar', $new_avatar_url);
        }
    }

    // 用户登录 -- end -- 

    // 自动登录
    wp_set_current_user($user_id, $user->user_login);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $user->user_login, $user);


    $r = isset($_GET['state']) ? urldecode($_GET['state']) : home_url();
    wp_redirect($r);

    die;
}

/**
 * 市场相关
 */
add_action('wp_ajax_nopriv_zhuige_market_event', 'zhuige_market_event');
add_action('wp_ajax_zhuige_market_event', 'zhuige_market_event');
function zhuige_market_event()
{
    $action = isset($_POST["zgaction"]) ? sanitize_text_field(wp_unslash($_POST["zgaction"])) : '';

    if ($action == 'get_list') { // 查询产品
        $cat = isset($_POST["cat"]) ? (int)($_POST["cat"]) : 0;
        $params = [];
        if ($cat) {
            $params['cat'] = $cat;
        }

        $free = isset($_POST["free"]) ? sanitize_text_field($_POST["free"]) : '';
        if ($free !== '') {
            $params['free'] = $free;
        }

        $init = isset($_POST["init"]) ? (int)($_POST["init"]) : 0;
        if ($init == 1) {
            $params['init'] = $init;
        }

        $response = wp_remote_post("https://www.zhuige.com/api/market/list", array(
            'method'      => 'POST',
            'body'        => $params
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);
        $datadata = $data['data'];

        if ($data['code'] == 1) {
            wp_send_json_success($datadata);
        } else {
            wp_send_json_error();
        }
    }

    die;
}
