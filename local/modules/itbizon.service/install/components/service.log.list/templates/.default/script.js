function download(path) {
    $.get(path).done(function (response) {
        let fileName = response.data.fileName;
        let fileBase64 = response.data.content;
        let link = document.createElement("a");
        link.setAttribute('download', fileName);
        link.href = "data:application/octet-stream;charset=utf-8;base64," + fileBase64;
        document.body.appendChild(link);
        link.click();
        link.remove();
    }).fail(function (jqXHR) {
        try {
            let data = jQuery.parseJSON(jqXHR.responseText);
            alert('System error: ' + data.message);
        } catch (e) {
            alert('Unknown error: ' + e);
        }
    });
}