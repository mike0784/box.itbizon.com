/**
 * @param ajaxPath
 * @param gridId
 * @param requestId
 */
function showChangeVaultPopup(ajaxPath, gridId, requestId) {
    let popupId = 'change-vault-popup';
    let formId  = 'change-vault-form';
    let popup = BX.PopupWindowManager.create(popupId, null, {
        width: 400,
        titleBar: 'Сменить кошелек',
        draggable: true,
        darkMode: false,
        autoHide: false,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: true,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Сохранить',
                className: 'webform-button-link-create',
                events: {
                    click: function () {
                        let thisPopup = this;
                        let form = $('.'+formId);
                        if(form !== undefined) {
                            let requestId = form.find('[name=requestId]').val();
                            let vaultId   = form.find('[name=vaultId]').val();
                            let autoApprove = (form.find('[name=autoApprove]').is(':checked')) ? 1 : 0;
                            command(
                                ajaxPath,
                                {
                                    action: 'change-vault',
                                    requestId: requestId,
                                    vaultId: vaultId,
                                    autoApprove: autoApprove
                                },
                                function(data) {
                                    thisPopup.popupWindow.close();
                                    thisPopup.popupWindow.destroy();
                                    reloadGrid(gridId);
                                }
                            );
                        } else {
                            customNotify('Form not found');
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                className: 'webform-button-link-cancel',
                events: {
                    click: function () {
                        this.popupWindow.close();
                        this.popupWindow.destroy();
                    }
                }
            })
        ]
    });
    command(
        ajaxPath,
        {
            action: 'get-form',
            formId: formId,
            requestId: requestId
        },
        function(content) {
            popup.setContent(content);
            popup.show();
        }
    );
}

/**
 * @param ajaxPath
 * @param gridId
 * @param requestId
 */
function renewRequest(ajaxPath, gridId, requestId) {
    command(
        ajaxPath,
        {
            action: 'renew',
            requestId: requestId,
        },
        function(data) {
            reloadGrid(gridId);
        }
    );
}

/**
 * @param ajaxPath
 * @param gridId
 * @param requestId
 */
function showApprovePopup(ajaxPath, gridId, requestId) {
    let popupId = 'approve-popup';
    let formId  = 'approve-form';
    let popup = BX.PopupWindowManager.create(popupId, null, {
        width: 400,
        titleBar: 'Утвердить заявку',
        draggable: true,
        darkMode: false,
        autoHide: false,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: true,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Сохранить',
                className: 'webform-button-link-create',
                events: {
                    click: function () {
                        let thisPopup = this;
                        let form = $('.'+formId);
                        if(form !== undefined) {
                            let requestId = form.find('[name=requestId]').val();
                            let amount    = form.find('[name=amount]').val();
                            let comment   = form.find('[name=comment]').val();
                            command(
                                ajaxPath,
                                {
                                    action: 'approve',
                                    requestId: requestId,
                                    amount: amount,
                                    comment: comment
                                },
                                function(data) {
                                    thisPopup.popupWindow.close();
                                    thisPopup.popupWindow.destroy();
                                    reloadGrid(gridId);
                                }
                            );
                        } else {
                            customNotify('Form not found');
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                className: 'webform-button-link-cancel',
                events: {
                    click: function () {
                        this.popupWindow.close();
                        this.popupWindow.destroy();
                    }
                }
            })
        ]
    });
    command(
        ajaxPath,
        {
            action: 'get-form',
            formId: formId,
            requestId: requestId
        },
        function(content) {
            popup.setContent(content);
            popup.show();
        }
    );
}

/**
 * @param ajaxPath
 * @param gridId
 * @param requestId
 */
function showDeclinePopup(ajaxPath, gridId, requestId) {
    let popupId = 'decline-popup';
    let formId  = 'decline-form';
    let popup = BX.PopupWindowManager.create(popupId, null, {
        width: 400,
        titleBar: 'Отклонить заявку',
        draggable: true,
        darkMode: false,
        autoHide: false,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: true,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Сохранить',
                className: 'webform-button-link-create',
                events: {
                    click: function () {
                        let thisPopup = this;
                        let form = $('.'+formId);
                        if(form !== undefined) {
                            let requestId = form.find('[name=requestId]').val();
                            let comment   = form.find('[name=comment]').val();
                            command(
                                ajaxPath,
                                {
                                    action: 'decline',
                                    requestId: requestId,
                                    comment: comment
                                },
                                function(data) {
                                    thisPopup.popupWindow.close();
                                    thisPopup.popupWindow.destroy();
                                    reloadGrid(gridId);
                                }
                            );
                        } else {
                            customNotify('Form not found');
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                className: 'webform-button-link-cancel',
                events: {
                    click: function () {
                        this.popupWindow.close();
                        this.popupWindow.destroy();
                    }
                }
            })
        ]
    });
    command(
        ajaxPath,
        {
            action: 'get-form',
            formId: formId,
            requestId: requestId
        },
        function(content) {
            popup.setContent(content);
            popup.show();
        }
    );
}

function closePeriod(ajaxPath, gridId, periodId) {
    command(
        ajaxPath,
        {
            action: 'close-period',
            periodId: periodId,
        },
        function() {
            reloadGrid(gridId);
        }
    );
}

/**
 * @param ajaxPath
 * @param gridId
 * @param requestId
 */
function showChangePopup(ajaxPath, gridId, requestId) {
    let popupId = 'change-popup';
    let formId  = 'change-form';
    let popup = BX.PopupWindowManager.create(popupId, null, {
        width: 400,
        titleBar: 'Редактировать заявку',
        draggable: true,
        darkMode: false,
        autoHide: false,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: true,
        buttons: [
            new BX.PopupWindowButton({
                text: 'Сохранить',
                className: 'webform-button-link-create',
                events: {
                    click: function () {
                        let thisPopup = this;
                        let form = $('.'+formId);
                        if(form !== undefined) {
                            command(
                                ajaxPath,
                                {
                                    action: 'change',
                                    requestId: form.find('[name=requestId]').val(),
                                    name: form.find('[name=name]').val(),
                                    amount: form.find('[name=amount]').val(),
                                    categoryId: form.find('[name=categoryId]').val(),
                                    vaultId: form.find('[name=vaultId]').val(),
                                    stockId: form.find('[name=stockId]').val(),
                                    autoApprove: (form.find('[name=autoApprove]').is(':checked')) ? 1 : 0,
                                    objectId: form.find('[name=object]').val(),
                                    comment: form.find('[name=comment]').val()
                                },
                                function(data) {
                                    thisPopup.popupWindow.close();
                                    thisPopup.popupWindow.destroy();
                                    reloadGrid(gridId);
                                }
                            );
                        } else {
                            customNotify('Form not found');
                        }
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: 'Закрыть',
                className: 'webform-button-link-cancel',
                events: {
                    click: function () {
                        this.popupWindow.close();
                        this.popupWindow.destroy();
                    }
                }
            })
        ]
    });
    command(
        ajaxPath,
        {
            action: 'get-form',
            formId: formId,
            requestId: requestId
        },
        function(content) {
            popup.setContent(content);
            popup.show();
            $('#object').select2({
                dropdownParent: $('#change-popup')
            });
        }
    );
}

/**
 * @param path
 * @param params
 * @param callback
 */
function command(path, params, callback) {
    $.ajax({
        type: "POST",
        url: path,
        data: params,
        success: function (answer) {
            if(answer.result !== undefined) {
                if(answer.result === true) {
                    callback(answer.data);
                } else {
                    customNotify(answer.message);
                }
            } else {
                customNotify('Error result');
            }
            //console.log(answer);
        },
        error: function (xhr) {
            if (xhr.responseJSON) {
                customNotify(xhr.responseJSON.message)
            } else {
                customNotify('Error processing');
            }
            //console.log(xhr);
        },
    });
}

/**
 * @param content
 */
function customNotify(content) {
    BX.UI.Notification.Center.notify({
        content: content
    });
    //alert(content);
}

/**
 * Reload grid by ID
 * @param gridId
 */
function reloadGrid(gridId) {
    let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
    let gridObject = BX.Main.gridManager.getById(gridId);

    if (gridObject.hasOwnProperty('instance')) {
        gridObject.instance.reloadTable('POST', reloadParams);
    }
}