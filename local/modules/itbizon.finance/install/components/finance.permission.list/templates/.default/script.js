$(document).ready(function () {
    if (!BX.SidePanel.Instance.isOpen()) {
        BX.addCustomEvent("SidePanel.Slider:onCloseComplete", function (event) {
            if (event.slider.data.get("close") === true) {
                resetGrid();
            }
        });
    }
});

function remove(path, id) {
    // noinspection JSAnnotator
    if (confirm(BX.message("ITB_FINANCE.PERMISSION.LIST.SCRIPT.MESSAGE.CONFIRM")))
        $.ajax({
            type: "POST",
            url: path,
            data: {
                ID: id
            },
            success: function (r) {
                console.log(r);
                resetGrid();
            },
            error: function (xhr) {
                console.log(xhr);
                if (xhr.responseJSON) {
                    alert(xhr.responseJSON.message)
                } else {
                    // noinspection JSAnnotator
                    alert(BX.message("ITB_FINANCE.PERMISSION.LIST.SCRIPT.ERRORS.UNKNOW"));
                }
            },
        });
    return 0;
}

function resetGrid() {
    let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
    let gridObject = BX.Main.gridManager.getById('itb_finance_permission_list'); // Идентификатор грида

    if (gridObject.hasOwnProperty('instance')) {
        gridObject.instance.reloadTable('POST', reloadParams);
    }
}