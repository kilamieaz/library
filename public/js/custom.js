$(document).ready(function () {
// confirm delete
    $(document.body).on('submit', '.js-confirm', function () {
        var $el = $(this)
        var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin melakukan tindakan ini ?'
        var c = confirm(text);
        return c;
    });

    // add selectize to select element
    $('#select-beast').selectize({
    create: true,
    sortField: 'text'
    });
});
