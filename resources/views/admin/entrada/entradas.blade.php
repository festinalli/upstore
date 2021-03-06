@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Aparelhos como entrada'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
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
                  @if(\Auth::user()->tipo == 'ADMIN')
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
                  @endif
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select id="selecionaStatusTipo5" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o status" data-size="7">
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
                      <th>Data de recebimento</th>
                      <th>Data de entrega</th>
                      <th>Pré proposta</th>
                      <th>Tipo</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($servicos as $s)
                      <tr style="cursor: pointer;" onclick="location.href='{{ route('admin.entrada',['id'=>$s->id]) }}'">
                        <td>E-{{ $s->id }}</td>
                        <td>{{ $s->cliente ? $s->cliente->nome : '-'}}</td>
                        <td>{{ $s->tecnico ? $s->tecnico->nome : 'Sem responsável' }}</td>
                        <td>{{date('d/m/Y',strtotime($s->updated_at))}}</td>
                        <td>{{ $s->data_entrega ? date('d/m/Y',strtotime($s->data_entrega)) : '-' }}</td>
                        <td>R$ {{number_format($s->valor/100,2,',','.')}}</td>
                        <td>{{$s->metodo}}</td>
                        <td>{{$s->status}}</td>
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
      $('#datatable').DataTable({
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

      var table = $('#datatable').DataTable();

      
    });
  </script>
  <script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection