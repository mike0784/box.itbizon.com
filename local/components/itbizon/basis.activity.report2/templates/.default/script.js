$(document).on('click', '.show__popup', function (e) {
    e.preventDefault();

    let path = $(this).data('path');
    $.ajax({
        type: "GET",
        url: path,
        success: function (response) {
            generateForm(response.data)
        },
        error: function (jqXHR) {
            try {
                let data = jQuery.parseJSON(jqXHR.responseText);
                generateForm(data.message)
            } catch (e) {
                generateForm(jqXHR.responseText)
            }
        },
    });
});

function generateForm(content) {

    let form = BX.PopupWindowManager.create('reportPopup', null, {
        width: 1000,
        height: document.body.clientHeight - 100,
        offsetTop: 0,
        titleBar: 'Отчет',
        draggable: true,
        darkMode: false,
        autoHide: false,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: true,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Закрыть',
                className: 'webform-button-link-cancel',
                events: {
                    click: function () {
                        this.popupWindow.close();
                        this.popupWindow.destroy();
                    }
                }
            }),
        ]
    });
    form.setContent(content);
    form.show();
}