$(document).ready(function() {

    $("input").on("input", function() {
        BX.setCookie("REPORT_" + this.id.toUpperCase(), this.value);
        reload();
    });

    $("select").on("change", function() {
        BX.setCookie("REPORT_" + this.id.toUpperCase(), this.value);
        reload();
    });

    $(".change-date").on("click", function() {
        let from = new Date($("#from").val());
        let to = new Date($("#to").val());

        const interval = to.getDate() - from.getDate() + 1;

        if(this.id == 'prev') {
            from.setDate(from.getDate() - interval);
            to.setDate(to.getDate() - interval);
        } else if(this.id == 'next') {
            from.setDate(from.getDate() + interval);
            to.setDate(to.getDate() + interval);
        }

        BX.setCookie("REPORT_FROM", from.toISOString().substr(0, 10));
        BX.setCookie("REPORT_TO", to.toISOString().substr(0, 10));

        reload();
        return false;
    });

});

function reload() {
    location.reload();
}