$(document).ready(function () {
    $('.sidenav').sidenav({
        draggable: true
    });

    $(".dropdown-trigger").dropdown({
        coverTrigger: false
    });

    $('.alert > button').on('click', function () {
        $(this).closest('div.alert').fadeOut('slow');
    });

    $('select').formSelect();
    $('.tooltipped').tooltip();
    $('.materialboxed').materialbox();
    $('.modal').modal();
    AOS.init({
        once: true
    });
});