function slugify(string) {
    const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;';
    const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnooooooooprrsssssttuuuuuuuuuwxyyzzz------';
    const p = new RegExp(a.split('').join('|'), 'g');

    return string.toString().toLowerCase()
        .replace(/\s+/g, '-') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '-and-') // Replace & with 'and'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '-') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
}

function toggleEdit() {
    edit_icon.classList.toggle('active');
    slugInput.readOnly = slugInput.readOnly !== true;
}

const titleToSlug = function (evt) {
    slugInput.value = slugify(evt.target.value);
};

const titleInput = document.getElementById('inputTitle');
const slugInput = document.getElementById('inputSlug');
titleInput.addEventListener('input', titleToSlug, false);

const edit_icon = document.getElementById('slugEditIcon');
edit_icon.addEventListener('click', toggleEdit);

$(document).ready(function () {
    $('textarea#textareaIntroduction').characterCounter();

    $('#formNewPost').validate({
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
                remote: '/admin/posts/validate-slug'
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
    });

    $('#formEditPost').validate({
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
                    url: '/admin/posts/validate-slug',
                    data: {
                        ignore_id: function () {
                            return post_id;
                        }
                    }
                }
            },
        },
        messages: {
            url_slug: {
                remote: 'URL slug is already taken'
            },
        },
        errorElement: 'div',
        errorClass: 'invalid'
    });
});
