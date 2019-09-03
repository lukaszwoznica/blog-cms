$(document).ready(function () {
    $('#formSignup').validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 50,
                remote: '/user/account/validate-username'
            },
            email: {
                required: true,
                email: true,
                remote: '/user/account/validate-email'
            },
            password: {
                required: true,
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
        }
    });

    $('#inputPassword').hideShowPassword({
       show: false,
       innerToggle: 'focus'
    })
});

