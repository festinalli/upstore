@extends('admin.layout')

@section('css')

@endsection

@section('main')
<div class="main-panel">
	<!-- Navbar -->

    @include('admin.navbar', ['title' => 'Principais problemas'])
      
    <!-- End Navbar -->
	<div class="panel-header">
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
			<div class="card">
				<div class="card-header">
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


					<div class="col-md-3">
    					<div class="row">
    						<div class="col-lg-12 col-md-12 col-sm-12">
        						<select class="selectpicker" id="selecionamarcas2" data-style="btn btn-default btn-round" multiple title="Selecione a Marca" data-size="100">
        							@foreach ($marcas as $marca) 
        								<option value="{{ $marca->nome }}">{{ $marca->nome }}</option>
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

					<div class="col-md-3">
    					<div class="row">
    						<div class="col-lg-12 col-md-12 col-sm-12">
        						<select  class="selectpicker" multiple id="problemaModelo" data-style="btn btn-default btn-round" title="Selecione o aparelho" data-size="100">
        							@foreach ($marcas as $marca)
                                        @foreach($marca->modelos as $modelo) 
                                            <option value="{{ $modelo->nome }}"> {{ $marca->nome }} - {{ $modelo->nome }}</option>
                                        @endforeach
        							@endforeach
        						</select>
    						</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input id="tagProblemasModelo" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                                </div>
                            </div>
    					</div>
					</div>
					
				</div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-2">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        </div>
                        
                      </div>
                    </div>
  
                    <div class="col-md-2">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        </div>
                        
                      </div>
                    </div>
                </div>
				</div>
				<div class="card-body">
    				<!-- <div class="toolbar">
                        <a class="btn btn-warning" style="color: white;"><b>Exportar</b></a>                            
                    </div> -->
                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Problema</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Ocorrências</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($problemas as $problema)
                            <tr>
                                <td>{{ $problema[0]->problema->nome }}</td>
                                <td>{{ $problema[0]->problema->modelo->marca->nome }}</td>                               
                                <td>{{ $problema[0]->problema->modelo->nome }}</td>                               
                                <td>{{ count($problema) }}</td>                               
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
      $('#datatable').DataTable({
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

      var table = $('#datatable').DataTable();

      
    });
    </script>
  <script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection
