function deleteStock(id) {
    if(confirm('Вы уверены?')) {
        $.ajax({
            type: "POST",
            url: '/local/modules/itbizon.service/tools/ajax.php',
            data: {
                modules: ['itbizon.finance'],
                process: '\\Itbizon\\Finance\\Utils\\AjaxHandler::deleteStock',
                DATA: {
                    ID: id
                }
            },
            cache: false,
            //processData: false,
            //contentType: false,
            success: function (response) {
                console.log(response);
                if (response.state === true) {
                    reloadGrid('itb_finance_stock_list');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr) {
                try {
                    const data = jQuery.parseJSON(xhr.responseText);
                    if (data.message) {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    alert('System error '+e.toString());
                }
            },
        });
    }
}
function reloadGrid(gridId) {
    let gridObject = BX.Main.gridManager.getById(gridId);
    if (gridObject.hasOwnProperty('instance')) {
        gridObject.instance.reloadTable('POST', {apply_filter: 'Y', clear_nav: 'Y'});
    }
}
$(document).ready(function () {
    if (!BX.SidePanel.Instance.isOpen()) {
        BX.addCustomEvent("SidePanel.Slider:onCloseComplete", function (event) {
            if (event.slider.data.get("reloadGrid") === true) {
                reloadGrid('itb_finance_stock_list');
            }
        });
    }
});