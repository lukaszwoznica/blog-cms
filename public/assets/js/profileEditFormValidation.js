$(document).ready(function () {
    $('#formEditProfile').validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 25,
                validUsernameCharsRange: true,
                validUsernameStartAndEnd: true,
                remote: {
                    url: '/user/account/validate-username',
                    data: {
                        ignore_id: function () {
                            return user_id;
                        }
                    }
                }
            },
            email: {
                required: true,
                email: true,
                remote: {
                    url: '/user/account/validate-email',
                    data: {
                        ignore_id: function () {
                            return user_id;
                        }
                    }
                }
            },
            password: {
                minlength: 6,
                validPassword: true
           }
        },
        messages: {
            username: {
                remote: 'Username is already taken'
            },
            email: {
                remote: 'Email is already taken'
            }
        },
        errorElement: 'div',
        errorClass: 'invalid'
    });
});

