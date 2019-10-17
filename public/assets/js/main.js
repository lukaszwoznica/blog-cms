function deleteConfirm(item_name = "") {
    return confirm('Are you sure you want to delete this ' + item_name + '?');
}

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

    $('select').formSelect();
});