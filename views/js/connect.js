'use strict';
function getAll(selector) {
  return Array.prototype.slice.call(document.querySelectorAll(selector), 0);
}

var $removes = getAll('.remove');

if ($removes.length > 0) {
  $removes.forEach(function ($el) {
    $el.addEventListener('click', function () {
		var $type = $el.dataset.type;
		$.post(
        '/removeammo', // адрес обработчика
		{
			type:$type
		}, 
		function(msg) {			
			location.reload();	
		});      
		return false;     
    });
  });
}


