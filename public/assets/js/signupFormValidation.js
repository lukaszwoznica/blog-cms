$.validator.addMethod('validPassword',
    function (value, element, param) {
        if (value !== '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false
            }
            if (value.match(/.*\d+.*/) == null) {
                return false
            }
        }
        return true
    },
    'Must contain at least one letter and one number'
);

$(document).ready(function () {
    $('#formSignup').validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 50,
                remote: '/signup/validate-username'
            },
            email: {
                required: true,
                email: true,
                remote: '/signup/validate-email'
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

