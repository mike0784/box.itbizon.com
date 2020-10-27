<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="/bitrix/css/main/bootstrap_v4/bootstrap.min.css">
    <script src="//api.bitrix24.com/api/v1/"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        $(document).on('click', '#btn-install', function() {
            BX24.init(function(){
                BX24.installFinish();
                console.log('Инициализация завершена!', BX24.isAdmin());
            });
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="container-fluid p-1">
        <button id="btn-install" class="btn btn-primary">Установить</button>
    </div>
</div>
</body>
</html>