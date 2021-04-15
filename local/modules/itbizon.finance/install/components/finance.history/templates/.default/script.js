// BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params) {
//     var workarea = $('#' + command); // в command будет храниться GRID_ID из фильтра
//     window.gridObject = BX.Main.gridManager.getById(command);
//
//     console.log(window.gridObject);
//     // $.ajax({
//     //     type: "POST",
//     //     data: {chart_update: 'Y'},
//     //     success: function (result) {
//     //         console.log(result);
//     //     },
//     //     error: function (xhr) {
//     //         // console.log(xhr);
//     //     },
//     // });
//
//     drawChart();
// }));
//
// google.charts.load('current', {'packages':['corechart']});
// google.charts.setOnLoadCallback(drawChart);
//
// function drawChart() {
//     var data = google.visualization.arrayToDataTable(window.chartData);
//
//
//     var options = {
//         title: 'График изменения баланса кошельков',
//         // curveType: 'function',
//         legend: { position: 'bottom' }
//     };
//
//     var chart = new google.visualization.LineChart(document.getElementById('history-chart'));
//
//     chart.draw(data, options);
// }