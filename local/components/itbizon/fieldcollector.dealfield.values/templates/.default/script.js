function deleteDealfieldItem(id, gridId) {
    // Используем стандартные диалоги https://dev.1c-bitrix.ru/api_d7/bitrix/ui/dialogs/dialogs.php
    messageBox = BX.UI.Dialogs.MessageBox.confirm(
        'Вы уверены что хотите удалить элемент?', // Текст сообщения, можно html
        'Удалить', // Заголовок сообщения
        function (messageBox) {
            // Нажали "ОК"
            // Вызываем действие компонента
            BX.ajax.runComponentAction('itbizon:fieldcollector.dealfield.list', 'deleteItem', {
                mode: 'ajax',
                data: {
                    id: id,
                },
            }).then(function (response) { // Успех
                // Перезагружаем грид (чтобы увидеть изменения)
                let gridObject = BX.Main.gridManager.getById(gridId);
                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', {apply_filter: 'Y', clear_nav: 'N'});
                }
                messageBox.close();
            }, function (response) { // Ошибка
                messageBox.close();
                BX.UI.Dialogs.MessageBox.alert(
                    'Ошибка: ' + response.errors[0].message, // Сообщение
                );
            });
        },
        function (messageBox) {
            //Нажали "Отмена" - ничего не далаем
        }
    );
}