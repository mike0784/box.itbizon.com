$(document).ready(function () {
    $(document).on('submit', '#editFine', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            beforeSend: function (xhr) {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            },
            success: function (response) {
                location.reload(true);
            },
            statusCode: {
                400: function (xhr) {
                    const data = JSON.parse(xhr.responseText);
                    $.each(data.data, function (key, message) {
                        let item = $('#' + key);
                        item.removeClass('is-valid');
                        item.addClass('is-invalid');
                        item.closest('.form-group').append('<div class="invalid-feedback">' + message + '</div>')
                    });
                },
            },
            error: function (xhr) {
                if (xhr.status !== 400) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                }
            },
        });
    });
});