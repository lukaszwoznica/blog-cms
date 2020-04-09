function trimSelectValue() {
    const select_dropdown = $('.select-dropdown');
    const selected_val = select_dropdown.val();
    select_dropdown.val($.trim(selected_val));
}

$(document).ready(function () {
    trimSelectValue();

    $('#selectCategory').change(function () {
        trimSelectValue()
    });
    $('.select-dropdown').click(function () {
        trimSelectValue()
    });
});
