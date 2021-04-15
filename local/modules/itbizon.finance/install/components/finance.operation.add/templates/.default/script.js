$(document).ready(function () {
    $('#operation-type').on("change", function () {
        $('.form-group.d-hidden').addClass('d-none');

        switch ($(this).val()) {
            // 'Расход'
            case '1':
                showDstVault();
                showCategory("ALLOW_INCOME");
                break;
            // 'Приход'
            case '2':
                showSrcVault();
                showCategory("ALLOW_OUTGO");
                break;
            // 'Перевод'
            case '3':
                showDstVault();
                showSrcVault();
                showCategory("ALLOW_TRANSFER");
                break;
        }
    });

    $('#operation-entity-type select').on("change", function () {
        $('#operation-entity').removeClass('d-none');
    });
});

function showSrcVault() {
    loadVault();
    $('#operation-src-vault').removeClass('d-none');
}

function showDstVault() {
    loadVault();
    $('#operation-dst-vault').removeClass('d-none');
}

function showCategory(type) {
    loadCategory(type);
    $('#operation-category').removeClass('d-none');
}

function loadCategory(type) {
    const path = $('[data-ajax]').data("ajax");
    $.ajax({
        type: "GET",
        url: path + "?categoryFilter=" + type,
        success: function (result) {
            let options = "<option disabled selected>" + BX.message('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.DEFAULT_CATEGORY') + "</option>";
            result.data.forEach(function (item) {
                options += "<option value=" + item.ID + ">" + item.NAME + "</option>"
            });

            $('#operation-category select').html(options);
        },
        error: function (xhr) {
            console.log(xhr);
        },
    });

}

function loadVault() {
    const path = $('[data-ajax]').data("ajax");

    if (!$('select.vault').hasClass('loaded')) {
        $.ajax({
            type: "GET",
            url: path + "?vault",
            success: function (result) {
                let options = "<option disabled selected>" + BX.message('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.DEFAULT_VAULT') + "</option>";
                result.data.forEach(function (item) {
                    options += "<option value=" + item.ID + ">" + item.NAME + "</option>"
                });

                $('select.vault').each(function (i, vault) {
                    $(vault).html(options);
                    $(vault).addClass('loaded');
                });
            },
            error: function (xhr) {
                console.log(xhr);
            },
        });
    }
}