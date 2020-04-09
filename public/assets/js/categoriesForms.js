$(document).ready(function () {
    const nameInput = document.getElementById('inputName');
    nameInput.addEventListener('input', titleToSlug, false);
    const options = {
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 100,
            },
            url_slug: {
                minlength: 3,
                maxlength: 255,
                required: true,
                validSlug: true,
                remote: {
                    url: '/admin/categories/validate-slug'
                }
            },
            description: {
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

    $('#formNewCategory').validate(options);

    options['rules']['url_slug']['remote']['data'] = {
        ignore_id: function () {
            return category_id;
        }
    };

    $('#formEditCategory').validate(options);
});