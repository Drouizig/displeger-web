
var highchart = require('highcharts');

$(document).ready(function() {
  var chart = $('#js-category-chart');
  var title = chart.data('title');
  var data = chart.data('categories');
  highchart.chart('js-category-chart', {
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
      text: title
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
          style: {
            color: (highchart.theme && highchart.theme.contrastTextColor) || 'black'
          }
        }
      }
    },
    series: [{
      name: 'Rummad',
      colorByPoint: true,
      data: data
    }]
  });

});