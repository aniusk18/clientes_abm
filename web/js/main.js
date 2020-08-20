function show_delete_cliente(id){
	$('#modal_alert').find('.modal-title').text('Eliminar Cliente');
	$('#modal_alert').find('.modal-body').text('¿Está seguro de querer Eliminar esta cliente?');
	$('#modal_alert').find('.modal-footer').html('<button type="button" class="btn btn-primary" onclick="delete_cliente('+id+')">Aceptar</button>');

	$('#modal_alert').modal('show');
}

function delete_cliente(id){
	$('#modal_alert').find('.modal-body').html('<div class="justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
	
	url_delete ='/cliente/'+id+'/delete';
	$.ajax({
            url: url_delete,
            dataType: "json",
            data: {
            },
            success: function(data) {
                //console.log(data);
                $('#modal_alert').find('.modal-body').html('<div class="alert alert-success" role="alert">Cliente eliminado exitosamente</div>');
                $('#modal_alert').find('.modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');

            }
        });
}
$('#search').on('keyup paste', function() {
	var text=$(this).val();
	if(text !== "" ){
		$('#dataTable tbody').html('<div class="justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
		
		$.ajax({
	            url: search_action,
	            dataType: "json",
	            data: {
	            	text:text
	            },
	            success: function(data) {
	                if (data != '404') {
	                	var path = "searchPag('"+text+"',__i)";
		                dataTable(JSON.parse(data.clientes));
		                pagerTable(data.nro_pag,path,data.lastpage);
	                }else{
	                	$('#dataTable tbody').html('<tr><td>NO SE ENCONTRARON COINCIDENCIAS!<button type="button" class="btn btn-primary" onclick="reload_table()">Reiniciar</button></td></tr>');
	                }

	            }
	        });
	}
});
function reload_table(){
	$.ajax({
		url: get_all_clientes,
		dataType: "json",
		data: {
		},
		success: function(data) {
			$('#search').val("");
			if (data !='404') {
				var path = "tablePag(__i)";
				dataTable(JSON.parse(data.clientes));
				pagerTable(data.nro_pag,path,data.lastpage);
	        }else{
	        	$('#dataTable tbody').html('<tr><td>NO existen datos guardados aún!</td></tr>');
	        }
	    }
	});
}
function dataTable(data){
	url_cliente_edit ='/cliente/_id/edit';
	url_cliente_show ='/cliente';
	html = "";
	$.each(data, function(i, item) {
		url_cliente_edit_this = url_cliente_edit.replace('_id',item.id);
		console.log(item.id);
		
		html += '<tr>';
		html += '<td>'+item.nombre+'</td>';
		html += '<td>'+item.apellido+'</td>';
		html += '<td>'+item.email+'</td>';
		html += '<td>';
		grupos =item.grupo_cliente;
		$.each(grupos, function(j, grupo) {
			html +=grupo+' |';
		});
		html += '</td>';
		html += '<td>';
		html += '<ul>';
		html += '<li>';
		html += '<a href="'+url_cliente_show+'/'+item.id+'">show</a>';
		html += '</li>';
		html += '<li>';
		html += '<a href="'+url_cliente_edit_this+'">edit</a>';
		html += '</li>';
		html += '<li>';
		html += '<button type="button" class="btn btn-danger del" onclick="show_delete_cliente('+item.id+')"><i class="fa fa-minus-square"></i> eliminar</button>';
		html += '</li>';
		html += '</ul>';
		html += '</td>';
		html += '</ul>';
		html += '</tr>';
	});
	$('#dataTable tbody').html(html);
}

function pagerTable(nro_pag,path,last_page){
	var html ="";
	var last_page = parseInt(last_page);
	var nro_pag = parseInt(nro_pag);
	if(nro_pag == null){
		nro_pag = 1;
	}
	var path =path;
	var onclick='';
	if (last_page > 1 ){
		html +='<nav aria-label="table Page navigation ">';
		html +='<ul class="pagination justify-content-end">';
		if(nro_pag > 1){
			page_number = (nro_pag - 1);
			onclick=path.replace('__i',page_number);

			html +='<li class="page-item">';
			html +='<button class="page-link" onclick="'+onclick+'" aria-label="Previous">';
			html +='<span aria-hidden="true">&laquo;</span>';
			html +='<span class="sr-only">Anterior</span>';
			html +='</button>';
			html +='</li>';
		}
		if(nro_pag > 5){
			page_number = (nro_pag - 5);
			onclick=path.replace('__i',page_number);
			html +='<li class="page-item"><a class="page-link" onclick="'+onclick+'">'+page_number+'</a></li>';
	    }
	    if(nro_pag > 4){
	    	page_number = (nro_pag - 4);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item"><a class="page-link" onclick="'+onclick+'">'+page_number+'</a></li>';
	    }
	    if(nro_pag > 3 ){
	    	page_number = (nro_pag - 3);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item"><a class="page-link" onclick="'+onclick+'">'+page_number+'</a></li>';

	    }
	    if(nro_pag > 2  ){
	    	page_number = (nro_pag - 2);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item"><a class="page-link" onclick="'+onclick+'">'+page_number+'</a></li>';
	    }
	    if(nro_pag != 1 && nro_pag != null){
	    	page_number = (nro_pag - 1);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><a class="page-link" onclick="'+onclick+'">'+page_number+'</a></li>';
	    }

	    onclick=path.replace('__i',nro_pag);
	    html +='<li class="page-item active"><button class="page-link" onclick="'+onclick+'">'+nro_pag+'</button></li>';
	    if(nro_pag != last_page){
	    	page_number = (nro_pag + 1);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><button class="page-link" onclick="'+onclick+'">'+page_number+'</button></li>';
	    }
	    if((nro_pag < (last_page-1)) && nro_pag != null){
	    	page_number = (nro_pag + 2);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><button class="page-link" onclick="'+onclick+'">'+page_number+'</button></li>';
	    }
	    if((nro_pag < (last_page-2) )&& nro_pag != null){
	    	page_number = (nro_pag + 3);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><button class="page-link" onclick="'+onclick+'">'+page_number+'</button></li>';
	    }
	    if((nro_pag < (last_page-3)) && nro_pag != null){
	    	page_number = (nro_pag + 4);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><button class="page-link" onclick="'+onclick+'">'+page_number+'</button></li>';
	    }
	    if((nro_pag < (last_page-4)) && nro_pag != null){
	    	page_number = (nro_pag + 5);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item "><button class="page-link" onclick="'+onclick+'">'+page_number+'</button></li>';
	    }
	    if(nro_pag+1 <= last_page){
	    	page_number = (nro_pag + 1);
			onclick=path.replace('__i',page_number);
	    	html +='<li class="page-item">';
	    	html +='<button class="page-link" onclick="'+onclick+'" aria-label="Next">';
	    	html +='<span aria-hidden="true">&raquo;</span>';
	    	html +='<span class="sr-only">Next</span>';
	    	html +='</button>';
	    	html +='</li>';
	    }
	    if(nro_pag < last_page){
			onclick=path.replace('__i',last_page);
	    	html +='<li class="page-item">';
	    	html +='<button class="page-link" onclick="'+onclick+'" aria-label="Next">';
	    	html +='<span aria-hidden="true">&raquo;&raquo;</span>';
	    	html +='<span class="sr-only">Last</span>';
	    	html +='</button>';
	    	html +='</li>';
	    }
	    html +='</ul>';
	    html +='</nav>';
	}
	$('#pager').html(html);
}
function searchPag(text,pag){
	if(text !== "" ){
		$('#dataTable tbody').html('<div class="justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
		
		$.ajax({
	            url: search_action,
	            dataType: "json",
	            data: {
	            	text:text,
	            	pag:pag
	            },
	            success: function(data) {
	                if (data != '404') {
	                	var path = "searchPag('"+text+"',__i)";
		                dataTable(data.clientes);
		                pagerTable(data.nro_pag,path,data.lastpage);
	                }else{
	                	$('#dataTable tbody').html('<tr><td>NO SE ENCONTRARON COINCIDENCIAS!<button type="button" class="btn btn-primary" onclick="reload_table()">Reiniciar</button></td></tr>');
	                }

	            }
	        });
	}
}
function tablePag(pag){
	var nro_pag = parseInt(pag);
	if(nro_pag == null){
		nro_pag = 1;
	}
	window.location.href = cliente_index+nro_pag;
}
function queryStringToJSON(queryString) {
  if(queryString.indexOf('?') > -1){
    queryString = queryString.split('?')[1];
  }
  var pairs = queryString.split('&');
  var result = {};
  pairs.forEach(function(pair) {
    pair = pair.split('=');
    result[pair[0]] = decodeURIComponent(pair[1] || '');
  });
  return result;
}
$(".modal_alert").on("hidden.bs.modal", function(){
    $(".modal-title").html("");
    $(".modal-body").html("");
    $(".modal-footer").html("");
});