$(document).ready(function () {
    const titleInput = document.getElementById('inputTitle');
    titleInput.addEventListener('input', titleToSlug, false);

    $('textarea#textareaIntroduction').characterCounter();

    const options = {
        rules: {
            title: {
                required: true,
                minlength: 3,
                maxlength: 255,
            },
            url_slug: {
                minlength: 3,
                maxlength: 255,
                required: true,
                validSlug: true,
                remote: {
                    url: '/admin/posts/validate-slug'
                }
            },
            introduction: {
                maxlength: 255
            }
        },
        messages: {
            url_slug: {
                remote: 'URL slug is already taken'
            },
        },
        errorElement: 'div',
        errorClass: 'invalid'
    };

    $('#formNewPost').validate(options);

    options['rules']['url_slug']['remote']['data'] = {
        ignore_id: function () {
            return post_id;
        }
    };

    $('#formEditPost').validate(options);
});
