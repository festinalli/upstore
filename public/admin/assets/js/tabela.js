
// Re-draw the table when the a date range filter changes
$('.date-range-filter').change( function() {
	let table = $('#datatable').DataTable();
	// Extend dataTables search
	$.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        let min  = $('#dataini').val();
	        let max  = $('#datafim').val();

	        if(min !='' && max !=''){
	        	let createdAt = data[3] || 0; // Our date column in the table
	        	createdAt = createdAt.split('/')
	        	let ano = createdAt[2].split(' ');

	        	if(ano.length>1){
	        		createdAt = ano[0]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}else{
	        		createdAt = createdAt[2]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}

		        if  ( createdAt >= min && createdAt <= max ){
		            return true;
		        }
		        return false;

	        }
	        return true;
	    }
	);
	table.draw();
} );


$('#selecionacliente').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionacliente").val();

	let regex = arr.join("|");
	$("#tagselecionacliente").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionacliente').tagsinput('add', value);
	});

	table.column(1).search(regex, true, false).draw();

} );

$('#selecionaTecnico').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaTecnico").val();

	let regex = arr.join("|");
	$("#tagselecionaTecnico").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaTecnico').tagsinput('add', value);
	});

	table.column(2).search(regex, true, false).draw();

} );

$('#selecionaProdutos').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaProdutos").val();

	let regex = arr.join("|");
	$("#tagselecionaProdutos").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaProdutos').tagsinput('add', value);
	});

	table.column(4).search(regex, true, false).draw();

} );


// Re-draw the table when the a date range filter changes
$('#selecionaStatus').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaStatus").val();

	let regex = arr.join("|");

	$("#tagselecionaStatus").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaStatus').tagsinput('add', value);
	});

	table.column(6).search(regex, true, false).draw();

} );

$('#selecionamarcas').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionamarcas").val();

	let regex = arr.join("|");

	$("#tagselecionamarcas").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionamarcas').tagsinput('add', value);
	});

	table.column(0).search(regex, true, false).draw();

} );


$('#selecionamarcas2').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionamarcas2").val();

	let regex = arr.join("|");

	$("#tagselecionamarcas").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionamarcas').tagsinput('add', value);
	});

	table.column(1).search(regex, true, false).draw();

} );

$('#problemasModelo').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#problemasModelo").val();

	let regex = arr.join("|");

	$("#tagProblemasModelo").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagProblemasModelo').tagsinput('add', value);
	});

	table.column(2).search(regex, true, false).draw();

} );


$('#selecionamodelo').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionamodelo").val();

	let regex = arr.join("|");

	$("#tagselecionaModelo").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaModelo').tagsinput('add', value);
	});

	table.column(2).search(regex, true, false).draw();

} );

$('#selecionaTipos').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaTipos").val();

	let regex = arr.join("|");

	$("#tagselecionaTipos").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaTipos').tagsinput('add', value);
	});

	table.column(4).search(regex, true, false).draw();

} );




/* Filtros para colunas diferentes */


$('#selecionamarcas1').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionamarcas1").val();

	let regex = arr.join("|");

	$("#tagselecionamarcas").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionamarcas').tagsinput('add', value);
	});

	table.column(1).search(regex, true, false).draw();

} );

// Re-draw the table when the a date range filter changes
$('#selecionaStatusTipo2').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaStatusTipo2").val();

	let regex = arr.join("|");

	$("#tagselecionaStatus").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaStatus').tagsinput('add', value);
	});

	table.column(5).search(regex, true, false).draw();

} );


$('#selecionaStatusTipo3').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaStatusTipo3").val();

	let regex = arr.join("|");

	$("#tagselecionaStatus").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaStatus').tagsinput('add', value);
	});

	table.column(3).search(regex, true, false).draw();

} );


$('#selecionaStatusTipo4').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaStatusTipo4").val();

	let regex = arr.join("|");

	$("#tagselecionaStatus").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaStatus').tagsinput('add', value);
	});

	table.column(2).search(regex, true, false).draw();

} );

$('#selecionaStatusTipo5').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaStatusTipo5").val();

	let regex = arr.join("|");

	$("#tagselecionaStatus").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaStatus').tagsinput('add', value);
	});

	table.column(7).search(regex, true, false).draw();

} );


$('#selecionaclienteTipo2').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaclienteTipo2").val();

	let regex = arr.join("|");
	$("#tagselecionacliente").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionacliente').tagsinput('add', value);
	});

	table.column(0).search(regex, true, false).draw();

} );

$('#selecionaTecnicoTipo2').change( function() {
	let table = $('#datatable').DataTable();
	let arr = $("#selecionaTecnicoTipo2").val();

	let regex = arr.join("|");
	$("#tagselecionaTecnico").tagsinput('removeAll');
	$.each(arr, function(index, value) {
		$('#tagselecionaTecnico').tagsinput('add', value);
	});

	table.column(1).search(regex, true, false).draw();

} );

// Re-draw the table when the a date range filter changes
$('.date-range-filter-tipo2').change( function() {
	let table = $('#datatable').DataTable();
	// Extend dataTables search
	$.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        let min  = $('#dataini').val();
	        let max  = $('#datafim').val();

	        if(min !='' && max !=''){
	        	let createdAt = data[2] || 0; // Our date column in the table
	        	createdAt = createdAt.split('/')
	        	let ano = createdAt[2].split(' ');

	        	if(ano.length>1){
	        		createdAt = ano[0]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}else{
	        		createdAt = createdAt[2]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}

		        if  ( createdAt >= min && createdAt <= max ){
		            return true;
		        }
		        return false;

	        }
	        return true;
	    }
	);
	table.draw();
} );

$('.date-range-filter-tipo3').change( function() {
	let table = $('#datatable').DataTable();
	// Extend dataTables search
	$.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        let min  = $('#dataini').val();
	        let max  = $('#datafim').val();

	        if(min !='' && max !=''){
	        	let createdAt = data[4] || 0; // Our date column in the table
	        	createdAt = createdAt.split('/')
	        	let ano = createdAt[2].split(' ');

	        	if(ano.length>1){
	        		createdAt = ano[0]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}else{
	        		createdAt = createdAt[2]+'-'+createdAt[1]+'-'+createdAt[0];
	        	}

		        if  ( createdAt >= min && createdAt <= max ){
		            return true;
		        }
		        return false;

	        }
	        return true;
	    }
	);
	table.draw();
} );