(function ($) {
    'use strict';

    $(document).ready(function () {
        var mediaUploader;

        $('#zhuige-btn-select-image').click(function (e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: '选择图片',
                button: {
                    text: '选择图片'
                },
                multiple: false
            });

            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on('select', function () {
                let attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#zhuige-img-user-avatar').attr('src', attachment.url)
                $('#zhuige-user-avatar-value').val(attachment.url);
            });
            // Open the uploader dialog
            mediaUploader.open();
        });

        $('#zhuige-btn-reset-image').click(function (e) {
            let default_avatar = $(this).data('default');
            $('#zhuige-user-avatar-value').val(default_avatar);
            $('#zhuige-img-user-avatar').attr('src', default_avatar);
        });
    });

})(jQuery);