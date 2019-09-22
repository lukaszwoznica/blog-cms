$(document).ready(function () {
    $('.sidenav').sidenav({
        draggable: true
    });

    $(".dropdown-trigger").dropdown({
        coverTrigger: false
    });

    $('.alert > button').on('click', function(){
        $(this).closest('div.alert').fadeOut('slow');
    })
});