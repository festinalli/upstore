@extends('admin.layout')

@section('css')
<style>
	#data1 input{
		border: 1px solid #E3E3E3;
		border-radius: 30px;
		color: #2c2c2c;
		line-height: normal;
		padding: 5px;
	}
	#data1 button{
		border-width: 2px;
		font-weight: 400;
		font-size: 0.8571em;
		line-height: 1.35em;
		border: none;
		margin: 10px 1px;
		border-radius: 0.1875rem;
		padding: 11px 22px;
		cursor: pointer;
		background-color: #888;
		color: #FFFFFF;
	}
</style>
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
                        <select id="selecionacliente" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o cliente" data-size="7">
                          @foreach($clientes as $cliente)
                              <option value="{{ $cliente }}">{{ $cliente }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input id="tagselecionacliente" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                      </div>
                    </div>
                  </div>
                  <!-- @if(\Auth::user()->tipo == 'ADMIN') -->
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select id="selecionaTecnico" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o responsável" data-size="7">
                          @foreach($tecnicos as $tecnico)
                              <option value="{{ $tecnico }}">{{ $tecnico }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <input id="tagselecionaTecnico" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                        </div>
                      </div>
                      
                    </div>
                    
                  </div>
                 <!--  @endif -->
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select id="selecionaStatusTipo2" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o status" data-size="7">
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
                <div class="row" style="margin-top: 10px;">
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
                
              </div>
				<div class="card-body">
					<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>OS</th>
								<th>Cliente</th>
								<th>Responsável</th>
                                <th>Data Recebimento</th>
                                <th>Data Entrega</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@foreach($manutencoes as $m)
  							<tr style="cursor: pointer;" onclick="location.href='{{ route('admin.manutencao',['id'=>$m->id]) }}'">
  								<td>M-{{ $m->id }}</td>
  								<td>{{ $m->cliente ? $m->cliente->nome : null }}</td>
                  <td>{{ $m->tecnico ? $m->tecnico->nome : 'Sem responsável' }}</td>
                  <td>{{date('d/m/Y',strtotime($m->updated_at))}}</td>
                  <td>{{ $m->data_entrega ? date('d/m/Y',strtotime($m->data_entrega)) : '-' }}</td>
  								<td>{{$m->status}}</td>
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

<script>
$(document).ready(function() {
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
	var table = $('#datatable').DataTable({
		"pagingType": "full_numbers",
		"lengthMenu": [
		[10, 25, 50, -1],
		[10, 25, 50, "All"]
		],
        "order": [[ 3, "desc" ]],
		responsive: true,
		"language": {
	        "url": "{{asset('js/pt-br.json')}}"
	    },
	});
});
</script>
<script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection