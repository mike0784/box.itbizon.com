$(document).ready(function () {
    const body = $('body');
    //show popup
    $(document).on('click', '#showPopup', function () {

        $.ajax({
            type: "GET",
            url: $('#showPopup').data('path'),
            success: function (response) {
                $('.container').append(response.data);
                body.addClass('modal-open');
                body.append('<div class="modal-backdrop show"></div>')
            },
            error: function (xhr) {
            },
        });
    });

    $(document).on('click', '#deleteFine', function (e) {
        e.preventDefault();

        let link = $(this);
        $.ajax({
            type: "POST",
            url: link.data('path'),
            success: function (response) {
                location.reload(true);
            },
            error: function (xhr) {
            },
        });
    });

    $(document).on('click', '.close', function () {
        body.removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('#popup').remove();
    });

    $(document).on('submit', '#createFines', function (e) {
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