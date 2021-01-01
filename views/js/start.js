'use strict';

var $base_mark = document.getElementById('base_mark').childNodes;
var $base_model = document.getElementById('base_model');
var $marks = Array.prototype.slice.call($base_mark);

$('#newammo').submit(function(){ //отправка формы
    $.post(
        'start', // адрес обработчика
    
		$("#newammo").serialize(), // отправляемые данные        
  
        function(msg) { // получен ответ сервера  
			//alert(msg);
			var $msg = JSON.parse(msg);
			console.log($msg);
			if ($msg[0] == true) {
				window.location.href = '/validate/' + $msg[1];
			} else if (Array.isArray($msg)) {
				var $warning_block = document.getElementById('error');
				$msg.forEach(function ($value) {
					var $warblock = $warning_block.innerHTML;
					$warblock = $warblock + $value + '<br/>';
					$warning_block.innerHTML = $warblock;					
				});				
				openModal('error_modal');
			} else {
				alert(msg);
			}
        }
    );
    return false;
});


$marks.forEach(function ($options) {
	$options.addEventListener('click', function(){
    $.post(
        'start', // адрес обработчика
        {
         mark_ajax: $options.value, // отправляемые данные          
        },
        function(msg) { // получен ответ сервера  
			var $msg = JSON.parse(msg);					
			$base_model.innerHTML = '';	
			$msg.forEach(function ($value) {
				var $newoption = document.createElement('option');
				$newoption.innerHTML = $value;
				$newoption.value = $value;
				$base_model.appendChild($newoption);
			});
        }
    );
    return false;
	});
});

