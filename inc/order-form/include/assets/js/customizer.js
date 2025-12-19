

jQuery(document).ready(function($) {
    wp.customize.bind('ready', function() {
        var control = $('#customize-control-thanks_control textarea');

        if (control.length) {
            control.after('<div id="mytheme-wysiwyg-editor"></div>');
            var editorId = 'mytheme-wysiwyg-editor';

            wp.editor.initialize(editorId, {
                tinymce: {
                    wpautop: true,
                    plugins: 'link, paste, media',
                    toolbar1: 'bold,italic,underline,|,bullist,numlist,|,link,unlink,|,undo,redo',
                },
                quicktags: true,
            });

            var editor = tinymce.get(editorId);

            if (editor) {
                editor.on('change', function(e) {
                    control.val(editor.getContent()).trigger('change');
                });
            }
        }
    });
});
