$(document).ready(function () {
    //delete fine
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

    //show popup
    $(document).on('click', '#showPopup', function () {
        $.ajax({
            type: "GET",
            url: $('#showPopup').data('path'),
            success: function (response) {
                createPopup(response.data);
            },
            error: function (xhr) {
            },
        });
    });

    //create popup function: get popup and form
    let createPopup = function (html) {
        let popupWindow = new BX.PopupWindow(
            "create_popup",
            null,
            {
                closeIcon: {right: "20px", top: "10px"},
                zIndex: 0,
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: false},
                overlay: {
                    background: 'black',
                    opacity: '20'
                },
                buttons: [
                    new BX.PopupWindowButton({
                        text: "Сохранить",
                        className: "popup-window-button-accept",
                        events: {
                            click: function () {
                                let form = $('#createFines');
                                let popupWindow = this.popupWindow;
                                $.ajax({
                                    type: "POST",
                                    url: form.attr('action'),
                                    data: form.serialize(),
                                    beforeSend: function (xhr) {
                                        $('.is-invalid').removeClass('is-invalid');
                                        $('.invalid-feedback').remove();
                                    },
                                    success: function (response) {
                                        location.reload(true);
                                        popupWindow.destroy();
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
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: "Закрыть",
                        className: "webform-button-link-cancel",
                        events: {
                            click: function () {
                                this.popupWindow.close();
                                this.popupWindow.destroy();
                            }
                        }
                    })
                ]
            });
        popupWindow.setContent(html);
        popupWindow.show()
    };
});