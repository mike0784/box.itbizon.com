const DIR = '/local/components/itbizon/finance.request.list/templates/.default';

$(document).ready(function () {
    if (!BX.SidePanel.Instance.isOpen()) {
        BX.addCustomEvent("SidePanel.Slider:onCloseComplete", function (event) {
            console.log(event);
        });
    }
});

function decline(userId, requestId) {
    let alertWrapper = $('#alert-wrapper');
    $.post(
        DIR + '/ajax.php',
        {
            cmd: 'decline',
            userId: userId,
            requestId: requestId,
        }
    ).done(function (answer) {
        if (answer.status === true) {
            console.log(answer);
            window.resetGrid();
        } else {
            console.log(answer);
            alertWrapper.html("<div class='alert alert-danger'>" + answer.message + "</div>");
        }
    }).fail(function (error) {
        console.log(error);
        alertWrapper.html("<div class='alert alert-danger'>" + error.message + "</div>");
    });
}

window.resetGrid = function () {
    let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
    let gridObject = BX.Main.gridManager.getById('request_template_list'); // Идентификатор грида

    if (gridObject.hasOwnProperty('instance')) {
        gridObject.instance.reloadTable('POST', reloadParams);
    }
};

window.addEventListener('message', function (event) {
    var message = event.data;
    if (message === 'resetGrid')
        window.resetGrid();
});

BX.addCustomEvent('BX.Main.Filter:apply', function (filterId, dumpObject, filterObj) {
    if (filterId === 'request_template_list') {
        let filterData = $('#filter__data');
        filterData.val(' ');
        filterData.val(JSON.stringify(filterObj.getFilterFieldsValues()));
        console.log(filterObj.getFilterFieldsValues())
    }
});

function downloadExcel() {
    let path = $(document).find('#ajax__path').val();
    let formData = new FormData();
    formData.append('cmd', 'import');
    formData.append('filter', $(document).find('#filter__data').val());
    console.log(path);
    console.log($(document).find('#filter__data').val());
    $.ajax({
        type: "POST",
        url: path,
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            if (response.data && response.data.pathToFile) {
                location.href = response.data.pathToFile;
            } else {
                alert('Системная ошибка');
            }
        },
        error: function (xhr) {
            try {
                const data = jQuery.parseJSON(jqXHR.responseText);
                if (data.message) {
                    alert('Ошибка: ' + data.message);
                } else {
                    alert('Системная ошибка');
                }
            } catch (e) {
                alert('Системная ошибка');
            }
        },
    });
}