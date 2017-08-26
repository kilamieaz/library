$(document).ready(function () {
    // confirm delete
    $(document.body).on('submit', '.js-confirm', function () {
        var $el = $(this)
        var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin melakukan tindakan ini ?'
        var c = confirm(text);
        return c;
        // add selectize to select element
        $('#select-selectized').selectize();
        });
        $('#select-multiple-selectized').selectize({
            delimiter: ',',
            persist: false,
            onDelete: function(values) {
                return confirm(values.length > 1 ? 'Are you sure you want to remove these ' + values.length + ' items?' : 'Are you sure you want to remove "' + values[0] + '"?');
            }
        });
    });
    //delete review book
    $(document.body).on('submit', '.js-review-delete', function () {
        var $el  = $(this);
        var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin melakukan tindakan ini ?';
        var c    = confirm(text);
        // cancel delete
        if (c === false) return c;
    // delete via ajax
    // disable behaviour default dari tombol submit
    
        event.preventDefault();
        // hapus data buku dengan ajax
        $.ajax({
            type    : 'POST',
            url     : $(this).attr('action'),
            dataType: 'json',
            data    : {
                _method : 'DELETE',
                // menambah csrf token dari laravel 
                _token  : $(this).children('input[name=_token]'.val())
            }
        }).done(function(data) {
            // cari baris yang di hapus
            baris = $('#form-'+data.id).closest('tr');
            // hilangkan baris (fadeout kemudian remove)
            baris.fadeOut(300, function() {$(this).remove});
        });
    });
});
