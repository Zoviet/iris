'use strict';

var $chartBar = document.getElementById('chartBar');
var $data = JSON.parse($chartBar.dataset.target);
console.log($data);

var optionsChartLine = {
  chart: {
    height: 550,
    type: 'area'
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth'
  },
  series: [{
    name: 'Сила',
    data: $data
  } 
  ],
  legend: {
    labels: {
      colors: "#f8f8f2"
    }
  },
  xaxis: {
    //type: 'datetime',
    //categories: $canon,
    labels: {
      style: {
        colors: "#f8f8f2"
      }
    }
  },
  yaxis: {
	type: 'numeric',
    show: true,
    labels: {
      show: true,
      style: {
        colors: "#ffffff"
      }
    },
    crosshairs: {
      show: true,
      position: 'back',
      stroke: {
        color: '#FFF',
        width: 1,
        dashArray: 0
      }
    }
  },

};

var $chart = new ApexCharts(document.querySelector("#chartBar"), optionsChartLine);

$chart.render();
