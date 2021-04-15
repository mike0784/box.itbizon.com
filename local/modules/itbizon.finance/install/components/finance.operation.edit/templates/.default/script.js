$(document).ready(function () {
    let history = $("#history button");
    history.on("click", function () {
        const form = $(this).parents("form");
        const action = form.attr("action");
        $.ajax({
            type: "POST",
            url: action,
            data: form.serialize(),
            success: function (response) {
                $("#vault-history").html(response.data);
            },
            error: function (xhr) {
                console.log(xhr);
            },
        });

        return 0;
    });

    history.click();
});