function deleteConfirm(item_name = "") {
    return confirm('Are you sure you want to delete this ' + item_name + '?');
}

function searchData(url, query) {
    $.ajax({
        url: url,
        method: "get",
        data: {search_query: query},
        success: function(data) {
            $(document.body).html(data);
        }
    });
}

$(document).ready(function () {
    AOS.init();

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

});