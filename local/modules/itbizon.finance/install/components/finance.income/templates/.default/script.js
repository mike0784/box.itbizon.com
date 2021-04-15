$(document).ready(function () {
    $('#field-company').select2();

    $("#form-module").on("submit", function (event) {
        event.preventDefault();
        let form = this;

        $.ajax({
            type: "POST",
            url: window.location.pathname,
            data: $(this).serialize(),
            success: function (xhr) {
                // BX.UI.Notification.Center.notify({
                //     content: xhr.message
                // });
                createPopup("<div class='custom-window-message alert alert-success'>"+xhr.message+"</div>", "operation_success");
                form.reset();
                console.log(xhr);
            },
            error: function (xhr) {
                console.log(xhr);
                BX.UI.Notification.Center.notify({
                    content: xhr.responseJSON.message
                });
            },
        });
    });
});


window.popupWindow = Object;

function createPopup(html, name) {
    if (window.popupWindow[name] === undefined) {
        window.popupWindow[name] = new BX.PopupWindow(
            name,
            null,
            {
                closeIcon: {right: "10px", top: "10px"},
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
                        text: "Закрыть",
                        className: "popup-window-button-accept",
                        events: {
                            click: function () {
                                window.popupWindow[name].close();
                                // this.popupWindow.destroy();
                            }
                        }
                    })
                ]
            });
    }

    window.popupWindow[name].setContent(html);
    window.popupWindow[name].show();
}