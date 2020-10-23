$(document).ready(function () {

    $("#shipPopup").on("click", async function () {
        let path = $(this).data('path');
        let e =  $("#submit");
        let form = $(e.parent("form"));
        let newID = null;

        if(path.indexOf('StationID=0') !== false) {
            newID = await saveform(form.attr('action'), form.serialize());
            path = path.replace('StationID=0', 'StationID='+newID);
        }

        $.ajax({
            type: "GET",
            url: path,
            success: function (response) {
                createPopup(response.data, "#editShip", function() {
                    console.log(newID);
                    if(newID)
                        window.location.pathname = window.location.pathname.replace('/edit/0', '/edit/'+newID);
                });
            },
            error: function (xhr) {
                console.log(xhr);
            },
        });

    });

    $("#submit").on("click", async function () {
        e = $(this);
        let form = $(e.parent("form"));

        await saveform(form.attr('action'), form.serialize());

        // location.reload(true);
        window.location.reload();
        return false;
    });

    window.saveform = async function(url, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function (response) {
                    resolve(response.data);
                },
                error: function (xhr) {
                    reject(new Error(xhr))
                },
            });
        });
    }

    $(".removeShip").on("click", function () {
        e = $(this);
        $.ajax({
            type: "GET",
            url: e.data('path'),
            success: function () {
                e.parents("tr")[0].remove();
            },
            error: function (xhr) {
                console.log(xhr);
            },
        });

        return false;
    });

    let createPopup = function (html, name, func = ()=>{}) {
        let popupWindow = new BX.PopupWindow(
            name,
            null,
            {
                closeIcon: {right: "5px", 'padding-bottom': "10px"},
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
                                let form = $(name);
                                let popupWindow = this.popupWindow;
                                console.log("click", form.attr('action'), form.serialize());
                                $.ajax({
                                    type: "POST",
                                    url: form.attr('action'),
                                    data: form.serialize(),
                                    beforeSend: function (xhr) {
                                        $('.is-invalid').removeClass('is-invalid');
                                        $('.invalid-feedback').remove();
                                    },
                                    success: function (response) {
                                        console.log(response);
                                        func();
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