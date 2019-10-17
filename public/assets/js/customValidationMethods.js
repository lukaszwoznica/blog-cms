$.validator.addMethod('validPassword',
    function (value) {
        if (value !== '') {
            if (value.match(/.*[a-z]+.*/i) === null) {
                return false
            }
            if (value.match(/.*\d+.*/) === null) {
                return false
            }
        }
        return true
    },
    'Password must contain at least one letter and one number.'
);

$.validator.addMethod('validUsernameCharsRange',
    function (value) {
        if (value !== '') {
            if (value.match(/^[a-z0-9_]+$/i) === null) {
                return false
            }
        }
        return true
    },
    'Username can only contain alphanumeric characters (letters A-Z, numbers 0-9) and underscore.'
);

$.validator.addMethod('validUsernameStartAndEnd',
    function (value) {
        if (value !== '') {
            if (value.match(/^([a-z]+).*([a-z0-9]+)$/i) === null) {
                return false
            }
        }
        return true
    },
    'Username must start with a letter and end with a letter or number.'
);

$.validator.addMethod('validSlug',
    function (value) {
        if (value !== '') {
            if (value.match(/^[a-z0-9-]+$/) === null) {
                return false
            }
        }
        return true
    },
    'Slug can only contain alphanumeric characters (lowercase letters a-z, numbers 0-9) and dash.'
);