$(document).ready(function () {
    $('.sidenav').sidenav();

    $('.alert > button').on('click', function(){
        $(this).closest('div.alert').fadeOut('slow');
    })
});