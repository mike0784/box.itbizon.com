function removeVault(path) {
    if (confirm(BX.message('ITB_FIN.VAULT_LIST.MESS.CONFIRM_DELETE_VAULT')))
        $.ajax({
            type: "GET",
            url: path,
            success: function () {
                let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
                let gridObject = BX.Main.gridManager.getById('finance_vault_list'); // Идентификатор грида

                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', reloadParams);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON) {
                    alert(xhr.responseJSON.message)
                } else {
                    alert(BX.message('ITB_FIN.VAULT_LIST.MESS.ERROR.UNKNOWN'));
                }
            },
        });
    return 0;
}
function removeVaultGroup(path) {
    if (confirm(BX.message('ITB_FIN.VAULT_LIST.MESS.CONFIRM_DELETE_GROUP')))
        $.ajax({
            type: "GET",
            url: path,
            success: function () {
                let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
                let gridObject = BX.Main.gridManager.getById('finance_vault_list'); // Идентификатор грида

                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', reloadParams);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON) {
                    alert(xhr.responseJSON.message)
                } else {
                    alert(BX.message('ITB_FIN.VAULT_LIST.MESS.ERROR.UNKNOWN'));
                }
            },
        });
    return 0;
}