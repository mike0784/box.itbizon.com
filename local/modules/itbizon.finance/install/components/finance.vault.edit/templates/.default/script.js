$(document).ready(function () {
    historyUpdateHandler();
    accessRightUpdate();

    $("#append-access-right").on("submit", function (event) {
        event.preventDefault();
        const form = $(this);
        const action = $("#finance-vault-edit-container").data("action");
        $.ajax({
            type: "POST",
            url: action,
            data: form.serialize(),
            success: function (xhr) {
                BX.UI.Notification.Center.notify({
                    content: xhr.message
                });
                accessRightUpdate();
            },
            error: function (xhr) {
                BX.UI.Notification.Center.notify({
                    content: xhr.responseJSON.message
                });
                console.log(xhr);
            },
        });
    });
});

function accessRightDeleteHandler() {
    $("#access-rights-list .remove-access[data-id]").on("click", function () {
        const action = $("#finance-vault-edit-container").data("action");
        $.ajax({
            type: "POST",
            url: action,
            data: {
                ACTION: "REMOVE_ACCESS",
                ID: $(this).data("id")
            },
            success: function (xhr) {
                BX.UI.Notification.Center.notify({
                    content: xhr.message
                });
                accessRightUpdate();
            },
            error: function (xhr) {
                BX.UI.Notification.Center.notify({
                    content: xhr.responseJSON.message
                });
                console.log(xhr);
            },
        });
    });
}

function historyUpdateHandler() {
    let history = $("#history button");
    history.on("click", function () {
        const form = $("#history");
        const action = $("#finance-vault-edit-container").data("action");
        $.ajax({
            type: "POST",
            url: action,
            data: form.serialize(),
            success: function (response) {
                $("#vault-history").html(response.data);
            },
            error: function (xhr) {
                console.log(xhr);
            },
        });
        return 0;
    });
    history.click();
}

function accessRightUpdate() {
    const action = $("#finance-vault-edit-container").data("action");
    let accessRightsList = $("#access-rights-list");
    $.ajax({
        type: "POST",
        url: action,
        data: {
            ACTION: "GET_ACCESS",
            ID: $(accessRightsList).data("vault-id")
        },
        success: function (response) {
            $(accessRightsList).html(response.data);
            accessRightDeleteHandler();
        },
        error: function (xhr) {
            console.log(xhr);
        },
    });
}