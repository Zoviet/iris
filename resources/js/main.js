'use strict';

$.each($('#nav li'),function(){
	$(this).click(function(){
		$('.tabs li').removeClass('is-active');
		$('.tab-pane').css('display','none');
		$('#matrix').data('level',$(this).data('level'));
		$('#'+$(this).data('target')).css('display','block');
		$(this).addClass('is-active');
	});
});


function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";SameSite=None; Secure";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$('#upload').change(function(){
	var file = $("#upload").prop("files")[0];
	$('#uploadlabel').text(file.name);	
});


$('.has-icon').click(function(){
	setCookie('clicked',$(this).data('menu'),1);
});

var clicked = getCookie('clicked');

if (clicked.length!='') {
	$('.has-icon[data-menu='+clicked+']').addClass('is-active');
	$('.has-icon[data-menu='+clicked+']').children('.icon').addClass('has-update-mark');
}

$('.object,.method,.cell').hover(
	function(){$(this).css('cursor', 'pointer');},
	function(){$(this).css('cursor', 'default');}
);

$('#matrix-public').click(function(){
	if ($(this).val()==1) {$(this).val(0)}
	else {$(this).val(1)}
});

$('#matrix-save').click(function(){
	$.ajax({
		url: '/savematrix',
		method: 'post',
		dataType: 'json',			
		data: {
			id: $('#matrix').data('id'),			
			title: $('#matrix-title').val(),
			autor: $('#matrix-name').val(),		
			description: $('#matrix-description').val(),	
			public: $('#matrix-public').val(),
		},
		success: function(response){
			$('#desc').addClass('has-background-success');
			$('#desc').animate({opacity:0.2}, 1000, "swing",function() {
				$(this).css('opacity',1);
				$(this).removeClass('has-background-success');
				location.reload();
			});	
		}
	});	
});


$('.delete-sheet').click(function(){
	var level = $('#matrix').data('level');
	$('#delete-title').text('"'+$('#sheet-title'+level).val()+'"');
	$('#delete').click(function(){
		$.ajax({
		url: '/deletelevel',
		method: 'post',
		dataType: 'json',			
		data: {
			id: $('#matrix').data('id'),
			level: $('#matrix').data('level'),			
		},
		success: function(response){
			location.reload();
		}
	})		
	});
});

$('.save-sheet').click(function(){
	var level = $('#matrix').data('level');
	$.ajax({
		url: '/savelevel',
		method: 'post',
		dataType: 'json',			
		data: {
			id: $('#matrix').data('id'),
			level: level,
			title: $('#sheet-title'+level).val(),
			autor: $('#sheet-name'+level).val(),		
			description: $('#sheet-description'+level).val(),	
		},
		success: function(response){
			$('.sheet-edit').addClass('has-background-success');
			$('.sheet-edit').animate({opacity:0.2}, 1000, "swing",function() {
				$(this).css('opacity',1);
				$(this).removeClass('has-background-success');
				location.reload();
			});	
		}
	});	
});

$('.delete-matrix').click(function(){
	var id = $(this).data('id');
	$('#delete-title').text('"'+$('#matrix'+id).children('td[data-label="Name"]').text()+'"');	
	$('#delete').click(function(){
		$.ajax({
		url: '/deletematrix',
		method: 'post',
		dataType: 'json',			
		data: {
			id: id,			
		},
		success: function(response){
			$('#matrix'+id).addClass('has-background-danger');
			$('#matrix'+id).fadeOut( "slow", function() {													
			$('#matrix'+id).hide();
			});	
		}
	})		
	});
});

$('.object,.method,.cell').click(function(){	
	var cell = $(this);
	if ($(this).children('.card').hasClass('active')) {
		$(this).children('.card').hide();
		$(this).children('.card').removeClass('active');
	} else {	
		if ($('#matrix').data('status')=='edit') {
			$('#cell-title').data('method',cell.data('method'));	
			$('#cell-title').data('object',cell.data('object'));
			$('#cell-title').data('addons_id',cell.data('addons_id'));
			$('#cell-title').data('controller',cell.data('controller'));
			$('#cell-title').data('row',cell.data('row'));
			$('#cell-title').data('col',cell.data('col'));
			$('#cell-title').val(cell.text());		
			$('#cell-type').find('option[value="TEXT"]').attr("selected",true);		
			if (cell.data('addons_id')!=''){				
				$.get('/addon/'+$(this).data('addons_id'),function(result){			
					$('#cell-data').val(result.data);	
					$('#cell-type').find("option[value='"+result.type+"']").attr("selected",true);									
				})
			}
		} else {				
			$.get('/addon/'+$(this).data('addons_id'),function(result){	
			if (result.type!=null) { 				
				cell.append($('<div></div>').addClass('card').addClass('active')
				.html(' <div class="card-content"> <div class="media"><div class="media-content"><p class="title is-6">ТИП: '+result.type+'</p></div></div><div class="content">'+result.data+'</div></div>'));
			}			
			})
		}
	}
	return null;
});

$('#cell-save').click(function(){
	$(this).removeClass('is-hidden');
	$(this).removeClass('is-hidden');	
	if ($('#cell-title').data('addons_id')=='') {
		if ($('#cell-title').data('controller')=='Method') {$('#cell-title').data('row');}
		if ($('#cell-title').data('controller')=='Object') {$('#cell-title').data('col');}	
		if ($('#cell-title').data('controller') == 'Cell' && ($('#cell-title').data('method')==''||$('#cell-title').data('object')=='')) {
			$('#cell-save').addClass('is-hidden');
			$('#cell-title').val('Перед заданием ячейки необходимо задать метод и объект');
			$('#cell-type').addClass('is-hidden');
			$('#cell-data').val('Предмет - первая строка зеленая ячейка. Метод - первый столбец последняя зеленая ячейка.');
		} 
	}
	console.log('ajax');
	$.ajax({
			url: '/saveaddon',
			method: 'post',
			dataType: 'json',			
			data: {
				title: $('#cell-title').val(),
				type: $('#cell-type').val(),		
				data: $('#cell-data').val(),
				id: $('#cell-title').data('addons_id'),
				object_id: $('#cell-title').data('object'),
				method_id: $('#cell-title').data('method'),
				controller: $('#cell-title').data('controller'),
				matrix_id: $('#matrix').data('id'),
				level: $('#matrix').data('level'),
			},
			success: function(response){	
				console.log('responce');
				if (response.addon=='') {location.reload();} else {																
				$('#edit-modal').addClass('has-background-success');
				$('#edit-modal').animate({opacity:0.2}, 600, "swing",function() {
					$(this).css('opacity',1);
					$(this).removeClass('has-background-success');
					$(this).removeClass('is-active');
					console.log(response.addon+': '+$('#cell-title').val());
					$('.jb-modal[data-addons_id="'+response.addon+'"]').text($('#cell-title').val());	
				});				 
				}
			}
	})
});

