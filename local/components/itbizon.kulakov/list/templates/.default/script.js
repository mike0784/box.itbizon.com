$(document).ready(function () {

    $("#selectAll").on("change", function() {
        let button = $("#remove");
        let c = this.checked;

        button[0].disabled = !c;
        $(".select-remove").each(function(i, e) {
            e.checked = c;
        });
    });

    $(".select-remove").on("change", function() {
        let button = $("#remove");
        button[0].disabled = true;

        $(".select-remove").each(function(i, e) {
            if(e.checked) {
                button[0].disabled = false;
                return;
            }
        });
    });

    $("#remove").on("click", function() {
        const path = $(this).data('path');

        $(".select-remove").each(function(i, e) {
            if(e.checked) {
                $.ajax({
                    type: "GET",
                    url: path + "=" + e.id,
                    success: function () {
                        $(e).parents("tr")[0].remove();
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    },
                });
            }
        });

    });

});