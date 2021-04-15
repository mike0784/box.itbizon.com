const DIR = '/local/components/itbizon/finance.requesttemplate.list/templates/.default';

$(document).ready(function () {
    if(!BX.SidePanel.Instance.isOpen()) {
        BX.addCustomEvent("SidePanel.Slider:onCloseComplete", function (event) {
            console.log(event);
            // if(event.slider.iframeSrc.indexOf('add') > 0)
            //     window.resetGrid();
        });
    }
});

function decline(userId, requestId)
{
    let alertWrapper = $('#alert-wrapper');
    $.post(
        DIR+'/ajax.php',
        {
            cmd: 'decline',
            userId : userId,
            requestId: requestId,
        }
    ).done(function(answer) {
        if(answer.status === true)
        {
            console.log(answer);
            window.resetGrid();
        } else {
            console.log(answer);
            alertWrapper.html("<div class='alert alert-danger'>"+answer.message+"</div>");
        }
    }).fail(function(error){
        console.log(error);
        alertWrapper.html("<div class='alert alert-danger'>"+error.message+"</div>");
    });
}

function remove(path) {
    // noinspection JSAnnotator
    if(confirm('Удалить шаблон ?'))
        $.ajax({
            type: "GET",
            url: path,
            success: function (r) {
                resetGrid();
            },
            error: function (xhr) {
                console.log(xhr);
                if (xhr.responseJSON) {
                    alert(xhr.responseJSON.message)
                } else {
                    // noinspection JSAnnotator
                    alert("Неизвестная ошибка");
                }
            },
        });
    return 0;
}

window.resetGrid = function() {
    let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
    let gridObject = BX.Main.gridManager.getById('req_template_list'); // Идентификатор грида

    if (gridObject.hasOwnProperty('instance')) {
        gridObject.instance.reloadTable('POST', reloadParams);
    }
}

window.addEventListener('message', function(event) {
    var message = event.data;
    if(message === 'resetGrid')
        window.resetGrid();
});