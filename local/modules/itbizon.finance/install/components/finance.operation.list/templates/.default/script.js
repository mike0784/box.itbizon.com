function request(path, message = null) {
    let conf = true;
    if (message != null) conf = confirm(message)

    if (conf) {
        $.ajax({
            type: "GET",
            url: path,
            success: function () {
                let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
                let gridObject = BX.Main.gridManager.getById('finance_operation_list1'); // Идентификатор грида

                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', reloadParams);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON) {
                    alert(xhr.responseJSON.message)
                } else {
                    alert(BX.message('ITB_FIN.OPERATION_LIST.MESS.ERROR.UNKNOWN'));
                }
            },
        });
    }
    return 0;
}