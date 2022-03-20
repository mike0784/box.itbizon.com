function deleteBook(id, gridId) {
    var addAnswer = new BX.PopupWindow(
        "my_answer",
        null,
        {
            content: BX( 'ajax-add-answer'),
            closeIcon: {right: "20px", top: "10px" },
            titleBar: "Удаление издательства",
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            draggable: {restrict: false},
            buttons: [
                new BX.PopupWindowButton({
                    text: "Удалить" ,
                    className: "popup-window-button-accept" ,
                    events: {click: function(){
                            BX.ajax.runComponentAction('mike:mike.publisher.view', 'deletePublisher', {
                                    mode: 'ajax',
                                    data: {
                                        id: id,
                                    },
                                }).then(function (response) {
                                let gridObject = BX.Main.gridManager.getById(gridId);
                                if (gridObject.hasOwnProperty('instance')) {
                                    gridObject.instance.reloadTable('POST', {apply_filter: 'Y', clear_nav: 'N'});
                                }
                                    this.popupWindow.close()
                            }, function (response) {
                                this.popupWindow.close()
                                console.log('Этап 3');
                                BX.UI.Dialogs.MessageBox.alert(
                                    'Ошибка: ' + response.errors[0].message,
                                );
                            });
                            this.popupWindow.close();
                        }}
                }),
                new BX.PopupWindowButton({
                    text: "Отмена" ,
                    className: "webform-button-link-cancel" ,
                    events: {click: function(){
                            this.popupWindow.close();
                        }}
                })
            ]
        })
    addAnswer.show();
}