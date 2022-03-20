var popup = new BX.CDialog({
    'title': 'Заголовок модального окна',
    'content_url': '/content.php',
    'draggable': true,
    'resizable': true,
    'buttons': [BX.CDialog.btnClose]
});

// событие после открытия окна (но до его выравнивания)
BX.addCustomEvent(popup, 'onWindowRegister',function(){
    console.log(this); // объект окна
});

// показ окна
popup.Show();