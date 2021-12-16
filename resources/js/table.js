'use strict';

function getAll(selector) {
  return Array.prototype.slice.call(document.querySelectorAll(selector), 0);
}

var rootEl = document.documentElement;
var $cells = getAll('.method, .cell, .object');
var $modals = getAll('.modal');
var $modalCloses = getAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button');
var $upload = document.getElementById("upload");
var $uploadlabel = document.getElementById("uploadlabel");

$( document ).ready(function() 	{
	$('#submittext').click(function() {	
		var $text = $('#textset').val();
		if ($('#status').attr('data-status')=='stat') {
			$.post( '/iris/textstat', { text: $text }, function(data) {
				$('#result').html(data);
			});
		} else {
			console.log($text);
		}
	});
});

$upload.addEventListener('focus', function () {
	var entry = $upload.files[0];
	$uploadlabel.innerHTML = entry.name;
},{
	capture: false,
});
    

if ($modalCloses.length > 0) {
  $modalCloses.forEach(function ($el) {
    $el.addEventListener('click', function () {
      closeModals();
    });
  });
}

document.addEventListener('keydown', function (event) {
  var e = event || window.event;
  if (e.keyCode === 27) {
    closeModals();
  }
});

function openModal(target) {
  var $target = document.getElementById(target);
  $target.classList.remove('is-hidden');
  $target.classList.add('is-active');
  return $target;
}

function closeModals() {
  rootEl.classList.remove('is-clipped');
  $modals.forEach(function ($el) {
    $el.classList.remove('is-active');
  });
}


if ($cells.length > 0) {	
	var $status = document.getElementById('status');
	var $type = $status.dataset.status;
	$cells.forEach(function ($el) {
		$el.style.cursor = "pointer"; 
		$el.addEventListener('click', function () {
		var $target = openModal($type);
		
		var $addon_id = $el.dataset.addons_id;
		var $addon_data = null;
		var $addon_type = null;
				
		$.get('/iris/addon/'+$addon_id,function(result){	
			$addon_type = result.type;
			$addon_data = result.data;
		})
		.done(function() {		
		
			if ($type=='show') {				
				$target.querySelectorAll('.type')[0].innerHTML=$addon_type;		
				$target.querySelectorAll('.title')[0].innerHTML=$el.innerHTML;
				$target.querySelectorAll('.data')[0].innerHTML=$addon_data;	
				$target.querySelectorAll('.id')[0].innerHTML=$el.dataset.id;	
			} else {
				var $selectors = $target.querySelectorAll('.selector');	
				$selectors.forEach(function ($selector) {
					if ($selector.getAttribute('value') == $addon_type) {
						$selector.setAttribute('selected','selected');
					}
				});						
				$target.querySelectorAll('.title')[0].innerHTML = $el.innerHTML;
				$target.querySelectorAll('.data')[0].innerHTML = $addon_data;
				$target.querySelectorAll('.id')[0].setAttribute('value', $el.dataset.id);
				$target.querySelectorAll('.addons_id')[0].setAttribute('value', $addon_id);
				$target.querySelectorAll('.controller')[0].setAttribute('value',$el.dataset.controller);	
			}
		});
	});
  });
}


function show_errors($errors) {	
	var $error_block = document.getElementById('errors');
	var $warning_block = document.getElementById('errorlist');
	var $warblock = $warning_block.innerHTML;				
	$error_block.classList.remove('is-hidden');
	$error_block.classList.add('is-active');				
	$errors.forEach(function ($value) {					
		$warblock = $warblock + $value + '<br/>';
		$warning_block.innerHTML = $warblock;					
	});		
}

