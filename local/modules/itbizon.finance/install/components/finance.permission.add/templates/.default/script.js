$(document).ready(function () {
    $("#field-entity-type-id").on("change", function () {
        let actionField = $("#field-action");
        const value = $(this).val();

        $("#field-entity-id option").prop("disabled", true);
        $("#field-entity-id").val("");

        $("#field-entity-id option[data-toggle="+value+"]").prop("disabled", false);

        resetSelect(actionField);
        switch (value) {
            case getEntityTypeId("VAULT"):
                selectEnabledOption(actionField, "REQUEST_INCOME");
            // no break
            case getEntityTypeId("VAULT_GROUP"):
            case getEntityTypeId("OPERATION"):
            case getEntityTypeId("CATEGORY"):
            case getEntityTypeId("STOCK"):
                selectEnabledOption(actionField, "ADD");
                selectEnabledOption(actionField, "VIEW");
                selectEnabledOption(actionField, "EDIT");
                selectEnabledOption(actionField, "DELETE");
                setEntityId("ALL");
                break;
            case getEntityTypeId("PERIOD"):
            case getEntityTypeId("REQUEST_TEMPLATE"):
                selectEnabledOption(actionField, "ADD");
                selectEnabledOption(actionField, "VIEW");
                selectEnabledOption(actionField, "EDIT");
                selectEnabledOption(actionField, "DELETE");
                setEntityId("ALL");
                break;
            case getEntityTypeId("CATEGORY_REPORT"):
                selectEnabledOption(actionField, "VIEW");
                break;
            case getEntityTypeId("CONFIG"):
                selectEnabledOption(actionField, "EDIT");
                setEntityId("ALL");
                break;
            default:
                selectEnabledOption(actionField, "ADD");
                selectEnabledOption(actionField, "VIEW");
                selectEnabledOption(actionField, "EDIT");
                selectEnabledOption(actionField, "DELETE");
                break;
        }

        $("#field-action").trigger("change");
    });

    $("#field-action").on("change", function () {
        const value = $(this).val();
        const entity = $("#field-entity-type-id").val();
        let formGroupEntityId = $("#field-entity-id").parents(".form-group");

        switch (value) {
            case getActionId("VIEW"):
            case getActionId("EDIT"):
                if(
                    entity === getEntityTypeId("VAULT") ||
                    entity === getEntityTypeId("VAULT_GROUP") ||
                    entity === getEntityTypeId("CATEGORY")
                ) {
                    formGroupEntityId.show();
                    $("#field-entity-id").prop("disabled", false);
                }
                break;
            case getActionId("REQUEST_INCOME"):
                if(entity === getEntityTypeId("VAULT")) {
                    formGroupEntityId.show();
                    $("#field-entity-id").prop("disabled", false);
                }
                break;
            default:
                formGroupEntityId.hide();
                $("#field-entity-id").prop("disabled", true);
                break;
        }
    });

    $("#field-action").trigger("change");
    $("#field-entity-type-id").trigger("change");
});

function setEntityId(name) {
    $("#field-entity-id").val(getEntityId(name));
}

function selectEnabledOption(el, option) {
    el.find("option[value=" + getActionId(option) + "]").prop("disabled", false);
}

function resetSelect(el) {
    el.val('');
    el.find("option").prop("disabled", true);
}

function getActionId(name) {
    return BX.message("ITB_FINANCE.PERMISSION.ADD.ACTIONS." + name).toString();
}

function getEntityTypeId(name) {
    return BX.message("ITB_FINANCE.PERMISSION.ADD.ENTITY." + name).toString();
}

function getEntityId(name) {
    return BX.message("ITB_FINANCE.PERMISSION.ADD.ENTITY_ID." + name).toString();
}