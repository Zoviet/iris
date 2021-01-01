'use strict';

var $start = document.getElementById('start');
var $stop = document.getElementById('stop');
var $temp = document.getElementById('temp');
var $time = document.getElementById('time');
var $number = document.getElementById('number');
var $next = document.getElementById('next');
var $reset = document.getElementById('reset');
var $fail = document.getElementById('fail');
var $chartBar = document.getElementById('chartBar');
var $id = $start.dataset.target;
var $type = $start.dataset.type;
var $bardata = [];

function show_errors($errors) {	
	var $error_block = document.getElementById('error_modal');
	var $warning_block = document.getElementById('error');
	var $warblock = $warning_block.innerHTML;				
	$error_block.classList.remove('is-hidden');
	$error_block.classList.add('is-active');				
	$errors.forEach(function ($value) {					
		$warblock = $warblock + $value + '<br/>';
		$warning_block.innerHTML = $warblock;					
	});		
}

function show_log($messages)  {	
	var $log_block = document.getElementById('log');	
	var $messageblock = $log_block.innerHTML;				
	$messages.forEach(function ($value) {					
		$messageblock = $messageblock + $value + '<br/>';
		$log_block.innerHTML = $messageblock;					
	});		
}	


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
    data: $bardata
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

var $timerId;
var $status = new Map([

  ['stop', false]
]);  

$reset.addEventListener('click', function(){
	clearInterval($timerId);
	location.reload();
});

$start.addEventListener('click', function(){	
	$timerId = setInterval(timer, 100);
	 $.post(
        '/test/type'+$type+'/'+$id, // адрес обработчика
    {
		stop:$status.get('stop'),
	}, 
	function(msg) {
		var $msg = JSON.parse(msg);
		console.log($msg);	
		if ($msg.stop) {
				$status.set('stop',true);
			}
		if ($msg.errors) {	
		if ($msg.errors.length != 0) {
			clearInterval($timerId);
			show_errors($msg.errors);		
			$status.set('stop',true);			
		}}
		if ($msg.messages){
		if ($msg.messages.length != 0) {
			show_log($msg.messages);
		}					
		}		
	});		

	return false;
});

$stop.addEventListener('click', function(){
	clearInterval($timerId);
	$status.set('stop',true);
	$.post(
        '/stop', // адрес обработчика
    {
		
	}, 
	function(msg) {
		var $msg = JSON.parse(msg);
		console.log($msg);
		show_log($msg.messages);	
		show_errors($msg.messages);	
	});		
	return false;	
});

$fail.addEventListener('click', function(){
	location.reload();
});

function timer(){
	 $.post(
        '/diagramm', // адрес обработчика
    {		
		stop:$status.get('stop'),		
	},    
        function(msg) { // получен ответ сервера 
			//alert(msg);						
			var $msg = JSON.parse(msg);	
			console.log('Положение: '+$msg.temp+' | Давление: '+$msg.press+' | Температура: '+$msg.temp);
			if ($msg.stop != false) { 	
				clearInterval($timerId);
				$start.classList.remove('is-active');
				$start.classList.add('is-hidden');
				$next.classList.remove('is-hidden');
				$next.classList.add('is-active');
				$reset.classList.remove('is-hidden');
				$reset.classList.add('is-active');
				$stop.classList.remove('is-active');
				$stop.classList.add('is-hidden');
			}			
			if ($msg.temp) {						
				$bardata.push([$msg.way,$msg.press]),
				$chartBar.innerHTML ='';
				$chart.render();									
				$temp.innerHTML ='';
				$temp.innerHTML = $msg.temp;					
				} 
			if ($msg.messages){									
				var $log_block = document.getElementById('log');	
				var $messageblock = '';				
				$msg.messages.forEach(function ($value) {					
					$messageblock =  $messageblock + $value + '<br/>';									
				});									
				$log_block.innerHTML = $messageblock;	
			}	
			if ($msg.errors) {	
				if ($msg.errors.length != 0) {
					clearInterval($timerId);
					show_errors($msg.errors);
					$status.set('stop',true);			
				} }					
        }
    );
    return false;
}










