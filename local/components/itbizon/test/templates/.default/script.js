$(function () {
    BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params, filterObj) {
        console.log(filterObj.getFilterFieldsValues()); // данные фильтра
        location.reload();
    }));
});