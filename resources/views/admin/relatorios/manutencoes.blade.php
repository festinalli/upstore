@extends('admin.layout')

@section('css')

@endsection

@section('main')
<div class="main-panel">
	<!-- Navbar -->
	@include('admin.navbar', ['title' => 'Manutenções'])
	<!-- End Navbar -->
	<div class="panel-header"></div>
	<div class="content">
        <div class="row">
		<div class="col-md-12">
            <div class="card">
			<div class="card-header">

                <h4 class="card-title">Filtros </h4>
                @if(session('success'))
                    <div class="alert alert-success">
                        <b>{{ session('success') }}</b>
                    </div>
                @endif

                @if(session('danger'))
                    <div class="alert alert-danger">
                        <b>{{ session('danger') }}</b>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <br>
                <div class="row"> 
					<div class="col-md-2">
	                    <div class="row">
	                      <div class="col-lg-12 col-md-12 col-sm-12">
	                        <select id="selecionamarcas" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione a Marca" data-size="7">
	                        	@foreach($marcas as $marca)
		                              <option value="{{ $marca }}">{{ $marca }}</option>
		                        @endforeach
	                        </select>
	                      </div>
	                      
	                    </div>
	                    <div class="row">
	                      <div class="col-md-12">
	                        <input id="tagselecionamarcas" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
	                      </div>
	                    </div>
	                  </div>
	                  <div class="col-md-2">
	                    <div class="row">
	                      <div class="col-lg-12 col-md-12 col-sm-12">
	                        <select id="selecionaStatusTipo4" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione a Etapa" data-size="7">
	                        	@foreach($status as $statu)
		                              <option value="{{ $statu }}">{{ $statu }}</option>
		                        @endforeach
	                        </select>
	                      </div>
	                      
	                    </div>
	                    <div class="row">
	                      <div class="col-md-12">
	                        <input id="tagselecionaStatus" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
	                      </div>
	                    </div>
	                   
	                  </div>
			</div>
			<div class="row">
					<div class="col-md-2">
	                      <div class="row">
	                        <div class="col-lg-12 col-md-12 col-sm-12">
	                          <label>De:</label>
	                            <div class="form-group">
	                            <input id='dataini' type="date" class="form-control date-range-filter" data-date-format="dd-mm-yyyy" >
	                          </div>
	                        </div>
	                        
	                      </div>
	                    </div>
	  
	                    <div class="col-md-2">
	                      <div class="row">
	                        <div class="col-lg-12 col-md-12 col-sm-12">
	                          <label>Até:</label>
	                            <div class="form-group">
	                            <input id='datafim'  type="date" class="form-control date-range-filter" data-date-format="dd-mm-yyyy" >
	                          </div>
	                        </div>
	                        
	                      </div>
	                    </div>
			</div>
			<div class="card-body">
                <div class="toolbar">
                	<!--        Here you can write extra buttons/actions for the toolbar              -->
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Marca/Modelo</th>
							<th>Técnico</th>
							<th>Etapa</th>
							<th>Prazo</th>
							<th>Entregue</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach($manutencoes as $m)
						<tr>
							<td>{{$m->aparelho->capacidade->modelo->marca->nome}} - {{$m->aparelho->capacidade->modelo->nome}}</td>
							@if($m->tecnico_id) 
								<td>{{$m->tecnico->nome}}</td>
							@else 
								<td>-</td>
							@endif
							<td>{{$m->status}}</td>
							<td>
	                            @if($m->data_entrega)
	                              {{ date('d/m/Y',strtotime($m->data_entrega)) }}
	                            @else
	                              -
	                            @endif
	                        </td>
	                        
							<td>dd/mm/YYYY</td>
							<td>Finalizada / Atrasada </td>
						</tr>
						@endforeach
					</tbody>
                </table>
			</div>
			<!-- end content-->
			</div>
            <!--  end card  -->
		</div>
		<!-- end col-md-12 -->
        </div>
        <!-- end row -->
	</div>
</div>
@endsection

@section('js')
<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
<script src="{{ asset('admin/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
<script>
$(document).ready(function() {
	var table = $('#datatable').DataTable({
		"pagingType": "full_numbers",
		"lengthMenu": [
			[10, 25, 50, -1],
			[10, 25, 50, "All"]
		],
		responsive: true,
		"language": {
	        "url": "{{asset('js/pt-br.json')}}"
	    },
	});
});
</script>
<script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection