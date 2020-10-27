BX24.init(function () {
    BX24.ready(function () {
        let $infoContainer = $(document).find('.info__container');
        $(document).on('submit', '#form-document', function (e) {
            e.preventDefault();

            let data = new FormData(this);
            data.append('cmd', 'generate-table');
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $infoContainer.empty();
                    $infoContainer.removeClass('alert-danger');
                    $infoContainer.removeClass('alert-success');
                    $infoContainer.css('display', 'none');
                },
                success: function (response) {
                    if (response.data.html) {
                        let tableContent = $('.table-content');
                        tableContent.empty();
                        tableContent.append(response.data.html);
                    }
                },
                error: function (jqXHR) {
                    $infoContainer.addClass('alert-danger');
                    try {
                        let data = jQuery.parseJSON(jqXHR.responseText);
                        $infoContainer.append(data.message)
                    } catch (e) {
                        $infoContainer.append(jqXHR.responseText)
                    }
                    $infoContainer.css('display', 'block');
                },
                complete: function () {
                    $('#document').val('')
                }
            })
        });

        $(document).on('click', '#create__archive', function (e) {
            e.preventDefault();
            let archiveName = $('.archiveName').val();

            let items = [];
            $('#tbody__data  > tr').each(function (key, value) {
                if (key !== 0) {
                    let number = $(this).find("input[name='number']").val();
                    let accountNumber = $(this).find("input[name='accountNumber']").val();
                    let debtorFIO = $(this).find("input[name='debtorFIO']").val();
                    let court = $(this).find("input[name='court']").val();
                    let debtorAddress = $(this).find("input[name='debtorAddress']").val();
                    let debtSum = $(this).find("input[name='debtSum']").val();
                    let countMonth = $(this).find("input[name='countMonth']").val();

                    // if (typeof name !== 'undefined' || typeof link !== 'undefined') {
                    let formData = new FormData();
                    formData.append('cmd', 'add-file');
                    formData.append('Number', number);
                    formData.append('AccountNumber', accountNumber);
                    formData.append('DebtorFIO', debtorFIO);
                    formData.append('Court', court);
                    formData.append('DebtorAddress', debtorAddress);
                    formData.append('DebtSum', debtSum);
                    formData.append('CountMonth', countMonth);

                    formData.append('archiveName', archiveName);
                    items.push(formData);
                    // }
                }
            });

            let countFiles = items.length;
            if (countFiles > 0) {
                $(this).remove();
                $('#form-document').remove();
                // $('#clear-all').remove();
                // $('.remove-archive').remove();

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
                // let data = new FormData();
                // data.append('cmd', 'archive-folder');
                // data.append('folderName', archiveName);

                // $.ajax({
                //     type: "POST",
                //     url: path,
                //     data: data,
                //     processData: false,
                //     contentType: false,
                //     beforeSend: function () {
                //         $('.error__message').remove();
                //     },
                //     success: function (response) {
                //         $('.progress-bar').prepend('<p>Данные усмешно заархивированы</p>');
                //         location.reload();
                //     },
                //     error: function (xhr) {
                //         const response = JSON.parse(xhr.responseText);
                //         $('.container-fluid').prepend(' <div class="alert alert-danger error__message" role="alert">' + response.message + '</div>');
                //         console.log(response);
                //     },
                // });
            }, function () {
                console.log('some errors!');
            });
        });

        // function to trigger the ajax call
        let ajaxRequest = function (formData) {
            let deferred = $.Deferred();
            let path = $('#path-to-ajax').val();
            // console.log(path)
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


        BX24.fitWindow();
    });
});