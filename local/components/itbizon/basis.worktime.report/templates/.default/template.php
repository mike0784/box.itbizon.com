<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load('ext_bootstrap4');

$message = $arResult['MESSAGE'];
$messageType = $arResult['MESSAGE_TYPE'];
$component = $this->getComponent();
$totalWorkTime = $component->getTotalWorkTime();

CUtil::InitJSCore(['xlsx']);

if (!empty($message))
{
    echo '<div class="alert ' . $messageType . '" role="alert">' . $arResult['MESSAGE'] . '</div>';
}

?>
<div class="container-fluid">
    <div class="row p-1">
        <div class="col">
            <?php
            Toolbar::addFilter([
                'GRID_ID' => $arResult['GRID_ID'],
                'FILTER_ID' => $arResult['GRID_ID'],
                'FILTER' => $arResult['FILTER'],
                'ENABLE_LIVE_SEARCH' => true,
                'ENABLE_LABEL' => true,
                'RESET_TO_DEFAULT_MODE' => true,
            ]);
            Toolbar::addButton([
                'text'=>'Выгрузить в Excel',
                'classList'=>['ui-btn-success','ui-btn-main'],
                'onclick'=>"downloadExcel",
            ]);
            ?>
        </div>
    </div>
    <div class="row p-2">
        <div class="col">
            <?php
            $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
                'GRID_ID' => $arResult['GRID_ID'],
                'COLUMNS' => $arResult['COLUMNS'],
                'ROWS' => $arResult['ROWS'],
                'SHOW_ROW_CHECKBOXES' => false,
                'AJAX_MODE' => 'Y',
                'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
                'ENABLE_COLLAPSIBLE_ROWS' => true,
                'AJAX_OPTION_JUMP' => 'N',
                'SHOW_CHECK_ALL_CHECKBOXES' => false,
                'SHOW_ROW_ACTIONS_MENU' => false,
                'SHOW_GRID_SETTINGS_MENU' => false,
                'SHOW_SELECTED_COUNTER' => true,
                'SHOW_TOTAL_COUNTER' => true,
                'SHOW_PAGESIZE' => true,
                'SHOW_ACTION_PANEL' => true,
                'ALLOW_COLUMNS_SORT' => true,
                'ALLOW_COLUMNS_RESIZE' => true,
                'ALLOW_HORIZONTAL_SCROLL' => true,
                'ALLOW_SORT' => true,
                'ALLOW_PIN_HEADER' => true,
                'AJAX_OPTION_HISTORY' => 'N',
            ]);
            ?>
        </div>
    </div>
</div>
<table id="excel-table" class="table table-sm table-striped table-hover text-center align-middle table-bordered">
    <thead class="thead-dark">
    <tr class="excel-table-head">
    </tr>
    </thead>
    <tbody class="excel-table-body">
    </tbody>
</table>

<script>
    var rows = [];
    $(function(){
        rows = $('.main-grid-row-body');
        $.each(rows, function(k, v){
            $(v).addClass('main-grid-row-expand');
        });

        /**
         * Событие обновление грида
         */
        BX.addCustomEvent(window, 'BX.Main.grid:paramsUpdated', function(){
            rows = $('.main-grid-row-body');
            $.each(rows, function(k, v){
                $(v).removeClass('main-grid-row-expand');
                $(v).addClass('main-grid-row-expand');
            });
        });

        // строка Итого
        let grid = $('#work_time_report_table');
        let tbody = grid.find('tbody');
        let row = tbody.find('tr')[0];
        let tdCount = $(row).find('td').length - 2;

        tbody.append('<tr id="total-row" class="main-grid-row main-grid-row-body main-grid-row-expand">');
        $('#total-row').append('<td class="main-grid-cell main-grid-cell-left"><span class="main-grid-cell-content"><b>Итого:</b></span></td>');
        while(tdCount-- > 1)
        {
            $('#total-row').append('<td class="main-grid-cell main-grid-cell-left"><span class="main-grid-cell-content"></span></td>');
        }
        $('#total-row').append('<td class="main-grid-cell main-grid-cell-left"><span class="main-grid-cell-content"><b><?=sprintf('%02d:%02d', $totalWorkTime['HOUR'], $totalWorkTime['MINUTE'])?></b></span></td>');
        tbody.append('</tr>');
    });
    
    function downloadExcel()
    {
        let reportTable = $('#work_time_report_table');
        let rowHead = $(reportTable).find('.main-grid-head-title');
        let reportTableBody = $(reportTable).find('.main-grid-row.main-grid-row-body');
        let excelHead = $('.excel-table-head');
        let excelBody = $('.excel-table-body');
        excelHead.html('');
        excelBody.html('');
        
        $.each($(rowHead), function(k, v){
            excelHead.append("<th>"+$(v).html()+"</th>");
        });
        excelBody.append("<tr></tr>".repeat($(reportTableBody).length-1));
        let excelBodyRow = excelBody.find('tr');
        
        $.each($(reportTableBody), function(k1, v){
            let row = $(v).find('.main-grid-cell-content');
            let depth = $(v).data('depth');

            if((k1+1) === $(reportTableBody).length)
                return;
            
            $.each($(row), function(k2, v){
                let name = '';
                if(k2 === 0)
                {
                    let html = $(v).html();
                    let depthStr = "-";
                    if($(v).find('.main-grid-plus-button').length > 0)
                        html = html.split('</span>')[1];
                    name = html.split('<a')[0];
                    name = name + $(v).find('a').html();
                    name = depthStr.repeat(depth)+' '+name;
                }
                else
                    name = $(v).html();
                
                let html = $(excelBodyRow[k1]).html();
                $(excelBodyRow[k1]).html(html + "<td>"+name+"</td>");
            });
        });
        
        let type = 'xlsx';
        let dl = '';
        let fn = '';
        var elt = document.getElementById('excel-table');
        console.log(elt);
        var wb = XLSX.utils.table_to_book(elt, {sheet:"Sheet JS"});
        return dl ?
            XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
            XLSX.writeFile(wb, fn || ('Отчет по трудоемкости.' + (type || 'xlsx')));
    }
    
</script>