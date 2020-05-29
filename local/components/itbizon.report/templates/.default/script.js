$(document).ready(function() {

    $("input").on("input", function() {
        BX.setCookie("REPORT_" + this.id.toUpperCase(), this.value);
        reload();
    });

    $("select").on("change", function() {
        BX.setCookie("REPORT_" + this.id.toUpperCase(), this.value);
        reload();
    });

    $(".change-date").on("click", function() {
        let from = new Date($("#from").val());
        let to = new Date($("#to").val());

        const interval = to.getDate() - from.getDate() + 1;

        if(this.id == 'prev') {
            from.setDate(from.getDate() - interval);
            to.setDate(to.getDate() - interval);
        } else if(this.id == 'next') {
            from.setDate(from.getDate() + interval);
            to.setDate(to.getDate() + interval);
        }

        BX.setCookie("REPORT_FROM", from.toISOString().substr(0, 10));
        BX.setCookie("REPORT_TO", to.toISOString().substr(0, 10));

        reload();
        return false;
    });

    $(".task-done-list").on("click", showTaskTable);

});

function reload() {
    location.reload();
}

function createPopup(html, name, func = ()=>{}) {
    let popupWindow = new BX.PopupWindow(
        name,
        null,
        {
            closeIcon: {right: "25px", top: "25px"},
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
                            // let form = $(name);
                            let popupWindow = this.popupWindow;
                            // console.log("click", form.attr('action'), form.serialize());
                            popupWindow.destroy();
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
    popupWindow.show();
};

function showTaskTable() {

    const id = $(this).parents("tr").first().attr("id");

    $.ajax({
        type: "GET",
        url: pathAjax + "?taskDone=" + id,
        success: function (response) {
            console.log(pathAjax + "?taskDone=" + id);
            console.log(response);
            // createPopup(response.data, "task-done", function() {
            // });
        },
        error: function (xhr) {
            console.log(pathAjax + "?taskDone=" + id);
            console.log(xhr);
        },
    });

    return false;
}