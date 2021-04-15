$(document).ready(function() {

    function calcOperationAmount() {
        let total = 0;
        $('.itb-operation-selector').each(function(i, e) {
            if($(e).prop('checked')) {
                let amount = Number($(e).data('value'));
                total += amount;
            }
        });
        $('#field-value').val(total/100);
    }

    $(document).on('change', '.itb-type-selector', function(evt) {
        let auto = ($(this).val() === 'AUTO');
        $('#field-value').prop('readonly', auto);
        $('.itb-operation-selector').prop('readonly', !auto);
        if(auto) {
            calcOperationAmount();
        }
    });

    $(document).on('change', '.itb-operation-selector', function(evt) {
        calcOperationAmount();
    });

    calcOperationAmount();
});