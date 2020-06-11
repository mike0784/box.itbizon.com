$(document).ready(function () {
    let path = $('#path-to-ajax').data('path');

    $(document).on('submit', 'form', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('cmd', 'get-data');
        formData.append('excelFile', $('#excelFile').prop('files')[0]);

        $.ajax({
            type: "POST",
            url: path,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('.error__message').remove();
            },
            success: function (response) {
                console.log(response)
                let tbody = $('#tbody__data');
                tbody.empty();
                tbody.append(response.data)
            },
            error: function (xhr) {
                const response = JSON.parse(xhr.responseText);
                $('.container-fluid').prepend(' <div class="alert alert-danger error__message" role="alert">' + response.message + '</div>');
                console.log(response)
            },
        });
    });

    $(document).on('click', '.create__archive', function (e) {
        let archiveName = $('.archiveName').val();

        let items = [];
        $('#tbody__data  > tr').each(function () {
            let name = $(this).find("input.name").val();
            let link = $(this).find("input.link").val();

            if (typeof name !== 'undefined' || typeof link !== 'undefined') {
                let formData = new FormData();
                formData.append('cmd', 'add-file');
                formData.append('name', name);
                formData.append('link', link);
                formData.append('archiveName', archiveName);
                items.push(formData);
            }
        });

        let countFiles = items.length;

        if (countFiles > 0) {
            $(this).remove();
            $('.get__data').remove();
            $('#clear-all').remove();
            $('.remove-archive').remove();

            $('.progress-bar').append('<div class="w3-container w3-blue w3-center progress__body" style="width:0%">загрузка 0%</div>');
        }
        let looper = $.Deferred().resolve();
        $.when.apply($, $.map(items, function (item, i) {

            looper = looper.then(function () {
                let fileUpload = i + 1;
                let percent = Math.round(100 * fileUpload / countFiles);
                let progressBar = $('.progress__body');
                progressBar.empty();
                progressBar.append(percent + '%');
                progressBar.css('width', percent + '%');

                return ajaxRequest(item);
            });
            return looper;
        })).then(function () {
            console.log('Done!');
            let data = new FormData();
            data.append('cmd', 'archive-folder');
            data.append('folderName', archiveName);

            $.ajax({
                type: "POST",
                url: path,
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('.error__message').remove();
                },
                success: function (response) {
                    $('.progress-bar').prepend('<p>Данные усмешно заархивированы</p>');
                    location.reload();
                },
                error: function (xhr) {
                    const response = JSON.parse(xhr.responseText);
                    $('.container-fluid').prepend(' <div class="alert alert-danger error__message" role="alert">' + response.message + '</div>');
                    console.log(response);
                },
            });
        }, function () {
            console.log('some errors!');
        });
    });

    $(document).on('click', '.remove-archive', function (e) {
        $.ajax({
            type: "GET",
            url: path,
            data: {
                name: $(this).data('value'),
                cmd: 'remove-archive'
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                const response = JSON.parse(xhr.responseText);
                $('.container-fluid').prepend(' <div class="alert alert-danger error__message" role="alert">' + response.message + '</div>');
                console.log(response);
            },
        });
    });

    $(document).on('click', '#clear-all', function (e) {
        $.ajax({
            type: "GET",
            url: path,
            data: {
                name: $(this).data('value'),
                cmd: 'clear-all'
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                const response = JSON.parse(xhr.responseText);
                $('.container-fluid').prepend(' <div class="alert alert-danger error__message" role="alert">' + response.message + '</div>');
                console.log(response);
            },
        });
    });
});

// function to trigger the ajax call
let ajaxRequest = function (formData) {
    let deferred = $.Deferred();
    let path = $('#path-to-ajax').data('path');

    $.ajax({
        type: "POST",
        url: path,
        data: formData,
        processData: false,
        contentType: false,
        // beforeSend: function () {
        //     $('.error__message').remove();
        // },
        success: function (response) {
            console.log(response)
            // $('.id' + id).prepend('<span style="color: red;font-size:20px;">&#x2705;</span>');
            deferred.resolve(response);
        },
        error: function (error) {
            // $('.id-' + id).prepend('<span style="color: red;font-size:20px;">&#10006;</span>');
            deferred.reject(error);
        },
    });

    return deferred.promise();
};

